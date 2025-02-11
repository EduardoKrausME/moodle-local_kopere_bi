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

use local_kopere_dashboard\util\message;

/**
 * Class sql_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sql_util {

    /**
     * Function prepare_sql
     *
     * @param $sql
     *
     * @return object
     * @throws \Exception
     */
    public static function prepare_sql($sql) {
        global $CFG, $USER, $COURSE;

        $isfilteruser = strpos($sql, ":userid");
        $isfiltercourse = strpos($sql, ":courseid");

        $params = [];
        if ($isfilteruser) {
            $userid = optional_param("userid", 0, PARAM_INT);
            if (!$userid) {
                if (isset($USER->filter_userid)) {
                    $userid = $USER->filter_userid;
                } else {
                    $userid = $USER->id;
                }
            }
            $USER->filter_userid = $userid;

            $count = mb_substr_count($sql, ":userid");
            $params["userid"] = $userid;
            if ($count == 1) {
                $params["userid"] = $userid;
            } else {
                $i = 0;
                $sql = preg_replace_callback(
                    '/:userid/',
                    function () use (&$i) {
                        $i++;
                        return ":count_{$i}_userid";
                    },
                    $sql,
                    -1, // Substituir todas as ocorrências.
                    $count // Contador de substituições.
                );

                for ($i = 1; $i <= $count; $i++) {
                    $params["count_{$i}_userid"] = $userid;
                }
            }
        }

        if ($isfiltercourse) {
            $courseid = optional_param("courseid", 0, PARAM_INT);
            if (!$courseid) {
                if (isset($USER->filter_courseid)) {
                    $courseid = $USER->filter_courseid;
                } else {
                    $courseid = $COURSE->id;
                }
            }
            $USER->filter_courseid = $courseid;

            $count = mb_substr_count($sql, ":courseid");
            $params["courseid"] = $courseid;
            if ($count == 1) {
                $params["courseid"] = $courseid;
            } else {
                $i = 0;
                $sql = preg_replace_callback(
                    '/:courseid/',
                    function () use (&$i) {
                        $i++;
                        return ":count_{$i}_courseid";
                    },
                    $sql,
                    -1, // Substituir todas as ocorrências.
                    $count // Contador de substituições.
                );

                for ($i = 1; $i <= $count; $i++) {
                    $params["count_{$i}_courseid"] = $courseid;
                }
            }
        }

        $sql = preg_replace('/\{([a-z][a-z0-9_]*)\}/', "{$CFG->prefix}$1", $sql);

        if ($CFG->prefix != "mdl_") {
            $sql = preg_replace('/mdl_(\w+)/', "{$CFG->prefix}\$1", $sql);
        }

        $sql = preg_replace('/UNIX_TIMESTAMP[\s+]?\([\s+]?\)/', time(), $sql);
        $sql = preg_replace('/AS[\s+]?\'(.*?)\'/', 'AS "$1"', $sql);

        return (object)["sql" => $sql, "params" => $params];
    }

    /**
     * Function chaves_replace
     *
     * @return string
     * @throws \Exception
     */
    public static function chaves_replace() {
        global $CFG;

        if ($CFG->prefix != "mdl_") {
            return
                message::info(
                    get_string("sql_replace_keys", "local_kopere_bi") .
                    get_string("sql_replace_keys_mdl", "local_kopere_bi", $CFG->prefix)
                ) .
                message::warning(
                    get_string("sql_read_only", "local_kopere_bi")
                );
        } else {
            return message::info(
                    get_string("sql_replace_keys", "local_kopere_bi")
                ) .
                message::warning(
                    get_string("sql_read_only", "local_kopere_bi")
                );
        }
    }
}
