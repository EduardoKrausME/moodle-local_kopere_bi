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
 * upgrade file
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kopere_bi\install\reports;

/**
 * Function xmldb_local_kopere_bi_upgrade
 *
 * @param $oldversion
 * @return bool
 * @throws Exception
 */
function xmldb_local_kopere_bi_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025011001) {
        $table = new xmldb_table("local_kopere_bi_cat");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table("local_kopere_bi_page");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table("local_kopere_bi_block");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table("local_kopere_bi_element");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        require_once("db-config.php");
        import_reports();

        upgrade_plugin_savepoint(true, 2025011001, "local", "kopere_bi");
    }

    if ($oldversion < 2026052521) {
        $table = new xmldb_table("local_kopere_bi_tracking");
        if (!$dbman->table_exists($table)) {
            $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field("userid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("courseid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("page", XMLDB_TYPE_CHAR, "20");
            $table->add_field("param", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("timespend", XMLDB_TYPE_INTEGER, "20");
            $table->add_field("visits", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("firstaccess", XMLDB_TYPE_INTEGER, "20");
            $table->add_field("lastaccess", XMLDB_TYPE_INTEGER, "20");
            $table->add_field("useragent", XMLDB_TYPE_CHAR, "100");
            $table->add_field("useros", XMLDB_TYPE_CHAR, "100");
            $table->add_field("userlang", XMLDB_TYPE_CHAR, "30");
            $table->add_field("userip", XMLDB_TYPE_CHAR, "100");
            $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $table->add_index("userid", XMLDB_INDEX_NOTUNIQUE, ["userid"]);
            $table->add_index("courseid", XMLDB_INDEX_NOTUNIQUE, ["courseid"]);
            $table->add_index("user_course", XMLDB_INDEX_NOTUNIQUE, ["userid", "courseid"]);
            $table->add_index("page_param", XMLDB_INDEX_NOTUNIQUE, ["page", "param"]);
            $table->add_index("lastaccess", XMLDB_INDEX_NOTUNIQUE, ["lastaccess"]);
            $dbman->create_table($table);
        }

        $table = new xmldb_table("local_kopere_bi_track_log");
        if (!$dbman->table_exists($table)) {
            $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field("trackid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("userid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("courseid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("timepoint", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("timespend", XMLDB_TYPE_INTEGER, "20");
            $table->add_field("visits", XMLDB_TYPE_INTEGER, "10");
            $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $table->add_index("trackid", XMLDB_INDEX_NOTUNIQUE, ["trackid"]);
            $table->add_index("userid", XMLDB_INDEX_NOTUNIQUE, ["userid"]);
            $table->add_index("courseid", XMLDB_INDEX_NOTUNIQUE, ["courseid"]);
            $table->add_index("timepoint", XMLDB_INDEX_NOTUNIQUE, ["timepoint"]);
            $table->add_index("track_time", XMLDB_INDEX_NOTUNIQUE, ["trackid", "timepoint"]);
            $dbman->create_table($table);
        }

        $table = new xmldb_table("local_kopere_bi_log_tmp");
        if (!$dbman->table_exists($table)) {
            $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("eventname", XMLDB_TYPE_CHAR, "255");
            $table->add_field("component", XMLDB_TYPE_CHAR, "100");
            $table->add_field("action", XMLDB_TYPE_CHAR, "100");
            $table->add_field("target", XMLDB_TYPE_CHAR, "100");
            $table->add_field("objecttable", XMLDB_TYPE_CHAR, "100");
            $table->add_field("objectid", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("contextid", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("contextlevel", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("contextinstanceid", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("userid", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("courseid", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("relateduserid", XMLDB_TYPE_INTEGER, "10");
            $table->add_field("timecreated", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("origin", XMLDB_TYPE_CHAR, "20");
            $table->add_field("ip", XMLDB_TYPE_CHAR, "45");
            $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $table->add_index("courseid", XMLDB_INDEX_NOTUNIQUE, ["courseid"]);
            $table->add_index("userid", XMLDB_INDEX_NOTUNIQUE, ["userid"]);
            $table->add_index("relateduserid", XMLDB_INDEX_NOTUNIQUE, ["relateduserid"]);
            $table->add_index("component", XMLDB_INDEX_NOTUNIQUE, ["component"]);
            $table->add_index("action", XMLDB_INDEX_NOTUNIQUE, ["action"]);
            $table->add_index("target_action", XMLDB_INDEX_NOTUNIQUE, ["target", "action"]);
            $table->add_index("contextinstance", XMLDB_INDEX_NOTUNIQUE, ["contextinstanceid"]);
            $table->add_index("timecreated", XMLDB_INDEX_NOTUNIQUE, ["timecreated"]);
            $dbman->create_table($table);
        }

        // Reload native reports so installed SQL uses the new support tables.
        $pagefiles = glob(__DIR__ . "/files/page-*.json");
        foreach ($pagefiles as $pagefile) {
            reports::from_file($pagefile);
        }

        upgrade_plugin_savepoint(true, 2026052521, "local", "kopere_bi");
    }

    if ($oldversion < 2026080200) {
        $table = new xmldb_table("local_kopere_bi_track_log");
        $index = new xmldb_index("time_course_user", XMLDB_INDEX_NOTUNIQUE, ["timepoint", "courseid", "userid"]);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table("local_kopere_bi_log_tmp");
        $index = new xmldb_index("time_course_user", XMLDB_INDEX_NOTUNIQUE, ["timecreated", "courseid", "userid"]);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table("local_kopere_bi_engage");
        if (!$dbman->table_exists($table)) {
            $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field("batchid", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("userid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("courseid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("timeenrolled", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("dayssinceenrol", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("lastaccess", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("lastaction", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("daysinactive", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("actions7", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("actions14", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("actions30", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("previous7", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("timespent30", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("totalactivities", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("completedactivities", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("progress", XMLDB_TYPE_NUMBER, "10, 2", null, XMLDB_NOTNULL);
            $table->add_field("finalgrade", XMLDB_TYPE_NUMBER, "20, 5");
            $table->add_field("gradepercent", XMLDB_TYPE_NUMBER, "10, 2");
            $table->add_field("gradepass", XMLDB_TYPE_NUMBER, "20, 5", null, XMLDB_NOTNULL);
            $table->add_field("timecompleted", XMLDB_TYPE_INTEGER, "20");
            $table->add_field("engagement", XMLDB_TYPE_CHAR, "10", null, XMLDB_NOTNULL);
            $table->add_field("riskscore", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("risklevel", XMLDB_TYPE_CHAR, "10", null, XMLDB_NOTNULL);
            $table->add_field("riskreason", XMLDB_TYPE_CHAR, "50", null, XMLDB_NOTNULL);
            $table->add_field("timemodified", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $table->add_index("batch_user_course", XMLDB_INDEX_UNIQUE, ["batchid", "userid", "courseid"]);
            $table->add_index("batch_course", XMLDB_INDEX_NOTUNIQUE, ["batchid", "courseid"]);
            $table->add_index("batch_risk", XMLDB_INDEX_NOTUNIQUE, ["batchid", "risklevel", "riskscore"]);
            $table->add_index("batch_engagement", XMLDB_INDEX_NOTUNIQUE, ["batchid", "engagement"]);
            $table->add_index("batch_lastaccess", XMLDB_INDEX_NOTUNIQUE, ["batchid", "lastaccess"]);
            $dbman->create_table($table);
        }

        $table = new xmldb_table("local_kopere_bi_courseag");
        if (!$dbman->table_exists($table)) {
            $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field("batchid", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("courseid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("enrolments", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("active7", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("active30", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("neveraccessed", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("completions", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("highrisk", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("mediumrisk", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("avgprogress", XMLDB_TYPE_NUMBER, "10, 2", null, XMLDB_NOTNULL);
            $table->add_field("avggrade", XMLDB_TYPE_NUMBER, "10, 2");
            $table->add_field("completionrate", XMLDB_TYPE_NUMBER, "10, 2", null, XMLDB_NOTNULL);
            $table->add_field("engagementrate", XMLDB_TYPE_NUMBER, "10, 2", null, XMLDB_NOTNULL);
            $table->add_field("trendpercent", XMLDB_TYPE_NUMBER, "10, 2", null, XMLDB_NOTNULL);
            $table->add_field("healthscore", XMLDB_TYPE_NUMBER, "10, 2", null, XMLDB_NOTNULL);
            $table->add_field("healthlevel", XMLDB_TYPE_CHAR, "10", null, XMLDB_NOTNULL);
            $table->add_field("timemodified", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $table->add_index("batch_course", XMLDB_INDEX_UNIQUE, ["batchid", "courseid"]);
            $table->add_index("batch_health", XMLDB_INDEX_NOTUNIQUE, ["batchid", "healthlevel", "healthscore"]);
            $dbman->create_table($table);
        }

        $table = new xmldb_table("local_kopere_bi_daily");
        if (!$dbman->table_exists($table)) {
            $table->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field("batchid", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("daystart", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("daykey", XMLDB_TYPE_CHAR, "10", null, XMLDB_NOTNULL);
            $table->add_field("courseid", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("activeusers", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL);
            $table->add_field("actions", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_field("logins", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL);
            $table->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $table->add_index("batch_day_course", XMLDB_INDEX_UNIQUE, ["batchid", "daystart", "courseid"]);
            $table->add_index("batch_course_day", XMLDB_INDEX_NOTUNIQUE, ["batchid", "courseid", "daystart"]);
            $dbman->create_table($table);
        }

        foreach (range(106, 111) as $pagenumber) {
            reports::from_file(__DIR__ . "/files/page-{$pagenumber}.json");
        }

        upgrade_plugin_savepoint(true, 2026080200, "local", "kopere_bi");
    }

    return true;
}
