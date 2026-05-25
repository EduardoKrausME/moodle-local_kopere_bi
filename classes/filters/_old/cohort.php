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

namespace local_kopere_bi\filters;

use Exception;
use local_kopere_dashboard\util\url_util;

/**
 * Class cohort
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort {
    /**
     * filter
     *
     * @param $paramsurl
     * @param $comand
     * @return bool|string
     * @throws Exception
     */
    public static function filter(&$paramsurl, $comand) {
        global $DB, $CFG;
        if (isset($comand->params["cohortid"]) || isset($comand->params["count_1_cohortid"])) {
            $cohort = $DB->get_record("cohort", ["id" => $comand->params["cohortid"]]);

            $paramsurl["cohortid"] = "{id}";
            if (isset($comand->params["courseid"])) {
                $paramsurl["courseid"] = $comand->params["courseid"];
            }
            $CFG->debugdeveloper = false;

            $classname = optional_param("classname", false, PARAM_TEXT);
            $method = optional_param("method", false, PARAM_TEXT);

            $data = [
                "cohort_fullname" => fullname($cohort),
                "url-ajax"  => url_util::makeurl("cohorts", "load_all_cohorts", [], "view-ajax"),
                "url-click"  => url_util::makeurl($classname, $method, $paramsurl),
            ];

            global $OUTPUT, $PAGE;
            $PAGE->requires->js_call_amd("local_kopere_bi/filter_cohort", "init");
            return $OUTPUT->render_from_template('local_kopere_bi/filter-cohort', $data);
        }

        return "";
    }
}
