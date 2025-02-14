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

/**
 * Class injector
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . "/../lib.php");

use dml_exception;
use local_kopere_bi\local\analise\access_analyze;

/**
 * Class core_hook_output
 */
class core_hook_output {

    /**
     * Function before_footer_html_generation
     */
    public static function before_footer_html_generation() {
        global $USER, $DB, $COURSE, $PAGE;

        if (!isloggedin()) {
            return;
        }

        if (isguestuser()) {
            return;
        }

        if (!$PAGE->get_popup_notification_allowed()) {
            return;
        }

        $moduleid = 0;
        if ($PAGE->cm && $PAGE->cm->id) {
            $moduleid = $PAGE->cm->id;
        }

        $key = $COURSE->id . ">" . $moduleid;

        if (isset($USER->koperebionline_id)) {
            $USER->koperebionline_id = [];
        }

        if (isset($USER->koperebionline_id[$key])) {
            if ($USER->koperebionline_time[$key] - time() > 100) {
                unset($USER->koperebionline_id);
                unset($USER->koperebionline_time);
            }
        }

        if (!isset($USER->koperebionline_id[$key])) {
            $lastip = local_kopere_bi_getremoteaddr();

            $dataagent = access_analyze::agent();
            $dataip = local_kopere_bi_iplookup_find_location($lastip);

            $koperebionline = (object)[
                "userid" => $USER->id,
                "courseid" => $COURSE->id,
                "moduleid" => $moduleid,
                "seconds" => 0,
                "currenttime" => time(),

                "client_type" => $dataagent->client_type,
                "client_name" => $dataagent->client_name,
                "client_version" => $dataagent->client_version,
                "os_name" => $dataagent->os_name,
                "os_version" => $dataagent->os_version,

                "lastip" => $lastip,
                "city_name" => $dataip->city,
                "country_name" => $dataip->country,
                "country_code" => isset($dataip->country_code) ? $dataip->country_code : $dataip->country,
                "latitude" => $dataip->latitude,
                "longitude" => $dataip->longitude,
            ];
            try {
                $koperebionlineid = $DB->insert_record("local_kopere_bi_online", $koperebionline);
                $USER->koperebionline_id[$key] = $koperebionlineid;
                $USER->koperebionline_time[$key] = time();
            } catch (dml_exception $e) {
                $e->getMessage();
            }
        }

        if (isset($USER->koperebionline_id[$key])) {
            $PAGE->requires->js_call_amd("local_kopere_bi/online", "init", [$USER->koperebionline_id[$key], $key]);
        }
    }
}
