<?php

namespace local_kopere_bi\analytics;

/**
 * Builds the small support tables consumed by the learning analytics dashboards.
 *
 * @package local_kopere_bi
 */
class snapshot_builder {
    /**
     * Build a complete and consistent analytics snapshot.
     *
     * @return int Snapshot identifier.
     * @throws \dml_exception
     */
    public function execute(): int {
        global $DB;

        $batchid = time();
        $transaction = $DB->start_delegated_transaction();

        $this->build_learner_snapshot($batchid);
        $this->build_course_snapshot($batchid);
        $this->build_daily_snapshot($batchid);

        $transaction->allow_commit();

        $DB->delete_records_select("local_kopere_bi_engage", "batchid <> :batchid", ["batchid" => $batchid]);
        $DB->delete_records_select("local_kopere_bi_courseag", "batchid <> :batchid", ["batchid" => $batchid]);
        $DB->delete_records_select("local_kopere_bi_daily", "batchid <> :batchid", ["batchid" => $batchid]);

        return $batchid;
    }

    /**
     * Build one row for every active student enrolment.
     *
     * @param int $batchid Snapshot identifier.
     * @throws \dml_exception
     */
    private function build_learner_snapshot(int $batchid): void {
        global $DB;

        $now = time();
        $mediumdays = $this->config_int("analytics_medium_days", 14, 1, 365);
        $highdays = $this->config_int("analytics_high_days", 30, $mediumdays, 730);
        $minactions = $this->config_int("analytics_min_actions", 5, 1, 1000);
        $lowprogress = $this->config_int("analytics_low_progress", 25, 0, 100);
        $gracedays = $this->config_int("analytics_grace_days", 7, 0, 365);

        $since7 = $now - (7 * DAYSECS);
        $since14 = $now - (14 * DAYSECS);
        $since30 = $now - (30 * DAYSECS);

        $score = "CASE
                    WHEN b.timecompleted IS NOT NULL THEN 0
                    ELSE
                        CASE WHEN b.lastaccess = 0 AND b.dayssinceenrol >= {$gracedays} THEN 35 ELSE 0 END
                        + CASE
                            WHEN b.lastaccess > 0 AND b.daysinactive >= {$highdays} THEN 35
                            WHEN b.lastaccess > 0 AND b.daysinactive >= {$mediumdays} THEN 20
                            ELSE 0
                          END
                        + CASE
                            WHEN b.dayssinceenrol >= {$gracedays} AND b.actions14 < {$minactions} THEN 20
                            ELSE 0
                          END
                        + CASE
                            WHEN b.dayssinceenrol >= {$gracedays}
                             AND b.totalactivities > 0
                             AND b.progress < {$lowprogress} THEN 15
                            ELSE 0
                          END
                        + CASE
                            WHEN b.gradepass > 0
                             AND b.finalgrade IS NOT NULL
                             AND b.finalgrade < b.gradepass THEN 30
                            ELSE 0
                          END
                  END";

        $sql = "INSERT INTO {local_kopere_bi_engage}
                    (batchid, userid, courseid, timeenrolled, dayssinceenrol, lastaccess, lastaction,
                     daysinactive, actions7, actions14, actions30, previous7, timespent30,
                     totalactivities, completedactivities, progress, finalgrade, gradepercent,
                     gradepass, timecompleted, engagement, riskscore, risklevel, riskreason, timemodified)
                SELECT :batchid,
                       s.userid,
                       s.courseid,
                       s.timeenrolled,
                       s.dayssinceenrol,
                       s.lastaccess,
                       s.lastaction,
                       s.daysinactive,
                       s.actions7,
                       s.actions14,
                       s.actions30,
                       s.previous7,
                       s.timespent30,
                       s.totalactivities,
                       s.completedactivities,
                       s.progress,
                       s.finalgrade,
                       s.gradepercent,
                       s.gradepass,
                       s.timecompleted,
                       CASE
                         WHEN s.timecompleted IS NOT NULL THEN 'high'
                         WHEN s.riskscore >= 60 THEN 'at-risk'
                         WHEN s.actions14 >= " . ($minactions * 4) . " THEN 'high'
                         WHEN s.actions14 >= " . ($minactions * 2) . " THEN 'medium'
                         WHEN s.actions14 >= {$minactions} THEN 'low'
                         ELSE 'at-risk'
                       END,
                       s.riskscore,
                       CASE
                         WHEN s.riskscore >= 60 THEN 'high'
                         WHEN s.riskscore >= 30 THEN 'medium'
                         WHEN s.riskscore >= 15 THEN 'low'
                         ELSE 'none'
                       END,
                       CASE
                         WHEN s.timecompleted IS NOT NULL THEN 'completed'
                         WHEN s.lastaccess = 0 AND s.dayssinceenrol >= {$gracedays} THEN 'noaccess'
                         WHEN s.lastaccess > 0 AND s.daysinactive >= {$highdays} THEN 'longinactive'
                         WHEN s.gradepass > 0 AND s.finalgrade IS NOT NULL AND s.finalgrade < s.gradepass THEN 'lowgrade'
                         WHEN s.lastaccess > 0 AND s.daysinactive >= {$mediumdays} THEN 'inactive'
                         WHEN s.dayssinceenrol >= {$gracedays} AND s.actions14 < {$minactions} THEN 'lowactivity'
                         WHEN s.dayssinceenrol >= {$gracedays}
                          AND s.totalactivities > 0
                          AND s.progress < {$lowprogress} THEN 'lowprogress'
                         ELSE 'monitor'
                       END,
                       :timemodified
                  FROM (
                        SELECT b.*, {$score} AS riskscore
                          FROM (
                                SELECT enrolments.userid,
                                       enrolments.courseid,
                                       enrolments.timeenrolled,
                                       FLOOR(({$now} - enrolments.timeenrolled) / 86400.0) AS dayssinceenrol,
                                       CASE
                                         WHEN COALESCE(la.timeaccess, 0) > COALESCE(logs.lastaction, 0)
                                           THEN COALESCE(la.timeaccess, 0)
                                         ELSE COALESCE(logs.lastaction, 0)
                                       END AS lastaccess,
                                       COALESCE(logs.lastaction, 0) AS lastaction,
                                       CASE
                                         WHEN COALESCE(la.timeaccess, 0) = 0 AND COALESCE(logs.lastaction, 0) = 0
                                           THEN FLOOR(({$now} - enrolments.timeenrolled) / 86400.0)
                                         WHEN COALESCE(la.timeaccess, 0) > COALESCE(logs.lastaction, 0)
                                           THEN FLOOR(({$now} - la.timeaccess) / 86400.0)
                                         ELSE FLOOR(({$now} - logs.lastaction) / 86400.0)
                                       END AS daysinactive,
                                       COALESCE(logs.actions7, 0) AS actions7,
                                       COALESCE(logs.actions14, 0) AS actions14,
                                       COALESCE(logs.actions30, 0) AS actions30,
                                       COALESCE(logs.previous7, 0) AS previous7,
                                       COALESCE(tracking.timespent30, 0) AS timespent30,
                                       COALESCE(activitytotal.totalactivities, 0) AS totalactivities,
                                       COALESCE(activitydone.completedactivities, 0) AS completedactivities,
                                       CASE
                                         WHEN COALESCE(activitytotal.totalactivities, 0) > 0
                                           THEN ROUND(100.0 * COALESCE(activitydone.completedactivities, 0)
                                                / activitytotal.totalactivities, 2)
                                         ELSE 0
                                       END AS progress,
                                       grades.finalgrade,
                                       grades.gradepercent,
                                       COALESCE(grades.gradepass, 0) AS gradepass,
                                       completion.timecompleted
                                  FROM (
                                        SELECT ue.userid,
                                               e.courseid,
                                               MIN(CASE WHEN ue.timestart > 0 THEN ue.timestart ELSE ue.timecreated END)
                                                   AS timeenrolled
                                          FROM {user_enrolments} ue
                                          JOIN {enrol} e ON e.id = ue.enrolid
                                          JOIN {context} ctx ON ctx.contextlevel = 50 AND ctx.instanceid = e.courseid
                                          JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = ue.userid
                                          JOIN {role} roledata ON roledata.id = ra.roleid AND roledata.archetype = 'student'
                                          JOIN {user} userdata ON userdata.id = ue.userid
                                          JOIN {course} coursedata ON coursedata.id = e.courseid
                                         WHERE e.status = 0
                                           AND ue.status = 0
                                           AND (ue.timestart = 0 OR ue.timestart <= {$now})
                                           AND (ue.timeend = 0 OR ue.timeend > {$now})
                                           AND userdata.deleted = 0
                                           AND userdata.suspended = 0
                                           AND coursedata.id > 1
                                      GROUP BY ue.userid, e.courseid
                                       ) enrolments
                             LEFT JOIN {user_lastaccess} la
                                    ON la.userid = enrolments.userid AND la.courseid = enrolments.courseid
                             LEFT JOIN (
                                        SELECT userid,
                                               courseid,
                                               MAX(timecreated) AS lastaction,
                                               SUM(CASE WHEN timecreated >= {$since7} THEN 1 ELSE 0 END) AS actions7,
                                               SUM(CASE WHEN timecreated >= {$since14} THEN 1 ELSE 0 END) AS actions14,
                                               COUNT(id) AS actions30,
                                               SUM(CASE WHEN timecreated >= {$since14} AND timecreated < {$since7}
                                                        THEN 1 ELSE 0 END) AS previous7
                                          FROM {local_kopere_bi_log_tmp}
                                         WHERE userid > 0
                                           AND courseid > 1
                                           AND timecreated >= {$since30}
                                      GROUP BY userid, courseid
                                       ) logs
                                    ON logs.userid = enrolments.userid AND logs.courseid = enrolments.courseid
                             LEFT JOIN (
                                        SELECT userid, courseid, SUM(timespend) AS timespent30
                                          FROM {local_kopere_bi_track_log}
                                         WHERE timepoint >= {$since30}
                                      GROUP BY userid, courseid
                                       ) tracking
                                    ON tracking.userid = enrolments.userid AND tracking.courseid = enrolments.courseid
                             LEFT JOIN (
                                        SELECT course AS courseid, COUNT(id) AS totalactivities
                                          FROM {course_modules}
                                         WHERE deletioninprogress = 0
                                           AND visible = 1
                                           AND completion > 0
                                      GROUP BY course
                                       ) activitytotal ON activitytotal.courseid = enrolments.courseid
                             LEFT JOIN (
                                        SELECT cm.course AS courseid,
                                               cmc.userid,
                                               COUNT(DISTINCT cmc.coursemoduleid) AS completedactivities
                                          FROM {course_modules_completion} cmc
                                          JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                                         WHERE cm.deletioninprogress = 0
                                           AND cm.visible = 1
                                           AND cm.completion > 0
                                           AND cmc.completionstate IN (1, 2)
                                      GROUP BY cm.course, cmc.userid
                                       ) activitydone
                                    ON activitydone.userid = enrolments.userid
                                   AND activitydone.courseid = enrolments.courseid
                             LEFT JOIN (
                                        SELECT gi.courseid,
                                               gg.userid,
                                               gg.finalgrade,
                                               gi.gradepass,
                                               CASE
                                                 WHEN gi.grademax > 0 AND gg.finalgrade IS NOT NULL
                                                   THEN ROUND(100.0 * gg.finalgrade / gi.grademax, 2)
                                                 ELSE NULL
                                               END AS gradepercent
                                          FROM {grade_items} gi
                                          JOIN {grade_grades} gg ON gg.itemid = gi.id
                                         WHERE gi.itemtype = 'course'
                                       ) grades
                                    ON grades.userid = enrolments.userid AND grades.courseid = enrolments.courseid
                             LEFT JOIN {course_completions} completion
                                    ON completion.userid = enrolments.userid
                                   AND completion.course = enrolments.courseid
                               ) b
                       ) s";

        $DB->execute($sql, ["batchid" => $batchid, "timemodified" => $now]);
    }

