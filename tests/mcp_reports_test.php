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
//
// @package local_kopere_bi
// @copyright 2026 Eduardo Kraus
// @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\mcp\provider;
use local_mcp\read\tool_interface;
use local_mcp\security\authenticated_identity;

defined('MOODLE_INTERNAL') || die();

/**
 * MCP auto-discovery and per-report structured data integration.
 *
 * local_mcp is optional. The test runs when both plugins are installed.
 */
final class mcp_reports_test extends advanced_testcase {
    /**
     * @return void
     */
    public function test_discovery_and_filtered_data(): void {
        global $DB;

        if (!interface_exists(tool_interface::class)) {
            $this->markTestSkipped('local_mcp is not installed.');
        }

        $this->resetAfterTest();
        $this->setAdminUser();

        $category = (object)[
            'refkey' => 'mcp_test_category',
            'title' => 'MCP test category',
            'sortorder' => 9999,
        ];
        $categoryid = $DB->insert_record('local_kopere_bi_cat', $category);
        $page = (object)[
            'refkey' => 'mcp_test_report',
            'cat_id' => $categoryid,
            'title' => 'MCP test report',
            'description' => 'Integration test report',
            'sortorder' => 9999,
            'time' => time(),
        ];
        $pageid = $DB->insert_record('local_kopere_bi_page', $page);
        $blockid = $DB->insert_record('local_kopere_bi_block', (object)[
            'page_id' => $pageid,
            'refkey' => 'mcp_test_block',
            'type' => 'block-1',
            'sequence' => 1,
            'time' => time(),
        ]);
        $DB->insert_record('local_kopere_bi_element', (object)[
            'refkey' => 'mcp_test_element',
            'title' => 'Users',
            'block_id' => $blockid,
            'block_num' => 1,
            'type' => 'table',
            'commandsql' => 'SELECT id, username FROM {user} WHERE id = :userid OR id = :userid',
            'info' => '{}',
            'time' => time(),
        ]);

        $tools = (new provider())->get_read_tools();
        $indexed = [];
        foreach ($tools as $tool) {
            $this->assertInstanceOf(tool_interface::class, $tool);
            $indexed[$tool->get_name()] = $tool;
        }
        $this->assertArrayHasKey('kopere_bi_list_reports', $indexed);
        $this->assertArrayHasKey('kopere_bi_report_' . $pageid, $indexed);

        $identity = new authenticated_identity((int)get_admin()->id, 'test', ['mcp:read']);
        $result = $indexed['kopere_bi_report_' . $pageid]->execute(['limit' => 1], $identity);
        $this->assertSame($pageid, $result['id']);
        $this->assertCount(1, $result['blocks']);
        $element = $result['blocks'][0]['elements'][0];
        $this->assertSame('ok', $element['status']);
        $this->assertSame(1, $element['row_count']);
        $this->assertSame((int)get_admin()->id, (int)$element['rows'][0]['id']);
        $this->assertArrayNotHasKey('commandsql', $element);

        $listing = $indexed['kopere_bi_list_reports']->execute([], $identity);
        $this->assertContains('kopere_bi_report_' . $pageid,
            array_column($listing['reports'], 'tool'));
    }

    /**
     * @return void
     */
    public function test_non_privileged_user_cannot_discover_report(): void {
        global $DB;

        if (!interface_exists(tool_interface::class)) {
            $this->markTestSkipped('local_mcp is not installed.');
        }
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $categoryid = $DB->insert_record('local_kopere_bi_cat', (object)[
            'title' => 'Private category',
            'refkey' => 'mcp_test_private_category',
        ]);
        $pageid = $DB->insert_record('local_kopere_bi_page', (object)[
            'cat_id' => $categoryid,
            'title' => 'Private BI report',
            'refkey' => 'mcp_test_private_report',
        ]);

        $names = array_map(static function(tool_interface $tool): string {
            return $tool->get_name();
        }, (new provider())->get_read_tools());
        $this->assertNotContains('kopere_bi_report_' . $pageid, $names);
    }
}
