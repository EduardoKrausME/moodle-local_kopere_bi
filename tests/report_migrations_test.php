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
 * Tests for targeted saved-report migrations.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\upgrade\report_migrations;

/**
 * Ensure migrations repair outdated SQL without resetting report customisations.
 */
final class report_migrations_test extends advanced_testcase {
    /**
     * Test the targeted correction and its repeatability.
     *
     * @covers \local_kopere_bi\upgrade\report_migrations
     * @return void
     */
    public function test_fix_quiz_question_status_only_updates_invalid_reference(): void {
        global $DB;

        $this->resetAfterTest();

        $categoryid = $DB->insert_record("local_kopere_bi_cat", (object)[
            "title" => "Migration tests",
            "refkey" => "migration_test_cat",
        ]);
        $pageid = $DB->insert_record("local_kopere_bi_page", (object)[
            "title" => "Migration tests",
            "cat_id" => $categoryid,
            "refkey" => "migration_test_page",
        ]);
        $blockid = $DB->insert_record("local_kopere_bi_block", (object)[
            "page_id" => $pageid,
            "refkey" => "migration_test_block",
            "type" => "block-1",
        ]);

        $oldsql = "SELECT kbi_q.status AS question_status, kbi_q.name AS question_name FROM {question} kbi_q";
        $corrected = "SELECT kbi_qv.status AS question_status, kbi_q.name AS question_name FROM {question} kbi_q";
        $elementid = $DB->insert_record("local_kopere_bi_element", (object)[
            "block_id" => $blockid,
            "refkey" => "kbi_el_quiz_question_structure",
            "title" => "Customised quiz question report",
            "type" => "table",
            "commandsql" => $oldsql,
            "info" => "{}",
        ]);

        $othersql = "SELECT id FROM {question}";
        $otherid = $DB->insert_record("local_kopere_bi_element", (object)[
            "block_id" => $blockid,
            "refkey" => "migration_other_report",
            "title" => "Unrelated report",
            "type" => "table",
            "commandsql" => $othersql,
            "info" => "{}",
        ]);

        report_migrations::fix_quiz_question_status();
        $this->assertSame($corrected, $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $elementid]));
        $this->assertSame($othersql, $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $otherid]));
        $this->assertSame(
            "Customised quiz question report",
            $DB->get_field("local_kopere_bi_element", "title", ["id" => $elementid])
        );

        // Re-running the operation must not rewrite an already-correct report.
        report_migrations::fix_quiz_question_status();
        $this->assertSame($corrected, $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $elementid]));
    }
}
