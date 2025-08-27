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
 * Class user
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {
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
        if (isset($comand->params["userid"]) || isset($comand->params["count_1_userid"])) {
            $user = $DB->get_record("user", ["id" => $comand->params["userid"]]);

            $paramsurl["userid"] = "{id}";
            if (isset($comand->params["courseid"])) {
                $paramsurl["courseid"] = $comand->params["courseid"];
            }
            $CFG->debugdeveloper = false;

            $classname = optional_param("classname", false, PARAM_TEXT);
            $method = optional_param("method", false, PARAM_TEXT);

            $data = [
                "popupid" => uniqid(),
                "btn-title" => get_string("defaultcoursestudent"),
                "btn-subtitle" => fullname($user),

                "cols" => [
                    [
                        "id" => "id",
                        "name" => "#",
                        "style" => "width:20px",
                    ],
                    [
                        "id" => "fullname",
                        "name" => get_string("user_table_fullname", "local_kopere_dashboard"),
                    ],
                    [
                        "id" => "username",
                        "name" => get_string("user_table_username", "local_kopere_dashboard"),
                    ],
                    [
                        "id" => "email",
                        "name" => get_string("user_table_email", "local_kopere_dashboard"),
                    ],
                ],
                "columns" => [
                    (object)["data" => "id"],
                    (object)["data" => "fullname"],
                    (object)["data" => "username"],
                    (object)["data" => "email"],
                ],

                "table-title" => get_string("reports_selectuser", "local_kopere_bi"),
                "url-ajax" => url_util::makeurl("users", "load_all_users", [], "view-ajax"),
                "url-click" => url_util::makeurl($classname, $method, $paramsurl),
            ];

            global $OUTPUT, $PAGE;
            $PAGE->requires->js_call_amd("local_kopere_bi/filter", "init", [$data["popupid"], $data["columns"]]);
            return $OUTPUT->render_from_template('local_kopere_bi/filter', $data);
        }

        return "";
    }
}
