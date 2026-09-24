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

use Exception;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use local_kopere_bi\feature;
use local_kopere_bi\ip_location;
use local_kopere_bi\vo\local_kopere_bi_online;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("{$CFG->libdir}/externallib.php");

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

        $context = \context_user::instance($USER->id);
        require_capability("local/kopere_bi:view", $context);
        self::validate_context($context);

        if (!feature::online_tracking_enabled()) {
            return ["success" => true];
        }

        if (isset($USER->koperebionline_time[$params["cache_key"]])) {
            $USER->koperebionline_time[$params["cache_key"]] = time();
        }

        /** @var local_kopere_bi_online $online */
        $online = $DB->get_record("local_kopere_bi_online", ["id" => $params["online_id"]]);
        if ($online && (int)$online->userid === (int)$USER->id) {
            $online->currenttime = time();
            $online->seconds = $online->seconds + $params["seconds"];
            $DB->update_record("local_kopere_bi_online", $online);

            // This may wait for the external API, but it runs in AJAX after the page has already rendered.
            if ($params["resolve_ip"]) {
                ip_location::resolve_online_record((int)$online->id);
            }
        }

        return ["success" => true];
    }
}