    /**
     * Aggregate the learner snapshot into course health metrics.
     *
     * @param int $batchid Snapshot identifier.
     * @throws \dml_exception
     */
    private function build_course_snapshot(int $batchid): void {
        global $DB;

        $health = "ROUND(
            metrics.completionrate * 0.25
            + metrics.avgprogress * 0.25
            + metrics.engagementrate * 0.25
            + COALESCE(metrics.avggrade, metrics.avgprogress) * 0.15
            + (100.0 - (100.0 * metrics.highrisk / metrics.enrolments)) * 0.10,
            2
        )";

        $sql = "INSERT INTO {local_kopere_bi_courseag}
                    (batchid, courseid, enrolments, active7, active30, neveraccessed, completions,
                     highrisk, mediumrisk, avgprogress, avggrade, completionrate, engagementrate,
                     trendpercent, healthscore, healthlevel, timemodified)
                SELECT :targetbatch,
                       metrics.courseid,
                       metrics.enrolments,
                       metrics.active7,
                       metrics.active30,
                       metrics.neveraccessed,
                       metrics.completions,
                       metrics.highrisk,
                       metrics.mediumrisk,
                       metrics.avgprogress,
                       metrics.avggrade,
                       metrics.completionrate,
                       metrics.engagementrate,
                       metrics.trendpercent,
                       {$health} AS healthscore,
                       CASE
                         WHEN {$health} >= 75 THEN 'healthy'
                         WHEN {$health} >= 50 THEN 'attention'
                         ELSE 'critical'
                       END AS healthlevel,
                       :timemodified
                  FROM (
                        SELECT courseid,
                               COUNT(id) AS enrolments,
                               SUM(CASE WHEN actions7 > 0 THEN 1 ELSE 0 END) AS active7,
                               SUM(CASE WHEN actions30 > 0 THEN 1 ELSE 0 END) AS active30,
                               SUM(CASE WHEN lastaccess = 0 THEN 1 ELSE 0 END) AS neveraccessed,
                               SUM(CASE WHEN timecompleted IS NOT NULL THEN 1 ELSE 0 END) AS completions,
                               SUM(CASE WHEN risklevel = 'high' THEN 1 ELSE 0 END) AS highrisk,
                               SUM(CASE WHEN risklevel = 'medium' THEN 1 ELSE 0 END) AS mediumrisk,
                               ROUND(AVG(progress), 2) AS avgprogress,
                               ROUND(AVG(gradepercent), 2) AS avggrade,
                               ROUND(100.0 * SUM(CASE WHEN timecompleted IS NOT NULL THEN 1 ELSE 0 END)
                                     / COUNT(id), 2) AS completionrate,
                               ROUND(100.0 * SUM(CASE WHEN actions30 > 0 THEN 1 ELSE 0 END)
                                     / COUNT(id), 2) AS engagementrate,
                               CASE
                                 WHEN SUM(previous7) > 0
                                   THEN ROUND(100.0 * (SUM(actions7) - SUM(previous7)) / SUM(previous7), 2)
                                 WHEN SUM(actions7) > 0 THEN 100
                                 ELSE 0
                               END AS trendpercent
                          FROM {local_kopere_bi_engage}
                         WHERE batchid = :sourcebatch
                      GROUP BY courseid
                       ) metrics";

