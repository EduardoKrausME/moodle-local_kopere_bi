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

    if ($oldversion < 2026092400) {
        // Store geolocation once per IP instead of repeating it in every online record.
        $locationtable = new xmldb_table("local_kopere_bi_iplocation");
        if (!$dbman->table_exists($locationtable)) {
            $locationtable->add_field("id", XMLDB_TYPE_INTEGER, "10", null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $locationtable->add_field("ip", XMLDB_TYPE_CHAR, "45", null, XMLDB_NOTNULL);
            $locationtable->add_field("city_name", XMLDB_TYPE_CHAR, "100");
            $locationtable->add_field("country_name", XMLDB_TYPE_CHAR, "100");
            $locationtable->add_field("country_code", XMLDB_TYPE_CHAR, "10");
            $locationtable->add_field("latitude", XMLDB_TYPE_NUMBER, "12, 7");
            $locationtable->add_field("longitude", XMLDB_TYPE_NUMBER, "12, 7");
            $locationtable->add_field("status", XMLDB_TYPE_INTEGER, "2", null, XMLDB_NOTNULL, null, "0");
            $locationtable->add_field("lastattempt", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL, null, "0");
            $locationtable->add_field("timecreated", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL, null, "0");
            $locationtable->add_field("timemodified", XMLDB_TYPE_INTEGER, "20", null, XMLDB_NOTNULL, null, "0");
            $locationtable->add_key("primary", XMLDB_KEY_PRIMARY, ["id"]);
            $locationtable->add_index("ip_unique", XMLDB_INDEX_UNIQUE, ["ip"]);
            $locationtable->add_index("status_attempt", XMLDB_INDEX_NOTUNIQUE, ["status", "lastattempt"]);
            $dbman->create_table($locationtable);
        }

        $onlinetable = new xmldb_table("local_kopere_bi_online");
        $iplocationfield = new xmldb_field("iplocationid", XMLDB_TYPE_INTEGER, "10", null, null, null, null, "lastip");
        if (!$dbman->field_exists($onlinetable, $iplocationfield)) {
            $dbman->add_field($onlinetable, $iplocationfield);
        }

        $lastipindex = new xmldb_index("lastip", XMLDB_INDEX_NOTUNIQUE, ["lastip"]);
        if (!$dbman->index_exists($onlinetable, $lastipindex)) {
            $dbman->add_index($onlinetable, $lastipindex);
        }

        $locationindex = new xmldb_index("iplocationid", XMLDB_INDEX_NOTUNIQUE, ["iplocationid"]);
        if (!$dbman->index_exists($onlinetable, $locationindex)) {
            $dbman->add_index($onlinetable, $locationindex);
        }

        // Preserve all locations already collected by the old hook before removing the duplicated columns.
        $legacyfields = ["city_name", "country_name", "country_code", "latitude", "longitude"];
        $selectfields = [];
        $haslegacyfields = false;
        foreach ($legacyfields as $legacyfield) {
            $field = new xmldb_field($legacyfield);
            if ($dbman->field_exists($onlinetable, $field)) {
                $selectfields[] = "MAX({$legacyfield}) AS {$legacyfield}";
                $haslegacyfields = true;
            } else {
                $selectfields[] = "NULL AS {$legacyfield}";
            }
        }

        if ($haslegacyfields) {
            $sql = "SELECT lastip, " . implode(", ", $selectfields) . "
                      FROM {local_kopere_bi_online}
                     WHERE lastip IS NOT NULL
                       AND lastip <> ''
                  GROUP BY lastip";

            $recordset = $DB->get_recordset_sql($sql);
            foreach ($recordset as $legacy) {
                $ip = trim((string)$legacy->lastip);
                if (strlen($ip) > 45 || filter_var($ip, FILTER_VALIDATE_IP) === false) {
                    continue;
                }

                $location = $DB->get_record("local_kopere_bi_iplocation", ["ip" => $ip]);
                $haslocation = $legacy->city_name !== null && $legacy->city_name !== '' ||
                    $legacy->country_name !== null && $legacy->country_name !== '' ||
                    $legacy->country_code !== null && $legacy->country_code !== '' ||
                    $legacy->latitude !== null && $legacy->latitude !== '' ||
                    $legacy->longitude !== null && $legacy->longitude !== '';
                $now = time();

                if (!$location) {
                    $location = (object)[
                        "ip" => $ip,
                        "city_name" => $legacy->city_name,
                        "country_name" => $legacy->country_name,
                        "country_code" => $legacy->country_code,
                        "latitude" => $legacy->latitude,
                        "longitude" => $legacy->longitude,
                        "status" => $haslocation ? 1 : 0,
                        "lastattempt" => $haslocation ? $now : 0,
                        "timecreated" => $now,
                        "timemodified" => $now,
                    ];
                    $location->id = $DB->insert_record("local_kopere_bi_iplocation", $location);
                } else if ($haslocation && (int)$location->status !== 1) {
                    $location->city_name = $legacy->city_name;
                    $location->country_name = $legacy->country_name;
                    $location->country_code = $legacy->country_code;
                    $location->latitude = $legacy->latitude;
                    $location->longitude = $legacy->longitude;
                    $location->status = 1;
                    $location->lastattempt = $now;
                    $location->timemodified = $now;
                    $DB->update_record("local_kopere_bi_iplocation", $location);
                }

                $DB->set_field_select(
                    "local_kopere_bi_online",
                    "iplocationid",
                    $location->id,
                    "lastip = :lastip",
                    ["lastip" => $legacy->lastip]
                );
            }
            $recordset->close();
        }

        // Drop the repeated location columns only after their values have been migrated.
        foreach (["city_name", "country_name", "country_code"] as $indexname) {
            $index = new xmldb_index($indexname, XMLDB_INDEX_NOTUNIQUE, [$indexname]);
            if ($dbman->index_exists($onlinetable, $index)) {
                $dbman->drop_index($onlinetable, $index);
            }
        }

        foreach ($legacyfields as $legacyfield) {
            $field = new xmldb_field($legacyfield);
            if ($dbman->field_exists($onlinetable, $field)) {
                $dbman->drop_field($onlinetable, $field);
            }
        }

        // The native online dashboard now reads location through iplocationid.
        reports::from_file(__DIR__ . "/files/page-001.json");

        upgrade_plugin_savepoint(true, 2026092400, "local", "kopere_bi");
    }

    return true;
}
