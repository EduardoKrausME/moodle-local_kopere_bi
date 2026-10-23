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
 * Compatibility and non-destructive refresh checks for the analytics dashboards.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\upgrade\analytics_report_refresh;

/**
 * Verify that the expanded analytics are executable and can be refreshed safely.
 *
 * @covers \local_kopere_bi\block\util\sql_util::prepare_sql
 * @covers \local_kopere_bi\upgrade\analytics_report_refresh::refresh
 */
final class analytics_report_refresh_test extends advanced_testcase {
    /**
     * Probe every requested dashboard using its actual database variant.
     */
    public function test_refined_reports_are_executable_and_have_expected_columns(): void {
        global $DB;

        $this->resetAfterTest();
        $expected = [
            121 => ["previous_grade", "grade_delta", "changed_by_userid"],
            122 => ["questionid", "incorrect_answer_share"],
            124 => ["activity_name", "criterionid", "assessments"],
            125 => ["feedbackid", "respondents", "total_responses"],
            126 => ["forumid", "discussions", "posts_per_contributor"],
            52 => ["is_overdue", "waiting_days"],
        ];

        foreach ($expected as $number => $required) {
            $suffix = $number === 52 && $DB->get_dbfamily() === "postgres" ? "-postgres" : "";
            $path = __DIR__ . "/../db/files/page-" . sprintf("%03d", $number) . $suffix . ".json";
            $page = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $element = $page["blocks"][0]["elements"][0];
            foreach ($required as $column) {
                $this->assertArrayHasKey($column, $element["info"]["column"], $path);
            }
            $prepared = sql_util::prepare_sql($element["commandsql"]);
            $sql = rtrim($prepared->sql, "; \n\r\t");
            try {
                $recordset = $DB->get_recordset_sql(
                    "SELECT * FROM (" . $sql . ") analytic_probe WHERE 1 = 0", $prepared->params
                );
                $recordset->close();
            } catch (\Throwable $e) {
                $this->fail(basename($path) . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Detect report customisations without changing the saved SQL on dry run.
     */
    public function test_dry_run_preserves_customised_report(): void {
        global $DB;

        $this->resetAfterTest();
        $categoryid = $DB->insert_record("local_kopere_bi_cat", (object)[
            "title" => "Analytics tests", "refkey" => "analytics_test_category",
        ]);
        $pageid = $DB->insert_record("local_kopere_bi_page", (object)[
            "cat_id" => $categoryid, "title" => "Analytics tests",
            "refkey" => "analytics_test_page",
        ]);
        $blockid = $DB->insert_record("local_kopere_bi_block", (object)[
            "page_id" => $pageid, "refkey" => "analytics_test_block", "type" => "block-1",
        ]);
        $elementid = $DB->insert_record("local_kopere_bi_element", (object)[
            "block_id" => $blockid, "refkey" => "kbi_el_grade_change_history",
            "title" => "Custom", "type" => "table",
            "commandsql" => "SELECT 42 AS id", "info" => "{}",
        ]);

        $result = analytics_report_refresh::refresh(true);
        $this->assertGreaterThanOrEqual(1, $result["preserved"]);
        $this->assertSame("SELECT 42 AS id",
            $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $elementid]));
    }
}