        $DB->execute($sql, [
            "targetbatch" => $batchid,
            "sourcebatch" => $batchid,
            "timemodified" => time(),
        ]);
    }

    /**
     * Build the time series used by access and activity trend charts.
     *
     * @param int $batchid Snapshot identifier.
     * @throws \dml_exception
     */
    private function build_daily_snapshot(int $batchid): void {
        global $DB;

        $historydays = $this->config_int("analytics_history_days", 90, 30, 365);
        $since = time() - ($historydays * DAYSECS);

        if ($DB->get_dbfamily() === "mysql") {
            $daystart = "UNIX_TIMESTAMP(DATE(FROM_UNIXTIME(l.timecreated)))";
            $daykey = "DATE_FORMAT(FROM_UNIXTIME(l.timecreated), '%Y-%m-%d')";
        } else {
            $daystart = "CAST(EXTRACT(EPOCH FROM DATE_TRUNC('day', TO_TIMESTAMP(l.timecreated))) AS BIGINT)";
            $daykey = "TO_CHAR(TO_TIMESTAMP(l.timecreated), 'YYYY-MM-DD')";
        }

        $sitewide = "INSERT INTO {local_kopere_bi_daily}
                          (batchid, daystart, daykey, courseid, activeusers, actions, logins)
                     SELECT :batchid,
                            {$daystart},
                            {$daykey},
                            0,
                            COUNT(DISTINCT l.userid),
                            SUM(CASE WHEN l.component LIKE 'mod_%' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN l.action = 'loggedin' AND l.target = 'user' THEN 1 ELSE 0 END)
                       FROM {local_kopere_bi_log_tmp} l
                       JOIN (
                              SELECT DISTINCT userid
                                FROM {local_kopere_bi_engage}
                               WHERE batchid = {$batchid}
                            ) learners ON learners.userid = l.userid
                      WHERE l.userid > 0
                        AND l.timecreated >= :since
                   GROUP BY {$daystart}, {$daykey}";
        $DB->execute($sitewide, ["batchid" => $batchid, "since" => $since]);

        $bycourse = "INSERT INTO {local_kopere_bi_daily}
                          (batchid, daystart, daykey, courseid, activeusers, actions, logins)
                     SELECT :batchid,
                            {$daystart},
                            {$daykey},
                            l.courseid,
                            COUNT(DISTINCT l.userid),
                            COUNT(l.id),
                            0
                       FROM {local_kopere_bi_log_tmp} l
                       JOIN {local_kopere_bi_engage} e
                         ON e.batchid = {$batchid}
                        AND e.userid = l.userid
                        AND e.courseid = l.courseid
                      WHERE l.userid > 0
                        AND l.courseid > 1
                        AND l.component LIKE 'mod_%'
                        AND l.timecreated >= :since
                   GROUP BY {$daystart}, {$daykey}, l.courseid";
        $DB->execute($bycourse, ["batchid" => $batchid, "since" => $since]);
    }

    /**
     * Return a bounded integer plugin setting.
     *
     * @param string $name Setting name.
     * @param int $default Default value.
     * @param int $minimum Minimum accepted value.
     * @param int $maximum Maximum accepted value.
     * @return int
     */
    private function config_int(string $name, int $default, int $minimum, int $maximum): int {
        $value = get_config("local_kopere_bi", $name);
        if ($value === false || $value === "") {
            $value = $default;
        }

        return max($minimum, min($maximum, (int)$value));
    }
}
