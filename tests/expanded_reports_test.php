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
 * SQL compatibility tests for the expanded report catalog.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\block\util\sql_util;

/**
 * Confirms that shipped report SQL parses on supported Moodle databases.
 *
 * @covers \local_kopere_bi\block\util\sql_util::prepare_sql
 */
final class expanded_reports_test extends advanced_testcase {
    /**
     * The 16 SQL reports are well-formed and have unique reference keys.
     */
    public function test_expanded_reports_sql(): void {
        global $DB;

        $this->resetAfterTest();
        $pagerefs = [];
        $elementrefs = [];

        foreach (range(112, 127) as $number) {
            $file = __DIR__ . "/../db/files/page-" . sprintf("%03d", $number) . ".json";
            $page = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

            $this->assertNotContains($page["refkey"], $pagerefs);
            $pagerefs[] = $page["refkey"];

            foreach ($page["blocks"] as $block) {
                foreach ($block["elements"] as $element) {
                    $this->assertNotContains($element["refkey"], $elementrefs);
                    $elementrefs[] = $element["refkey"];
                    $this->assertSame("table", $element["type"]);
                    $this->assertMatchesRegularExpression("/^SELECT\\s/i", $element["commandsql"]);

                    $prepared = sql_util::prepare_sql($element["commandsql"]);
                    try {
                        // The false predicate should avoid scanning data while checking SQL syntax.
                        $recordset = $DB->get_recordset_sql(
                            "SELECT * FROM (" . $prepared->sql . ") expanded_report_probe WHERE 1 = 0",
                            $prepared->params
                        );
                        $recordset->close();
                    } catch (\Throwable $exception) {
                        $this->fail("Report {$number} SQL failed: " . $exception->getMessage());
                    }
                }
            }
        }

        $this->assertCount(16, $pagerefs);
        $this->assertCount(16, $elementrefs);
    }
}
