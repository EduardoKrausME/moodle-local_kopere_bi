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

namespace local_kopere_bi\task;

use Exception;

/**
 * Synchronises support tables used by the native BI reports.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_tables_sync extends \core\task\scheduled_task {
    /**
     * Number of months kept in the standard log cache.
     *
     * @var int
     */
    protected $logmonths = 12;

    /**
     * Returns the task name.
     *
     * @return string
     */
    public function get_name() {
        return "Synchronise Kopere BI report support tables";
    }

    /**
     * Executes the scheduled task.
     *
     * @throws Exception
     */
    public function execute() {
        global $DB;

        if (!in_array($DB->get_dbfamily(), ["mysql", "postgres"])) {
            mtrace("Only MySQL and PostgreSQL are supported by the Kopere BI support table synchronisation.");
            return;
        }

        $this->rebuild_tracking_tables();
        $this->sync_standard_log_table();
    }

    /**
     * Rebuilds tracking caches from local_kopere_bi_online.
     *
     * @throws Exception
     */
    protected function rebuild_tracking_tables() {
        global $DB;

        mtrace("Rebuilding Kopere BI tracking cache from local_kopere_bi_online...");

        $DB->delete_records("local_kopere_bi_track_log");
        $DB->delete_records("local_kopere_bi_tracking");

        $pagecase = "CASE WHEN o.moduleid > 0 THEN 'module' WHEN o.courseid > 1 THEN 'course' ELSE 'site' END";
        $paramcase = "CASE WHEN o.moduleid > 0 THEN o.moduleid WHEN o.courseid > 1 THEN o.courseid ELSE 0 END";

        $sql = "INSERT INTO {local_kopere_bi_tracking}
                    (userid, courseid, page, param, timespend, visits, firstaccess, lastaccess, useragent, useros, userlang, userip)
                SELECT o.userid,
                       o.courseid,
                       {$pagecase} AS page,
                       {$paramcase} AS param,
                       SUM(o.seconds) AS timespend,
                       COUNT(o.id) AS visits,
                       MIN(o.currenttime) AS firstaccess,
                       MAX(o.currenttime) AS lastaccess,
                       COALESCE(o.client_name, '') AS useragent,
                       COALESCE(o.os_name, '') AS useros,
                       '' AS userlang,
                       COALESCE(o.lastip, '') AS userip
                  FROM {local_kopere_bi_online} o
                 WHERE o.userid > 0
              GROUP BY o.userid,
                       o.courseid,
                       {$pagecase},
                       {$paramcase},
                       COALESCE(o.client_name, ''),
                       COALESCE(o.os_name, ''),
                       COALESCE(o.lastip, '')";
        $DB->execute($sql);

        $this->rebuild_tracking_log_table($pagecase, $paramcase);

        $trackingcount = $DB->count_records("local_kopere_bi_tracking");
        $logcount = $DB->count_records("local_kopere_bi_track_log");
        mtrace("Kopere BI tracking cache rebuilt: {$trackingcount} tracking rows and {$logcount} daily rows.");
    }

    /**
     * Rebuilds the daily tracking cache.
     *
     * @param string $pagecase
     * @param string $paramcase
     * @throws Exception
     */
    protected function rebuild_tracking_log_table($pagecase, $paramcase) {
        global $DB;

        if ($DB->get_dbfamily() == "mysql") {
            $timepoint = "UNIX_TIMESTAMP(DATE(FROM_UNIXTIME(o.currenttime)))";
        } else {
            $timepoint = "CAST(EXTRACT(EPOCH FROM DATE_TRUNC('day', TO_TIMESTAMP(o.currenttime))) AS BIGINT)";
        }

        $sql = "INSERT INTO {local_kopere_bi_track_log}
                    (trackid, userid, courseid, timepoint, timespend, visits)
                SELECT t.id AS trackid,
                       o.userid,
                       o.courseid,
                       {$timepoint} AS timepoint,
                       SUM(o.seconds) AS timespend,
                       COUNT(o.id) AS visits
                  FROM {local_kopere_bi_online} o
                  JOIN {local_kopere_bi_tracking} t
                    ON t.userid = o.userid
                   AND t.courseid = o.courseid
                   AND t.page = {$pagecase}
                   AND t.param = {$paramcase}
                   AND COALESCE(t.useragent, '') = COALESCE(o.client_name, '')
                   AND COALESCE(t.useros, '') = COALESCE(o.os_name, '')
                   AND COALESCE(t.userip, '') = COALESCE(o.lastip, '')
                 WHERE o.userid > 0
              GROUP BY t.id,
                       o.userid,
                       o.courseid,
                       {$timepoint}";
        $DB->execute($sql);
    }

    /**
     * Synchronises a filtered cache of logstore_standard_log.
     *
     * @throws Exception
     */
    protected function sync_standard_log_table() {
        global $DB;

        mtrace("Synchronising Kopere BI temporary log cache from logstore_standard_log...");

        $since = strtotime("-{$this->logmonths} months", time());
        $DB->delete_records_select("local_kopere_bi_log_tmp", "timecreated < :since", ["since" => $since]);

        $lastid = (int)$DB->get_field_sql("SELECT MAX(id) FROM {local_kopere_bi_log_tmp}");

        $sql = "INSERT INTO {local_kopere_bi_log_tmp}
                    (id, eventname, component, action, target, objecttable, objectid, contextid, contextlevel,
                     contextinstanceid, userid, courseid, relateduserid, timecreated, origin, ip)
                SELECT l.id,
                       l.eventname,
                       l.component,
                       l.action,
                       l.target,
                       l.objecttable,
                       l.objectid,
                       l.contextid,
                       l.contextlevel,
                       l.contextinstanceid,
                       l.userid,
                       l.courseid,
                       l.relateduserid,
                       l.timecreated,
                       l.origin,
                       l.ip
                  FROM {logstore_standard_log} l
                 WHERE l.id > :lastid
                   AND l.timecreated >= :since
                   AND (
                         l.component LIKE 'mod_%'
                         OR (l.action = 'loggedout' AND l.contextid = 1)
                         OR (l.target = 'role' AND l.contextlevel = 50 AND (l.action = 'assigned' OR l.action = 'unassigned'))
                       )";
        $DB->execute($sql, ["lastid" => $lastid, "since" => $since]);

        $logcount = $DB->count_records("local_kopere_bi_log_tmp");
        mtrace("Kopere BI temporary log cache synchronised: {$logcount} rows available.");
    }
}
