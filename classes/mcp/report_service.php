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
 * Service for MCP discovery and execution of Kopere BI reports.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\mcp;

use context_course;
use context_system;
use InvalidArgumentException;
use local_kopere_bi\block\util\string_util;
use local_kopere_bi\feature;
use local_kopere_bi\page_access;
use local_kopere_bi\report_columns;
use local_kopere_bi\vo\external_report;
use local_mcp\security\authenticated_identity;
use moodle_exception;
use Throwable;

/**
 * Run the existing Kopere BI report definitions as structured, paginated data.
 *
 * This service never evaluates templates, takes SQL from clients, or changes the
 * database connection to read-only globally. Only saved report SQL is executed.
 */
final class report_service {
    /** @var int Default rows returned per element. */
    private const DEFAULT_LIMIT = 50;

    /** @var int Maximum rows returned per element. */
    private const MAX_LIMIT = 200;

    /**
     * Page discovery is dynamic: newly created reports become tools immediately.
     *
     * @return object[]
     */
    public static function visible_pages(): array {
        global $DB;

        $pages = $DB->get_records('local_kopere_bi_page', [], 'sortorder ASC, id ASC');
        $visible = [];
        foreach ($pages as $page) {
            if (self::visible($page)) {
                $visible[] = $page;
            }
        }
        return $visible;
    }

    /**
     * Return pages with unique MCP-compatible refkeys.
     *
     * Pages with an empty, invalid, or duplicate refkey are not exposed. No
     * numeric database ID is used as a fallback in the public API.
     *
     * @return object[]
     */
    public static function discovered_pages(): array {
        $pages = self::visible_pages();
        $counts = [];

        foreach ($pages as $page) {
            $refkey = trim((string)($page->refkey ?? ''));
            if (!preg_match('/^[A-Za-z0-9_.-]{1,100}$/D', $refkey)) {
                continue;
            }
            $name = self::tool_name($page);
            $counts[$name] = ($counts[$name] ?? 0) + 1;
        }

        $discovered = [];
        foreach ($pages as $page) {
            $name = self::tool_name($page);
            if (($counts[$name] ?? 0) === 1) {
                $discovered[] = $page;
            }
        }

        return $discovered;
    }

    /**
     * Build the stable MCP tool name from the report refkey.
     *
     * @param object $page Stored BI report page.
     * @return string
     */
    public static function tool_name(object $page): string {
        return 'kopere_bi_report_' . trim((string)($page->refkey ?? ''));
    }

    /**
     * Check whether a report is visible to the current user.
     *
     * @param object $page
     * @return bool
     */
    private static function visible(object $page): bool {
        global $DB;

        if (!feature::page_is_available($page) || !page_access::can_view($page)) {
            return false;
        }
        // Reuse category metadata during one discovery request to avoid N+1 queries.
        static $categorycache = [];
        $categoryid = (int)$page->cat_id;
        if (!array_key_exists($categoryid, $categorycache)) {
            $categorycache[$categoryid] = $DB->get_record('local_kopere_bi_cat', ['id' => $categoryid]);
        }
        return $categorycache[$categoryid] && feature::category_is_available($categorycache[$categoryid]);
    }

    /**
     * Return only display metadata, never SQL or custom HTML from the report.
     *
     * @param object $page
     * @return array
     */
    public static function describe(object $page): array {
        global $DB;

        $category = $DB->get_record('local_kopere_bi_cat', ['id' => $page->cat_id]);
        return [
            'id' => (int)$page->id,
            'refkey' => (string)($page->refkey ?? ''),
            'title' => string_util::get_string((string)$page->title),
            'description' => string_util::get_string((string)($page->description ?? '')),
            'category' => $category ? string_util::get_string((string)$category->title) : '',
            'tool' => self::tool_name($page),
        ];
    }

