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
 * Tests for the read-only CLI interface decisions and output formats.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\audit\report_inventory;

/**
 * Test isolated CLI helpers without running the Moodle CLI.
 *
 * @covers \local_kopere_bi\audit\report_inventory
 */
final class report_inventory_cli_test extends advanced_testcase {
    /**
     * Valid text and JSON formats render identical audit statuses.
     */
    public function test_text_and_json_output(): void {
        $audit = [
            'audit' => ['database' => 'postgres'],
            'summary' => ['CURRENT' => 1],
            'approval' => ['status' => 'PENDING', 'blocking_issues' => []],
            'reports' => [['status' => 'CURRENT', 'refkey' => 'sample']],
        ];
        report_inventory::validate_options(['format' => 'text'], []);
        report_inventory::validate_options(['format' => 'json'], []);
        $text = report_inventory::render($audit, 'text');
        $json = report_inventory::render($audit, 'json');
        $this->assertStringContainsString('CURRENT: 1', $text);
        $this->assertStringContainsString('CURRENT', $json);
        $this->assertSame($audit, json_decode($json, true, 512, JSON_THROW_ON_ERROR));
        $this->assertSame(0, report_inventory::exit_code($audit, true));
    }

    /**
     * Strict status is non-zero only for blocking issues.
     */
    public function test_strict_exit_code(): void {
        $audit = ['approval' => ['blocking_issues' => []]];
        $this->assertSame(0, report_inventory::exit_code($audit, true));
        $audit['approval']['blocking_issues'][] = ['code' => 'MISSING'];
        $this->assertSame(2, report_inventory::exit_code($audit, true));
        $this->assertSame(0, report_inventory::exit_code($audit, false));
    }

    /**
     * Unknown output types are rejected before any database read.
     */
    public function test_invalid_format(): void {
        $this->expectException(\InvalidArgumentException::class);
        report_inventory::validate_options(['format' => 'xml'], []);
    }

    /**
     * Unknown command-line arguments are rejected.
     */
    public function test_unrecognized_arguments(): void {
        $this->expectException(\InvalidArgumentException::class);
        report_inventory::validate_options(['format' => 'json'], ['--write']);
    }
}
