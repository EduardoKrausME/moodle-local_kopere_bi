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
 * Regression tests for stock reports and bundled SQL.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\block\util\sql_util;

/**
 * Active-driver SQL probes and regression checks for fixed metrics.
 * @package local_kopere_bi
 * @covers \local_kopere_bi\block\util\sql_util::prepare_sql
 */
final class group5_report_regressions_test extends advanced_testcase {
    /**
     * Provide one independent SQL check per report.
     *
     * @return array<string, array{int}>
     */
    public static function report_ids(): array {
        $cases = [];
        foreach ([35, 37, 52, 53, 63, 74, 75, 76, 79, 83, 87, 97, 120] as $id) {
            $cases["report_{$id}"] = [$id];
        }
        return $cases;
    }

    /**
     * Validate each SQL against the active Moodle database without loading rows.
     *
     * @dataProvider report_ids
     * @param int $number The report number.
     */
    public function test_report_sql_parses(int $number): void {
        global $DB;
        $this->resetAfterTest();
        $root = __DIR__ . "/../db/files/page-" . sprintf("%03d", $number);
        $suffix = $DB->get_dbfamily() === "postgres" &&
            file_exists($root . "-postgres.json") ? "-postgres" : "";
        $page = json_decode(file_get_contents($root . $suffix . ".json"),
            true, 512, JSON_THROW_ON_ERROR);
        $element = $page["blocks"][0]["elements"][0];
        $this->assertNotEmpty($page["refkey"]);
        $this->assertNotEmpty($element["refkey"]);
        $prepared = sql_util::prepare_sql($element["commandsql"]);
        $records = $DB->get_recordset_sql(
            "SELECT * FROM (" . rtrim($prepared->sql, "; \n\r\t") .
            ") group5_empty_probe WHERE 1 = 0", $prepared->params);
        $records->close();
    }

    /**
     * Ensure the exact reported defects cannot silently regress.
     */
    public function test_fixed_definitions(): void {
        foreach (["074", "074-postgres"] as $suffix) {
            $page = json_decode(file_get_contents(__DIR__ . "/../db/files/page-{$suffix}.json"),
                true, 512, JSON_THROW_ON_ERROR);
            $sql = $page["blocks"][0]["elements"][0]["commandsql"];
            $this->assertStringContainsString("AS contributors", $sql);
            if ($suffix === "074-postgres") {
                $this->assertStringContainsString("COUNT(DISTINCT fp.userid)", $sql);
                $this->assertStringNotContainsString("kbi_fp.userid", $sql);
            } else {
                $this->assertStringNotContainsString("GROUP BY a.id", $sql);
            }
        }
        $forum = json_decode(file_get_contents(__DIR__ . "/../db/files/page-076.json"),
            true, 512, JSON_THROW_ON_ERROR)["blocks"][0]["elements"][0];
        $this->assertArrayHasKey("replies_received", $forum["info"]["column"]);
        $this->assertStringContainsString("id={{{userid}}}",
            $forum["info"]["column"]["firstname"]["mustache"]);
        $quiz = json_decode(file_get_contents(__DIR__ . "/../db/files/page-120.json"),
            true, 512, JSON_THROW_ON_ERROR)["blocks"][0]["elements"][0]["commandsql"];
        $this->assertStringContainsString("MIN(qnext.id)", $quiz);
        $this->assertStringContainsString("a1.preview = 0 AND a2.preview = 0", $quiz);
        foreach (["075", "075-postgres"] as $suffix) {
            $page = json_decode(file_get_contents(__DIR__ . "/../db/files/page-{$suffix}.json"),
                true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame("string", $page["blocks"][0]["elements"][0]["info"]["column"]["id"]["type"]);
        }
    }
}