    /**
     * Execute a report using its internal numeric database ID.
     *
     * @param int $pageid Report identifier resolved internally.
     * @param array $arguments Validated MCP input.
     * @param authenticated_identity $identity Connected user.
     * @return array
     */
    public static function execute(int $pageid, array $arguments, authenticated_identity $identity): array {
        global $DB, $COURSE;

        $page = $DB->get_record('local_kopere_bi_page', ['id' => $pageid], '*', MUST_EXIST);
        if (!self::visible($page)) {
            throw new moodle_exception('nopermissions', 'error', '', 'Kopere BI report');
        }
        page_access::require_view($page, context_system::instance());

        $limit = (int)($arguments['limit'] ?? self::DEFAULT_LIMIT);
        $offset = (int)($arguments['offset'] ?? 0);
        if ($limit < 1 || $limit > self::MAX_LIMIT || $offset < 0 || $offset > 100000) {
            throw new InvalidArgumentException('Invalid report pagination parameters.');
        }

        $manager = has_capability('local/kopere_bi:manage', context_system::instance(), $identity->userid);
        $userid = (int)($arguments['user_id'] ?? $identity->userid);
        if ($userid < 1 || (!$manager && $userid !== $identity->userid)) {
            throw new moodle_exception('nopermissions', 'error', '', 'Report user filter');
        }
        $courseid = (int)($arguments['course_id'] ?? ($COURSE->id ?? SITEID));
        if ($courseid < 1 || !$DB->record_exists('course', ['id' => $courseid])) {
            throw new InvalidArgumentException('Invalid course filter.');
        }
        if (!$manager && $courseid !== SITEID) {
            $coursecontext = context_course::instance($courseid);
            if (!is_enrolled($coursecontext, $identity->userid) &&
                    !has_capability('moodle/course:view', $coursecontext, $identity->userid)) {
                throw new moodle_exception('nopermissions', 'error', '', 'Report course filter');
            }
        }
        $cohortid = (int)($arguments['cohort_id'] ?? 0);
        if ($cohortid < 0 || (!$manager && $cohortid > 0)) {
            throw new moodle_exception('nopermissions', 'error', '', 'Report cohort filter');
        }
        if ($cohortid && !$DB->record_exists('cohort', ['id' => $cohortid])) {
            throw new InvalidArgumentException('Invalid cohort filter.');
        }

        $result = self::describe($page);
        $result['filters'] = ['user_id' => $userid, 'course_id' => $courseid, 'cohort_id' => $cohortid];
        $result['limit'] = $limit;
        $result['offset'] = $offset;
        $result['blocks'] = [];

        $blocks = $DB->get_records('local_kopere_bi_block', ['page_id' => $pageid], 'sequence ASC, id ASC');
        foreach ($blocks as $block) {
            $elements = $DB->get_records('local_kopere_bi_element',
                ['block_id' => $block->id], 'block_num ASC, id ASC');
            $output = [
                'id' => (int)$block->id,
                'refkey' => (string)($block->refkey ?? ''),
                'type' => (string)$block->type,
                'elements' => [],
            ];
            foreach ($elements as $element) {
                $output['elements'][] = self::element_data($element, $userid, $courseid,
                    $cohortid, $limit, $offset, $manager, $identity->userid);
            }
            $result['blocks'][] = $output;
        }
        return $result;
    }

    /**
     * Return a single report element as a structured, paginated dataset.
     *
     * @param object $element Saved Kopere BI element.
     * @param int $userid
     * @param int $courseid
     * @param int $cohortid
     * @param int $limit
     * @param int $offset
     * @param bool $showdetails Whether the user can inspect technical errors.
     * @return array
     */
    private static function element_data(object $element, int $userid, int $courseid,
            int $cohortid, int $limit, int $offset, bool $showdetails, int $viewerid): array {
        $output = [
            'id' => (int)$element->id,
            'refkey' => (string)($element->refkey ?? ''),
            'title' => string_util::get_string((string)($element->title ?? '')),
            'type' => (string)$element->type,
            'columns' => [],
            'rows' => [],
            'row_count' => 0,
            'has_more' => false,
        ];
        $source = trim((string)($element->commandsql ?? ''));
        if ($source === '') {
            $output['status'] = 'no_data_source';
            return $output;
        }

        try {
            $rows = self::rows($source, $userid, $courseid, $cohortid, $limit, $offset);
            $output['has_more'] = count($rows) > $limit;
            $details = json_decode((string)($element->info ?? ''), true);
            $definitions = is_array($details) ? ($details['column'] ?? []) : [];
            $visible = array_slice($rows, 0, $limit);
            if ($element->type === 'table') {
                [$visible, $definitions] = report_columns::prepare($visible,
                    is_array($definitions) ? $definitions : [], $source,
                    is_array($details) ? $details : [], $viewerid);
            }
            $output['rows'] = array_map(static function($row) {
                return (array)$row;
            }, $visible);
            $output['row_count'] = count($output['rows']);
            $keys = $output['rows'] ? array_keys($output['rows'][0]) :
                (is_array($definitions) ? array_keys($definitions) : []);
            foreach ($keys as $key) {
                $column = is_array($definitions) ? ($definitions[$key] ?? []) : [];
                $output['columns'][] = [
                    'key' => (string)$key,
                    'title' => isset($column['title']) ?
                        string_util::get_string((string)$column['title']) : (string)$key,
                ];
            }
            if ($element->type === 'info' && $output['rows']) {
                $output['value'] = reset($output['rows'][0]);
            }
            $output['status'] = 'ok';
        } catch (Throwable $e) {
            // Keep the generic error for readers. Only BI managers may inspect diagnostics.
            debugging("Kopere BI MCP element " . (int)$element->id . ": " .
                get_class($e) . ": " . $e->getMessage(), DEBUG_DEVELOPER);
            $output["status"] = "error";
            $output["error"] = "Unable to execute this report data source.";
            if ($showdetails) {
                $output["error_details"] = [
                    "exception" => get_class($e),
                    "message" => $e->getMessage(),
                    "code" => (string)$e->getCode(),
                ];
                // Moodle database exceptions keep the driver error in debuginfo.
                // Include only its first line; never expose SQL or bound parameters.
                if ($e instanceof \moodle_exception && !empty($e->debuginfo)) {
                    $firstline = strtok((string)$e->debuginfo, "\r\n");
                    if ($firstline !== false && trim($firstline) !== "") {
                        $output["error_details"]["database_error"] = mb_substr(trim($firstline), 0, 500);
                    }
                }
            }
        }
        return $output;
    }

