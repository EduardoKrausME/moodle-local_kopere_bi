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

namespace bifilters_course;

use Exception;
use local_kopere_bi\filters\i_filter_provider;
use local_kopere_dashboard\util\url_util;

/**
 * Class provider course
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements i_filter_provider {
    /**
     * filter
     *
     * @param $paramsurl
     * @param $comand
     * @return bool|string
     * @throws Exception
     */
    public static function filter(&$paramsurl, $comand) {
        global $DB;
        if (isset($comand->params["courseid"]) || isset($comand->params["count_1_courseid"])) {
            $course = $DB->get_record("course", ["id" => $comand->params["courseid"]]);

            $paramsurl["courseid"] = "{id}";
            if (isset($comand->params["userid"])) {
                $paramsurl["userid"] = $comand->params["userid"];
            }

            $classname = optional_param("classname", false, PARAM_TEXT);
            $method = optional_param("method", false, PARAM_TEXT);

            $data = [
                "popupid" => uniqid(),
                "btn-title" => get_string("course"),
                "btn-subtitle" => $course->fullname,

                "cols" => [
                    [
                        "id" => "id",
                        "name" => "#",
                        "style" => "width:20px",
                    ],

                    [
                        "id" => "fullname",
                        "name" => get_string("courses_name", "local_kopere_dashboard"),
                    ],
                    [
                        "id" => "shortname",
                        "name" => get_string("courses_shortname", "local_kopere_dashboard"),
                    ],
                    [
                        "id" => "visible",
                        "name" => get_string("visible", "local_kopere_dashboard"),
                    ],
                    [
                        "id" => "visible",
                        "name" => get_string("courses_enrol", "local_kopere_dashboard"),
                        "style" => "width:50px;white-space:nowrap;",
                    ],
                ],
                "columns" => [
                    (object)["data" => "id"],
                    (object)["data" => "fullname"],
                    (object)["data" => "shortname"],
                    (object)["data" => "visible"],
                    (object)["data" => "enrolments"],
                ],
                "columnDefs" => [
                    (object)[
                        "render" => "numberRenderer",
                        "targets" => 0,
                    ],
                    (object)[
                        "render" => "visibleRenderer",
                        "targets" => 3,
                    ],
                    (object)[
                        "render" => "numberRenderer",
                        "targets" => 4,
                    ],
                ],
                "table-title" => get_string("reports_selectcourse", "local_kopere_bi"),
                "url-ajax"  => url_util::makeurl("courses", "load_all_courses", [], "view-ajax"),
                "url-click"  => url_util::makeurl($classname, $method, $paramsurl),
            ];

            global $OUTPUT, $PAGE;
            $PAGE->requires->js_call_amd("local_kopere_bi/filter", "init",
                [$data["popupid"], $data["columns"], $data["columnDefs"]]);
            return $OUTPUT->render_from_template("local_kopere_bi/filter", $data);
        }

        return "";
    }

    /**
     * Get key for show filters message
     *
     * @return string
     * @throws Exception
     */
    public static function get_key() {
        return "courseid";
    }
}
