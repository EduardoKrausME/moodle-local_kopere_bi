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
 * Isolated regression tests for the read-only report inventory.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\audit\report_inventory;
use local_kopere_bi\upgrade\report_stock_refresh;

/**
 * Audit stock and customised reports without database mutations.
 *
 * @covers \local_kopere_bi\audit\report_inventory
 */
final class report_inventory_test extends advanced_testcase {
    /**
     * Load a stock element and historical installation sample.
     *
     * @return array
     */
    private function reports(): array {
        $dir = __DIR__ . '/../db/files/';
        $stock = json_decode(file_get_contents($dir . 'page-076.json'), true, 512, JSON_THROW_ON_ERROR);
        $legacy = json_decode(file_get_contents(__DIR__ . '/fixtures/stock_page_076.json'),
            true, 512, JSON_THROW_ON_ERROR);
        return [$stock['blocks'][0]['elements'][0], $legacy['blocks'][0]['elements'][0]];
    }

    /**
     * Build a fixture using the exact current stock definition.
     *
     * @return array
     */
    private function setup_sample(): array {
        [$stock, $legacy] = $this->reports();
        $catalogue = ['pages' => ['testpage' => ['refkey' => 'testpage']],
            'blocks' => ['testblock' => ['refkey' => 'testblock']],
            'elements' => [$stock['refkey'] => [
                'refkey' => $stock['refkey'], 'file' => 'page-076.json',
                'page_refkey' => 'testpage', 'block_refkey' => 'testblock',
                'sql' => $stock['commandsql'], 'columns' => $stock['info']['column'],
            ]], 'allrefs' => [$stock['refkey'] => true, 'other_pg' => true], 'issues' => []];
        $installed = [
            'cat' => [(object)['id' => 1, 'refkey' => 'testcat']],
            'page' => [(object)['id' => 2, 'refkey' => 'testpage', 'cat_id' => 1]],
            'block' => [(object)['id' => 3, 'refkey' => 'testblock', 'page_id' => 2]],
            'element' => [(object)['id' => 4, 'refkey' => $stock['refkey'], 'block_id' => 3,
                'commandsql' => $legacy['commandsql'],
                'info' => json_encode($legacy['info'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]],
        ];
        return [$catalogue, $installed, $stock, $legacy];
    }

    /**
     * Only compatible elements of mixed-engine dashboards belong to the catalogue.
     */
    public function test_catalogue_selects_database_variants(): void {
        $mysql = report_inventory::catalogue(__DIR__ . '/../db/files', 'mysql');
        $postgres = report_inventory::catalogue(__DIR__ . '/../db/files', 'postgres');
        $this->assertArrayHasKey('report_3_users_online', $mysql['elements']);
        $this->assertArrayNotHasKey('report_3_users_online_pg', $mysql['elements']);
        $this->assertArrayHasKey('report_3_users_online_pg', $postgres['elements']);
        $this->assertArrayNotHasKey('report_3_users_online', $postgres['elements']);
        $this->assertArrayHasKey('report_3_top_browsers', $mysql['elements']);
        $this->assertArrayHasKey('report_3_top_browsers', $postgres['elements']);
        $this->assertArrayHasKey('report_3_users_online_pg', $mysql['allrefs']);
    }

    /**
     * A historical stock row must produce the same eligibility as migration 2026102500.
     */
    public function test_legacy_and_current_classification(): void {
        [$catalogue, $installed, $stock] = $this->setup_sample();
        $manifest = report_stock_refresh::stock_signatures();
        $result = report_inventory::inspect($catalogue, $installed, $manifest, 'mysql');
        $this->assertSame(1, $result['summary']['WOULD_UPDATE']);
        $this->assertSame(0, $result['summary']['MISSING']);
        $this->assertSame('RECOGNIZED_STOCK', $result['reports'][0]['reason']);
        $this->assertContains('replies_received', $result['reports'][0]['columns']['diff']['added']);
        $installed['element'][0]->commandsql = $stock['commandsql'];
        $installed['element'][0]->info = json_encode($stock['info'], JSON_THROW_ON_ERROR);
        $current = report_inventory::inspect($catalogue, $installed, $manifest, 'mysql');
        $this->assertSame(1, $current['summary']['CURRENT']);
    }

    /**
     * SQL and presentation changes always prevent simulated stock upgrades.
     */
    public function test_custom_sql_and_columns_are_preserved(): void {
        [$catalogue, $installed] = $this->setup_sample();
        $manifest = report_stock_refresh::stock_signatures();
        $installed['element'][0]->commandsql = 'SELECT 555 AS id';
        $result = report_inventory::inspect($catalogue, $installed, $manifest, 'mysql');
        $this->assertSame('PRESERVED', $result['reports'][0]['status']);
        $this->assertSame('UNRECOGNIZED_SQL', $result['reports'][0]['reason']);
        $this->assertSame('BLOCKED', $result['approval']['status']);
        $installed['element'][0]->commandsql = $this->reports()[1]['commandsql'];
        $info = json_decode($installed['element'][0]->info, true);
        $info['column']['firstname']['mustache'] = '<b>Institution-specific</b>';
        $installed['element'][0]->info = json_encode($info, JSON_THROW_ON_ERROR);
        $result = report_inventory::inspect($catalogue, $installed, $manifest, 'mysql');
        $this->assertSame('UNRECOGNIZED_COLUMNS', $result['reports'][0]['reason']);
        $this->assertNotEmpty($result['reports'][0]['columns']['diff']['modified']);
    }

    /**
     * Missing pages, duplicate keys and orphan records are independent blockers.
     */
    public function test_missing_duplicate_and_orphan_inventory(): void {
        [$catalogue, $installed] = $this->setup_sample();
        $installed['element'] = [];
        $missing = report_inventory::inspect($catalogue, $installed, [], 'mysql');
        $this->assertSame(1, $missing['summary']['MISSING']);
        $installed['element'] = [(object)['id' => 4, 'refkey' => 'other_pg', 'block_id' => 999]];
        $foreign = report_inventory::inspect($catalogue, $installed, [], 'mysql');
        $this->assertSame(1, $foreign['summary']['OTHER_ENGINE']);
        $this->assertContains('ORPHAN_ELEMENT', array_column($foreign['issues'], 'code'));
        [$catalogue, $installed] = $this->setup_sample();
        $installed['element'][] = clone $installed['element'][0];
        $installed['element'][1]->id = 5;
        $duplicate = report_inventory::inspect($catalogue, $installed, [], 'mysql');
        $this->assertSame(1, $duplicate['summary']['DUPLICATE']);
        $this->assertContains('DUPLICATE_INSTALLED_REFKEY', array_column($duplicate['issues'], 'code'));
    }

    /**
     * Unknown versions, malformed metadata and non-stock reports are distinguishable.
     */
    public function test_uncovered_custom_and_invalid_metadata(): void {
        [$catalogue, $installed] = $this->setup_sample();
        $notcovered = report_inventory::inspect($catalogue, $installed, [], 'mysql');
        $this->assertSame(1, $notcovered['summary']['NOT_COVERED']);
        $installed['element'][0]->info = 'invalid JSON';
        $invalid = report_inventory::inspect($catalogue, $installed,
            report_stock_refresh::stock_signatures(), 'mysql');
        $this->assertSame(1, $invalid['summary']['INVALID_INFO']);
        $installed['element'][] = (object)['id' => 6, 'refkey' => 'institution_custom',
            'block_id' => 3, 'commandsql' => 'SELECT 1', 'info' => '{}'];
        $custom = report_inventory::inspect($catalogue, $installed, [], 'mysql');
        $this->assertSame(1, $custom['summary']['CUSTOM_ONLY']);
    }

    /**
     * Summary formats never expose installed SQL, HTML templates or private metadata.
     */
    public function test_json_and_text_outputs_are_read_only(): void {
        [$catalogue, $installed] = $this->setup_sample();
        $sqlbefore = $installed['element'][0]->commandsql;
        $audit = report_inventory::inspect($catalogue, $installed,
            report_stock_refresh::stock_signatures(), 'mysql');
        $json = json_encode($audit, JSON_THROW_ON_ERROR);
        $text = report_inventory::text($audit);
        $this->assertStringContainsString('WOULD_UPDATE', $json);
        $this->assertStringContainsString('WOULD_UPDATE', $text);
        $this->assertStringNotContainsString($sqlbefore, $json);
        $this->assertStringNotContainsString($sqlbefore, $text);
        $this->assertSame($sqlbefore, $installed['element'][0]->commandsql);
        $this->assertSame(0, $audit['audit']['writes']);
        $this->assertSame(64, strlen($audit['reports'][0]['sql']['sha256']));
    }
}
