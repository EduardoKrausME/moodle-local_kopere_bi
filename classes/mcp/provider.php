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

namespace local_kopere_bi\mcp;

use local_mcp\extension\read_provider_interface;

/**
 * Auto-discovered by local_mcp from classes/mcp/provider.php.
 *
 * Every report page registered in the database is an independent READ tool.
 * There is no hardcoded list, lib.php callback, or local_mcp modification.
 */
final class provider implements read_provider_interface {
    /**
     * @return \local_mcp\read\tool_interface[]
     */
    public function get_read_tools(): array {
        $tools = [new list_reports()];
        foreach (report_service::visible_pages() as $page) {
            $tools[] = new report_tool($page);
        }
        return $tools;
    }
}
