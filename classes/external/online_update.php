<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_kopere_bi\external;

use context_user;
use Exception;
use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_function_parameters;
use local_kopere_bi\analysis\access;
use local_kopere_bi\analytics\period_activity;
use local_kopere_bi\feature;
use local_kopere_bi\ip_location;
use local_kopere_bi\vo\local_kopere_bi_online;

defined('MOODLE_INTERNAL') || die;

/**
 * Contabiliza o tempo gasto de um usuário para o dashboard
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class online_update extends external_api {
    /**
     * Parâmetros recebidos pelo webservice
     *
     * @return external_function_parameters
     */
    public static function api_parameters() {
        return new external_function_parameters([
            "online_id" => new external_value(PARAM_INT, 'The online id'),
            "cache_key" => new external_value(PARAM_TEXT, 'The cache id'),
            "seconds" => new external_value(PARAM_INT, 'The time spent by the user on the page'),
            "resolve_ip" => new external_value(PARAM_BOOL, 'Resolve the IP location asynchronously', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Identificador do retorno do webservice
     *
     * @return external_single_structure
     */
    public static function api_returns() {
        return new external_single_structure([
            "success" => new external_value(PARAM_BOOL, "Operation Success", VALUE_OPTIONAL),
        ]);
    }

    /**
     * Define se o endpoint é chamado via AJAX
     *
     * @return bool
     */
    public static function api_is_allowed_from_ajax() {
        return true;
    }

    /**
     * API para contabilizar o tempo gasto na plataforma pelos usuários
     *
     * @param $onlineid
     * @param $cachekey
     * @param $seconds
     * @param $resolveip
     * @return array
     * @throws Exception
     */
    public static function api($onlineid, $cachekey, $seconds, $resolveip = false) {
        global $DB, $USER;

        $params = self::validate_parameters(self::api_parameters(), [
            "online_id" => $onlineid,
            "cache_key" => $cachekey,
            "seconds" => $seconds,
            "resolve_ip" => $resolveip,
        ]);

        // Tracking is available to students, even without the BI report-view capability.
        // Authentication, user context, and ownership of the online record are mandatory.
        require_login();
        if (isguestuser()) {
            return ["success" => false];
        }
        self::validate_context(context_user::instance($USER->id));

        if (!feature::online_tracking_enabled()) {
            return ["success" => true];
        }

        /** @var local_kopere_bi_online $online */
        $online = $DB->get_record("local_kopere_bi_online", [
            "id" => $params["online_id"],
            "userid" => $USER->id,
        ]);
        if (!$online) {
            return ["success" => true];
        }

        $expectedkey = $online->courseid . ">" . $online->moduleid;
        if ($params["cache_key"] === $expectedkey &&
                isset($USER->koperebionline_id[$expectedkey]) &&
                (int)$USER->koperebionline_id[$expectedkey] === (int)$online->id &&
                isset($USER->koperebionline_time[$expectedkey])) {
            $USER->koperebionline_time[$expectedkey] = time();
        }

        // Persist focused time before the more expensive asynchronous enrichment.
        $trackedseconds = max(0, min(7200, (int)$params["seconds"]));
        $online->currenttime = time();
        $online->seconds += $trackedseconds;
        $DB->update_record("local_kopere_bi_online", $online);
        period_activity::record_seconds((int)$online->userid, (int)$online->courseid,
            (int)$online->currenttime, $trackedseconds);

        if ($params["resolve_ip"]) {
            // The first online.js AJAX call fills browser/OS fields only once per visit.
            if (empty($online->client_type) && empty($online->os_name)) {
                try {
                    $agent = access::agent();
                    $DB->update_record("local_kopere_bi_online", (object)[
                        "id" => $online->id,
                        "client_type" => $agent->client_type,
                        "client_name" => $agent->client_name,
                        "client_version" => $agent->client_version,
                        "os_name" => $agent->os_name,
                        "os_version" => $agent->os_version,
                    ]);
                } catch (\Throwable $e) {
                    debugging("Kopere BI could not identify the browser and operating system.", DEBUG_DEVELOPER);
                }
            }

            // IP geolocation was already deferred to this same AJAX call.
            ip_location::resolve_online_record((int)$online->id);
        }

        return ["success" => true];
    }
}
