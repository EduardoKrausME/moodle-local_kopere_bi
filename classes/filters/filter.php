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
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\vo\external_report;
use local_kopere_dashboard\util\url_util;

/**
 * Class filter
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter {
    /**
     * Function create_filter_page
     *
     * @param $pageid
     * @return string
     * @throws Exception
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
     * @return string
     * @throws Exception
     */
    public static function create_filter($commandsql) {
        global $DB;

        $return = "";

        $elementid = optional_param("item_id", false, PARAM_INT);
        $pageid = optional_param("page_id", false, PARAM_INT);
        $paramsurl = ["item_id" => $elementid, "page_id" => $pageid];

        // Checks if the string $commandsql starts with "\Namespace\Classe" and that a class will be called in PHP.
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

                    $return .= self::create_filter($course, $paramsurl);
                }
                if ($parameters->isfilteruser) {
                    $user = $DB->get_record("user", ["id" => $params["userid"]]);

                    $paramsurl["userid"] = "{id}";
                    if (isset($params["courseid"])) {
                        $paramsurl["courseid"] = $params["courseid"];
                    }

                    $return .= self::create_filter($user, $paramsurl);
                }

                $return .= "</div>";
            }

            return $return;
        }

        $comand = sql_util::prepare_sql($commandsql);

        $filters = "";
        $filters .= user::filter($paramsurl, $comand);
        $filters .= course::filter($paramsurl, $comand);
        // Cohort.
        // userprofile.
        // date.

        if (isset($filters[3])) {
            $return .= "<div id='chart-filter' class='d-flex' style='gap: 12px;'>{$filters}</div>";
        }

        return $return;
    }
}
