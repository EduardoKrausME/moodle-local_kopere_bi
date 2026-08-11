<?php

namespace local_kopere_bi\task;

use local_kopere_bi\analytics\snapshot_builder;

/**
 * Refreshes the aggregated learning analytics tables.
 *
 * @package local_kopere_bi
 */
class analytics_snapshot extends \core\task\scheduled_task {
    /**
     * Return the translated task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string("task_analytics_snapshot", "local_kopere_bi");
    }

    /**
     * Build the next analytics snapshot.
     *
     * @throws \dml_exception
     */
    public function execute(): void {
        global $DB;

        if (!in_array($DB->get_dbfamily(), ["mysql", "postgres"], true)) {
            mtrace("Kopere BI learning analytics supports MySQL and PostgreSQL.");
            return;
        }

        mtrace("Building Kopere BI learning analytics snapshot...");
        $batchid = (new snapshot_builder())->execute();

        $learners = $DB->count_records("local_kopere_bi_engage", ["batchid" => $batchid]);
        $courses = $DB->count_records("local_kopere_bi_courseag", ["batchid" => $batchid]);
        $days = $DB->count_records("local_kopere_bi_daily", ["batchid" => $batchid]);
        mtrace("Snapshot {$batchid} ready: {$learners} enrolments, {$courses} courses and {$days} daily rows.");
    }
}
