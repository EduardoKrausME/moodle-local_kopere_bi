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

use context;
use context_system;
use local_kopere_bi\block\util\string_util;
use local_mcp\read\tool_interface;
use local_mcp\security\authenticated_identity;

/**
 * Per-report MCP tool; the identity of an API is its stable database page ID.
 */
final class report_tool implements tool_interface {
    /** @var object Report metadata read during discovery. */
    private object $page;

    public function __construct(object $page) {
        $this->page = $page;
    }

    public function get_name(): string {
        return 'kopere_bi_report_' . (int)$this->page->id;
    }

    public function get_title(): string {
        return 'Kopere BI: ' . string_util::get_string((string)$this->page->title);
    }

    public function get_description(): string {
        $description = trim(strip_tags(string_util::get_string((string)($this->page->description ?? ''))));
        return 'Read structured data from the Kopere BI report "' .
            string_util::get_string((string)$this->page->title) . '". ' .
            ($description === '' ? '' : mb_substr($description, 0, 300) . ' ') .
            'Returns data rows for every SQL-backed chart, KPI and table; '
            . 'limit and offset apply independently to each element.';
    }

    public function get_input_schema(): array {
        return [
            'type' => 'object',
            'properties' => [
                'user_id' => ['type' => 'integer', 'minimum' => 1,
                    'description' => 'Optional :userid filter; defaults to the connected user. '
                        . 'Selecting a different user requires BI manage permission.'],
                'course_id' => ['type' => 'integer', 'minimum' => 1,
                    'description' => 'Optional :courseid filter; defaults to the current course or site.'],
                'cohort_id' => ['type' => 'integer', 'minimum' => 1,
                    'description' => 'Optional :cohortid filter, available to BI managers.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200,
                    'description' => 'Maximum rows returned per element; default 50, maximum 200.'],
                'offset' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100000,
                    'description' => 'Pagination offset applied independently to each element.'],
            ],
            'additionalProperties' => false,
        ];
    }

    public function get_required_capability(): string {
        return 'local/kopere_bi:view';
    }

    public function resolve_context(array $arguments): context {
        return context_system::instance();
    }

    public function execute(array $arguments, authenticated_identity $identity): array {
        return report_service::execute((int)$this->page->id, $arguments, $identity);
    }
}
