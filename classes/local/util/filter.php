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

namespace local_kopere_bi\local\util;

use local_kopere_bi\local\vo\external_report;

/**
 * Class filter
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter {

    /**
     * Function create_filter_page
     *
     * @param $pageid
     *
     * @return string
     *
     * @throws \dml_exception
     * @throws \Exception
     */
    public static function create_filter_page($pageid) {
        global $DB;

        $sql = "
            SELECT e.id, commandsql
              FROM {local_kopere_bi_element} e
              JOIN {local_kopere_bi_block}   b ON b.id = e.block_id
             WHERE b.page_id = :page_id";
        $pages = $DB->get_records_sql($sql, ["page_id" => $pageid]);

        $commandssql = "";
        foreach ($pages as $page) {
            $commandssql .= $page->commandsql;
        }

        return self::create_filter($commandssql);
    }

    /**
     * Function create_filter
     *
     * @param $commandsql
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function create_filter($commandsql) {
        global $DB, $CFG;

        $return = "";

        $elementid = optional_param("item_id", false, PARAM_INT);
        $pageid = optional_param("page_id", false, PARAM_INT);
        $paramsurl = ["item_id" => $elementid, "page_id" => $pageid];

        if (preg_match('/^\\\\\w+\\\\\w+/', $commandsql)) {
            /** @var external_report $class */
            $class = $commandsql;
            $parameters = $class::parameters();

            if ($parameters->isfilterusercourse || $parameters->isfilteruser) {
                $return .= "<div id='chart-filter' class='d-flex' style='gap: 12px;'>";
                list($sql, $params) = sql_util::params($commandsql, $parameters->isfilteruser, $parameters->isfilterusercourse);

                if ($parameters->isfilterusercourse) {
                    $course = $DB->get_record("course", ["id" => $params["courseid"]]);

                    $paramsurl["courseid"] = "{id}";
                    if (isset($params["userid"])) {
                        $paramsurl["userid"] = $params["userid"];
                    }

                    $return .= self::create_filter_course($course, $paramsurl);
                }
                if ($parameters->isfilteruser) {
                    $user = $DB->get_record("user", ["id" => $params["userid"]]);

                    $paramsurl["userid"] = "{id}";
                    if (isset($params["courseid"])) {
                        $paramsurl["courseid"] = $params["courseid"];
                    }

                    $return .= self::create_filter_user($user, $paramsurl);
                }

                $return .= "</div>";
            }

            return $return;
        }

        $comand = sql_util::prepare_sql($commandsql);

        if (isset($comand->params["userid"]) || isset($comand->params["count_1_userid"]) ||
            isset($comand->params["courseid"]) || isset($comand->params["count_1_courseid"])) {
            $return .= "<div id='chart-filter' class='d-flex' style='gap: 12px;'>";

            if (isset($comand->params["courseid"]) || isset($comand->params["count_1_courseid"])) {
                $course = $DB->get_record("course", ["id" => $comand->params["courseid"]]);

                $paramsurl["courseid"] = "{id}";
                if (isset($comand->params["userid"])) {
                    $paramsurl["userid"] = $comand->params["userid"];
                }

                $return .= self::create_filter_course($course, $paramsurl);
            }

            if (isset($comand->params["userid"]) || isset($comand->params["count_1_userid"])) {
                $user = $DB->get_record("user", ["id" => $comand->params["userid"]]);

                $paramsurl["userid"] = "{id}";
                if (isset($comand->params["courseid"])) {
                    $paramsurl["courseid"] = $comand->params["courseid"];
                }
                $CFG->debugdeveloper = false;

                self::create_filter_user($user, $paramsurl);
            }

            $return .= "</div>";
        }

        return $return;
    }

    /**
     * Function create_filter_course
     *
     * @param $course
     * @param $paramsurl
     *
     * @return mixed
     * @throws \coding_exception
     */
    private static function create_filter_course($course, $paramsurl) {
        global $OUTPUT, $PAGE;

        $classname = optional_param("classname", false, PARAM_TEXT);
        $method = optional_param("method", false, PARAM_TEXT);

        $data = [
            "course_fullname" => $course->fullname,
            "url_ajax" => local_kopere_dashboard_makeurl("courses", "load_all_courses", [], "view-ajax"),
            "url_click" => local_kopere_dashboard_makeurl($classname, $method, $paramsurl),
        ];
        $PAGE->requires->js_call_amd("local_kopere_bi/filter_course", "init");
        return $OUTPUT->render_from_template('local_kopere_bi/filter-course', $data);
    }

    /**
     * Function create_filter_user
     *
     * @param $user
     * @param $paramsurl
     *
     * @return mixed
     * @throws \coding_exception
     */
    private static function create_filter_user($user, $paramsurl) {
        global $OUTPUT, $PAGE;

        $classname = optional_param("classname", false, PARAM_TEXT);
        $method = optional_param("method", false, PARAM_TEXT);

        $data = [
            "user_fullname" => fullname($user),
            "url_ajax" => local_kopere_dashboard_makeurl("users", "load_all_users", [], "view-ajax"),
            "url_click" => local_kopere_dashboard_makeurl($classname, $method, $paramsurl),
        ];
        $PAGE->requires->js_call_amd("local_kopere_bi/filter_user", "init");
        return $OUTPUT->render_from_template('local_kopere_bi/filter-user', $data);
    }

    /**
     * Function find_user
     *
     * @throws \Exception
     */
    public function find_user() {
        global $DB, $CFG;

        $q = optional_param("q", "", PARAM_TEXT);
        $sqlfullname = $DB->sql_fullname();

        $users = $DB->get_records_select("user",
            "{$sqlfullname} LIKE ? AND id > 1 AND confirmed = 1 AND deleted = 0 AND suspended = 0",
            ["%{$q}%"], "", "id,firstname,lastname", 0, 20);

        $CFG->debugdeveloper = false;

        $returnuser = [];
        foreach ($users as $user) {
            $returnuser[] = (object)[
                "id" => $user->id,
                "text" => fullname($user),
            ];
        }

        echo json_encode([
            "results" => $returnuser,
            "pagination" => (object)[
                "more" => false,
            ],
        ], JSON_NUMERIC_CHECK);
        die();
    }

    /**
     * Function find_course
     *
     * @throws \Exception
     */
    public function find_course() {
        global $DB;

        $q = optional_param("q", "", PARAM_TEXT);

        $courses = $DB->get_records_select("course",
            "fullname LIKE ? AND id > 1 AND visible = 1",
            ["%{$q}%"], "", "id,fullname AS text", 0, 20);

        echo json_encode([
            "results" => $courses,
            "pagination" => (object)[
                "more" => false,
            ],
        ], JSON_NUMERIC_CHECK);
        die();
    }
}
