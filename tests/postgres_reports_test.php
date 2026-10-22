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
 * Tests for MySQL and PostgreSQL variants of bundled reports.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\install\reports;

/**
 * Verify report variants are independent and imported only for their database family.
 *
 * @covers \local_kopere_bi\install\reports
 */
final class postgres_reports_test extends advanced_testcase {
    /**
     * Every MySQL-only page must have a PostgreSQL counterpart with unique identifiers.
     *
     * @return void
     */
    public function test_all_mysql_only_pages_have_postgres_variants(): void {
        $files = glob(__DIR__ . "/../db/files/page-*-postgres.json");
        $this->assertCount(40, $files);

        $allkeys = [];
        foreach ($files as $file) {
            $postgres = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            $mysqlfile = str_replace("-postgres.json", ".json", $file);
            $this->assertFileExists($mysqlfile);
            $mysql = json_decode(file_get_contents($mysqlfile), true, 512, JSON_THROW_ON_ERROR);

            $this->assertSame("mysql", $mysql["pre_requisit"]);
            $this->assertSame("postgres", $postgres["pre_requisit"]);
            $this->assertNotSame($mysql["refkey"], $postgres["refkey"]);
            $this->assertSame($mysql["title"], $postgres["title"]);
            $this->assertCount(count($mysql["blocks"]), $postgres["blocks"]);

            foreach ($postgres["blocks"] as $blockindex => $block) {
                $sourceblock = $mysql["blocks"][$blockindex];
                $this->assertNotSame($sourceblock["refkey"], $block["refkey"]);
                $this->assertCount(count($sourceblock["elements"]), $block["elements"]);
                foreach ($block["elements"] as $index => $element) {
                    $this->assertNotSame($sourceblock["elements"][$index]["refkey"], $element["refkey"]);
                    $this->assertNotEmpty($element["commandsql"]);
                    $this->assertDoesNotMatchRegularExpression(
                        "/\b(?:UNIX_TIMESTAMP|FROM_UNIXTIME|GROUP_CONCAT|DATE_FORMAT|TIME_TO_SEC|" .
                        "DATEDIFF|WEEKDAY|DAYOFMONTH|PERIOD_DIFF|CURDATE)\s*\(/i",
                        $element["commandsql"]
                    );
                    $allkeys[] = $element["refkey"];
                }
                $allkeys[] = $block["refkey"];
            }
            $allkeys[] = $postgres["refkey"];
        }
        $this->assertCount(count(array_unique($allkeys)), $allkeys);
        foreach ($allkeys as $refkey) {
            $this->assertLessThanOrEqual(50, strlen($refkey));
        }
    }

    /**
     * Shared dashboards must preserve MySQL and PostgreSQL blocks independently.
     *
     * @return void
     */
    public function test_shared_dashboards_have_both_block_variants(): void {
        foreach ([1, 4, 8] as $number) {
            $path = __DIR__ . "/../db/files/page-" . sprintf("%03d", $number) . ".json";
            $page = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $mysql = [];
            $postgres = [];
            foreach ($page["blocks"] as $block) {
                if (($block["pre_requisit"] ?? "") === "mysql") {
                    $mysql[] = $block;
                } else if (($block["pre_requisit"] ?? "") === "postgres") {
                    $postgres[] = $block;
                }
            }
            $this->assertNotEmpty($mysql);
            $this->assertCount(count($mysql), $postgres);
            foreach ($postgres as $block) {
                $this->assertStringEndsWith("_pg", $block["refkey"]);
                foreach ($block["elements"] as $element) {
                    $this->assertSame("postgres", $element["pre_requisit"]);
                    $this->assertStringEndsWith("_pg", $element["refkey"]);
                }
            }
        }
    }

    /**
     * The installer must skip definitions for the other database family.
     *
     * @return void
     */
    public function test_import_selects_only_current_database_family(): void {
        global $DB;

        $this->resetAfterTest();
        foreach (["local_kopere_bi_element", "local_kopere_bi_block",
            "local_kopere_bi_page", "local_kopere_bi_cat"] as $table) {
            $DB->delete_records($table);
        }

        $path = __DIR__ . "/../db/files/";
        reports::from_file($path . "page-012.json");
        reports::from_file($path . "page-012-postgres.json");
        reports::from_file($path . "page-008.json");

        $mysqlpage = "kbi_page_inactive_users_in_system_inact";
        $postgrespage = $mysqlpage . "_pg";
        $ispostgres = $DB->get_dbfamily() === "postgres";

        $this->assertSame(!$ispostgres, $DB->record_exists(
            "local_kopere_bi_page", ["refkey" => $mysqlpage]
        ));
        $this->assertSame($ispostgres, $DB->record_exists(
            "local_kopere_bi_page", ["refkey" => $postgrespage]
        ));
        $this->assertSame(!$ispostgres, $DB->record_exists(
            "local_kopere_bi_block", ["refkey" => "dashboard-1"]
        ));
        $this->assertSame($ispostgres, $DB->record_exists(
            "local_kopere_bi_block", ["refkey" => "dashboard-1_pg"]
        ));
    }
}
