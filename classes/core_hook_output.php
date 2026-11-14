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
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . "/../lib.php");

use core\hook\output\before_footer_html_generation;
use dml_exception;
use local_kopere_bi\analytics\period_activity;

/**
 * Class core_hook_output
 */
class core_hook_output {

    /**
     * Function before_footer_html_generation
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook) {
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

        if (!feature::online_tracking_enabled()) {
            $PAGE->requires->js_call_amd("local_kopere_bi/mod_koperebi", "init");
            return;
        }

        $moduleid = 0;
        if ($PAGE->cm && $PAGE->cm->id) {
            $moduleid = $PAGE->cm->id;
        }

        $key = $COURSE->id . ">" . $moduleid;

        if (!isset($USER->koperebionline_id) || !is_array($USER->koperebionline_id)) {
            $USER->koperebionline_id = [];
        }
        if (!isset($USER->koperebionline_time) || !is_array($USER->koperebionline_time)) {
            $USER->koperebionline_time = [];
        }

        if (isset($USER->koperebionline_id[$key]) &&
                time() - (int)($USER->koperebionline_time[$key] ?? 0) > 100) {
            unset($USER->koperebionline_id[$key], $USER->koperebionline_time[$key]);
        }

        if (!isset($USER->koperebionline_id[$key])) {
            $lastip = local_kopere_bi_getremoteaddr();

            // Device detection and IP geolocation are deferred to the first AJAX heartbeat.
            // Neither the DeviceDetector regexes nor external services may block page rendering.
            $koperebionline = (object)[
                "userid" => $USER->id,
                "courseid" => $COURSE->id,
                "moduleid" => $moduleid,
                "seconds" => 1,
                "currenttime" => time(),


                "lastip" => $lastip,
            ];
            try {
                $koperebionlineid = $DB->insert_record("local_kopere_bi_online", $koperebionline);
                $USER->koperebionline_id[$key] = $koperebionlineid;
                $USER->koperebionline_time[$key] = time();
                period_activity::record_visit((int)$USER->id, (int)$COURSE->id, (int)$koperebionline->currenttime);
            } catch (dml_exception $e) {
                $e->getMessage();
            }
        }

        if (isset($USER->koperebionline_id[$key])) {
            $PAGE->requires->js_call_amd("local_kopere_bi/online", "init", [$USER->koperebionline_id[$key], $key]);
        }

        $PAGE->requires->js_call_amd("local_kopere_bi/mod_koperebi", "init");
    }
}