    /**
     * Execute an existing query, with named filter bindings and per-element limits.
     * Report classes are limited to the plugin's external_report contract.
     *
     * @param string $source Existing saved SQL or PHP report class.
     * @param int $userid
     * @param int $courseid
     * @param int $cohortid
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private static function rows(string $source, int $userid, int $courseid, int $cohortid,
            int $limit, int $offset): array {
        global $DB, $CFG;

        if (str_starts_with($source, '\\')) {
            if (!preg_match('/^\\\\[A-Za-z_][A-Za-z0-9_\\\\]*$/D', $source) ||
                    !is_subclass_of($source, external_report::class)) {
                throw new InvalidArgumentException('Unsupported BI report data source.');
            }
            $rows = $source::get_data($courseid, $userid);
            if (!is_array($rows)) {
                throw new InvalidArgumentException('Invalid BI report class result.');
            }
            $rows = array_values($rows);
            $rows = array_slice($rows, $offset, $limit + 1);
        } else {
            $sql = trim($source);
            $sql = preg_replace('/;\\s*$/', '', $sql);
            // Restrict the API to a single read query; never run report DDL/DML.
            if (!preg_match('/^(SELECT|WITH)\\s/i', $sql) || str_contains($sql, ';')) {
                throw new InvalidArgumentException('Only read-only report SQL is supported.');
            }
            $sql = preg_replace('/\\{([a-z][a-z0-9_]*)\\}/', $CFG->prefix . '$1', $sql);
            if ($CFG->prefix !== 'mdl_') {
                $sql = preg_replace('/\\bmdl_([a-z][a-z0-9_]*)\\b/', $CFG->prefix . '$1', $sql);
            }
            $sql = preg_replace('/UNIX_TIMESTAMP\\s*\\(\\s*\\)/i', (string)time(), $sql);
            $sql = preg_replace("/\\bAS\\s+'([^']+)'/i", 'AS "$1"', $sql);

            $values = ['userid' => $userid, 'courseid' => $courseid, 'cohortid' => $cohortid];
            $params = [];
            $num = 0;
            $sql = preg_replace_callback('/:(userid|courseid|cohortid)\\b/i',
                static function(array $match) use ($values, &$params, &$num): string {
                    $name = strtolower($match[1]);
                    $bindname = 'kopere_mcp_' . $name . '_' . (++$num);
                    $params[$bindname] = $values[$name];
                    return ':' . $bindname;
                }, $sql);

            // A derived table allows Moodle to apply its own portable pagination even
            // when a saved dashboard query already includes a trailing LIMIT clause.
            $statement = 'SELECT * FROM (' . $sql . ') kopere_mcp_report';
            $recordset = $DB->get_recordset_sql($statement, $params, $offset, $limit + 1);
            $rows = [];
            try {
                foreach ($recordset as $record) {
                    $rows[] = (array)$record;
                }
            } finally {
                $recordset->close();
            }
        }

        return array_map(static function($row): array {
            return (array)$row;
        }, $rows);
    }
}
