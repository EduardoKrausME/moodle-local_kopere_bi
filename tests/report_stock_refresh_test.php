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
use local_kopere_bi\upgrade\report_stock_refresh;

/**
 * Regression coverage for migration of untouched default reports.
 * @package local_kopere_bi
 */
/** @covers \\local_kopere_bi\\upgrade\\report_stock_refresh */
final class report_stock_refresh_test extends advanced_testcase {
    /**
     * Install a historical stock report in the isolated test database.
     *
     * @return array Legacy element ID and saved stock fixture.
     */
    private function install_legacy_fixture(): array {
        global $DB;
        $this->resetAfterTest();
        foreach (["local_kopere_bi_element", "local_kopere_bi_block",
                "local_kopere_bi_page", "local_kopere_bi_cat"] as $table) {
            $DB->delete_records($table);
        }
        $legacy = json_decode(file_get_contents(__DIR__ . "/fixtures/stock_page_076.json"),
            true, 512, JSON_THROW_ON_ERROR)["blocks"][0]["elements"][0];
        $cat = $DB->insert_record("local_kopere_bi_cat",
            (object)["refkey" => "refresh_test_cat", "title" => "Refresh test"]);
        $page = $DB->insert_record("local_kopere_bi_page",
            (object)["cat_id" => $cat, "refkey" => "refresh_test_page", "title" => "Refresh test"]);
        $block = $DB->insert_record("local_kopere_bi_block",
            (object)["page_id" => $page, "refkey" => "refresh_test_block", "type" => "block-1"]);
        $id = $DB->insert_record("local_kopere_bi_element", (object)[
            "block_id" => $block, "refkey" => $legacy["refkey"],
            "title" => "Default report", "type" => "table",
            "commandsql" => $legacy["commandsql"],
            "info" => json_encode($legacy["info"], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
        return [$id, $legacy];
    }

    /**
     * Check the manifest keys, targets and historical test fixture hashes.
     */
    public function test_stock_manifest_and_fixture(): void {
        $signatures = report_stock_refresh::stock_signatures();
        $this->assertCount(65, $signatures);
        foreach ($signatures as $ref => [$suffix, $sqlhash, $infohash]) {
            $this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $sqlhash);
            $this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $infohash);
            $json = json_decode(file_get_contents(__DIR__ . "/../db/files/page-{$suffix}.json"),
                true, 512, JSON_THROW_ON_ERROR);
            $keys = [];
            foreach ($json["blocks"] as $block) {
                foreach ($block["elements"] as $element) {
                    $keys[] = $element["refkey"];
                }
            }
            $this->assertContains($ref, $keys, $suffix);
        }
        $legacy = json_decode(file_get_contents(__DIR__ . "/fixtures/stock_page_076.json"),
            true, 512, JSON_THROW_ON_ERROR)["blocks"][0]["elements"][0];
        [$suffix, $sqlhash, $infohash] = $signatures[$legacy["refkey"]];
        $this->assertSame("076", $suffix);
        $this->assertSame($sqlhash, hash("crc32b", $legacy["commandsql"]));
        $this->assertSame($infohash, hash("crc32b", json_encode($legacy["info"]["column"],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)));
    }

    /**
     * Stock SQL/columns update once; dry-run never writes.
     */
    public function test_default_upgrade_is_idempotent(): void {
        global $DB;
        [$id, $legacy] = $this->install_legacy_fixture();
        $this->assertSame(1, report_stock_refresh::refresh(true)["updated"]);
        $this->assertSame($legacy["commandsql"],
            $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $id]));
        $this->assertSame(1, report_stock_refresh::refresh()["updated"]);
        $expected = json_decode(file_get_contents(__DIR__ . "/../db/files/page-076.json"),
            true, 512, JSON_THROW_ON_ERROR)["blocks"][0]["elements"][0];
        $stored = $DB->get_record("local_kopere_bi_element", ["id" => $id], "*", MUST_EXIST);
        $this->assertSame($expected["commandsql"], $stored->commandsql);
        $this->assertEquals($expected["info"]["column"],
            json_decode($stored->info, true, 512, JSON_THROW_ON_ERROR)["column"]);
        $this->assertSame(0, report_stock_refresh::refresh()["updated"]);
        $this->assertSame(1, report_stock_refresh::refresh()["current"]);
    }

    /**
     * Local SQL changes have priority over stock migrations.
     */
    public function test_custom_sql_is_preserved(): void {
        global $DB;
        [$id] = $this->install_legacy_fixture();
        $DB->set_field("local_kopere_bi_element", "commandsql", "SELECT 456 AS id", ["id" => $id]);
        $this->assertSame(1, report_stock_refresh::refresh()["preserved"]);
        $this->assertSame("SELECT 456 AS id",
            $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $id]));
    }

    /**
     * Even standard SQL must not override altered local column layouts.
     */
    public function test_custom_columns_are_preserved(): void {
        global $DB;
        [$id, $legacy] = $this->install_legacy_fixture();
        $legacy["info"]["column"]["firstname"]["mustache"] = "<b>Custom</b>";
        $info = json_encode($legacy["info"], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $DB->set_field("local_kopere_bi_element", "info", $info, ["id" => $id]);
        $this->assertSame(1, report_stock_refresh::refresh()["preserved"]);
        $this->assertSame($info, $DB->get_field("local_kopere_bi_element", "info", ["id" => $id]));
        $this->assertSame($legacy["commandsql"],
            $DB->get_field("local_kopere_bi_element", "commandsql", ["id" => $id]));
    }

    /**
     * Reconcile old stock columns even if default SQL was already refreshed.
     */
    public function test_refreshes_old_columns_on_current_sql(): void {
        global $DB;
        [$id] = $this->install_legacy_fixture();
        $latest = json_decode(file_get_contents(__DIR__ . "/../db/files/page-076.json"),
            true, 512, JSON_THROW_ON_ERROR)["blocks"][0]["elements"][0]["commandsql"];
        $DB->set_field("local_kopere_bi_element", "commandsql", $latest, ["id" => $id]);
        $this->assertSame(1, report_stock_refresh::refresh()["updated"]);
        $info = json_decode($DB->get_field("local_kopere_bi_element", "info", ["id" => $id]),
            true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey("replies_received", $info["column"]);
    }
}
