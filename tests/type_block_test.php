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

namespace local_kopere_bi;

use advanced_testcase;

/**
 * Regression tests for selecting installed dashboard block types.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_kopere_bi\type_block
 */
final class type_block_test extends advanced_testcase {
    /**
     * All bundled chart and content block types must appear with their provider metadata.
     */
    public function test_bundled_types_use_their_biblocks_providers(): void {
        $types = ["pie", "line", "area", "column", "table", "maps", "info", "html"];
        $buttons = type_block::get_type_buttons(42, 3);

        $this->assertSame($types, array_column($buttons, "id"));
        foreach ($buttons as $button) {
            $type = $button["id"];
            $provider = "\\biblocks_{$type}\\provider";

            $this->assertSame($provider::get_name(), $button["title"]);
            $this->assertSame($provider::get_description(), $button["description"]);
            $this->assertSame(
                "?classname=dashboard&method=type_block_edit&block_id=42&block_num=3&type={$type}",
                $button["link"]
            );
        }
    }

    /**
     * Uninstalled providers should not break the selection screen or hide installed types.
     */
    public function test_missing_provider_is_skipped_without_hiding_html(): void {
        $buttons = type_block::get_type_buttons(7, 2, ["missing", "pie", "html", "also_missing"]);

        $this->assertSame(["pie", "html"], array_column($buttons, "id"));
        $this->assertCount(2, $buttons);
        $this->assertSame(
            "?classname=dashboard&method=type_block_edit&block_id=7&block_num=2&type=html",
            $buttons[1]["link"]
        );
    }
}
