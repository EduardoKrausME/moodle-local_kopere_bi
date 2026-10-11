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
/**
 * MCP tool for listing accessible Kopere BI reports.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\mcp;

use context;
use context_system;
use local_mcp\read\tool_interface;
use local_mcp\security\authenticated_identity;

/**
 * Discover the BI reports available to the authenticated user.
 */
final class list_reports implements tool_interface {
    /**
     * Return the listing tool name.
     *
     * @return string
     */
    public function get_name(): string {
        return 'kopere_bi_list_reports';
    }

    /**
     * Return the listing tool title.
     *
     * @return string
     */
    public function get_title(): string {
        return 'List Kopere BI reports';
    }

    /**
     * Describe the report discovery tool.
     *
     * @return string
     */
    public function get_description(): string {
        return 'List Kopere BI report APIs available to the connected user, '
            . 'including report refkeys, IDs, titles, categories, and MCP tool names.';
    }

    /**
     * Return a schema with no required arguments.
     *
     * @return array
     */
    public function get_input_schema(): array {
        return ['type' => 'object', 'properties' => (object)[], 'additionalProperties' => false];
    }

    /**
     * Return the BI view capability.
     *
     * @return string
     */
    public function get_required_capability(): string {
        return 'local/kopere_bi:view';
    }

    /**
     * Resolve the system context.
     *
     * @param array $arguments MCP tool arguments.
     * @return context
     */
    public function resolve_context(array $arguments): context {
        return context_system::instance();
    }

    /**
     * Return accessible reports identified by their refkeys.
     *
     * @param array $arguments MCP tool arguments.
     * @param authenticated_identity $identity Connected user.
     * @return array
     */
    public function execute(array $arguments, authenticated_identity $identity): array {
        $reports = [];
        foreach (report_service::discovered_pages() as $page) {
            $reports[] = report_service::describe($page);
        }
        return ['count' => count($reports), 'reports' => $reports];
    }
}
