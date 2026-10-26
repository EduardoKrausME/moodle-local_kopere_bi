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
 * Read-only catalogue and migration inventory.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\audit;

/**
 * Inspect bundled reports and stored metadata without executing report SQL or writing data.
 */
final class report_inventory {
    /**
     * Determine whether a prerequisite is compatible with the selected database.
     *
     * @param mixed $requirement Database requirement.
     * @param string $family Database family.
     * @return bool
     */
    public static function supports_database($requirement, string $family): bool {
        $requirement = strtolower(trim((string)$requirement));
        $aliases = ['pgsql' => 'postgres', 'postgresql' => 'postgres', 'mariadb' => 'mysql'];
        $requirement = $aliases[$requirement] ?? $requirement;
        return !in_array($requirement, ['mysql', 'postgres'], true) || $requirement === $family;
    }

    /**
     * Read official JSON files, including mixed-engine dashboard blocks.
     *
     * @param string $directory Bundled db/files directory.
     * @param string $family Database family.
     * @return array
     */
    public static function catalogue(string $directory, string $family): array {
        if (!in_array($family, ['mysql', 'postgres'], true)) {
            throw new \InvalidArgumentException('Unsupported database family.');
        }
        $files = glob(rtrim($directory, '/') . '/page-*.json');
        if ($files === false || !$files) {
            throw new \RuntimeException('No bundled page definitions found.');
        }
        sort($files, SORT_STRING);
        $result = ['categories' => [], 'pages' => [], 'blocks' => [],
            'elements' => [], 'allrefs' => [], 'issues' => []];
        foreach ($files as $file) {
            $name = basename($file);
            try {
                $page = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $exception) {
                $result['issues'][] = ['code' => 'INVALID_STOCK_JSON', 'file' => $name];
                continue;
            }
            if (!is_array($page) || empty($page['refkey'])) {
                $result['issues'][] = ['code' => 'INVALID_STOCK_PAGE', 'file' => $name];
                continue;
            }
            $pageref = (string)$page['refkey'];
            $pagesupported = self::supports_database($page['pre_requisit'] ?? null, $family);
            $usableblocks = [];
            foreach ($page['blocks'] ?? [] as $block) {
                if (empty($block['refkey'])) {
                    $result['issues'][] = ['code' => 'INVALID_STOCK_BLOCK', 'file' => $name];
                    continue;
                }
                $blockref = (string)$block['refkey'];
                $blocksupported = $pagesupported && self::supports_database($block['pre_requisit'] ?? null, $family);
                $usableelements = [];
                foreach ($block['elements'] ?? [] as $element) {
                    if (empty($element['refkey'])) {
                        $result['issues'][] = ['code' => 'INVALID_STOCK_ELEMENT', 'file' => $name];
                        continue;
                    }
                    $ref = (string)$element['refkey'];
                    $result['allrefs'][$ref] = true;
                    if (!$blocksupported || !self::supports_database($element['pre_requisit'] ?? null, $family)) {
                        continue;
                    }
                    if (isset($result['elements'][$ref])) {
                        $result['issues'][] = ['code' => 'DUPLICATE_STOCK_REFKEY', 'refkey' => $ref];
                        continue;
                    }
                    $result['elements'][$ref] = [
                        'refkey' => $ref, 'file' => $name, 'page_refkey' => $pageref,
                        'block_refkey' => $blockref, 'type' => (string)($element['type'] ?? ''),
                        'sql' => (string)($element['commandsql'] ?? ''),
                        'columns' => $element['info']['column'] ?? null,
                        'metadata' => self::metadata($element, $element['info'] ?? null),
                    ];
                    $usableelements[] = $ref;
                }
                if ($usableelements) {
                    if (isset($result['blocks'][$blockref])) {
                        $result['issues'][] = ['code' => 'DUPLICATE_STOCK_BLOCK',
                            'refkey' => $blockref, 'file' => $name];
                    }
                    $result['blocks'][$blockref] = ['refkey' => $blockref, 'page_refkey' => $pageref,
                        'file' => $name];
                    $usableblocks[] = $blockref;
                }
            }
            if ($usableblocks) {
                $category = $page['category'] ?? [];
                $catref = (string)($category['refkey'] ?? '');
                if ($catref === '') {
                    $result['issues'][] = ['code' => 'INVALID_STOCK_CATEGORY', 'file' => $name];
                } else if (isset($result['categories'][$catref]) &&
                        $result['categories'][$catref]['title'] !== ($category['title'] ?? null)) {
                    $result['issues'][] = ['code' => 'DUPLICATE_STOCK_CATEGORY',
                        'refkey' => $catref, 'file' => $name];
                } else {
                    $result['categories'][$catref] = [
                        'refkey' => $catref, 'title' => $category['title'] ?? null,
                    ];
                }
                if (isset($result['pages'][$pageref])) {
                    $result['issues'][] = ['code' => 'DUPLICATE_STOCK_PAGE',
                        'refkey' => $pageref, 'file' => $name];
                }
                $result['pages'][$pageref] = [
                    'refkey' => $pageref, 'file' => $name, 'category_refkey' => $catref,
                ];
            }
        }
        return $result;
    }

    /**
     * CRC32B as used by the existing Moodle upgrade for default column definitions.
     *
     * @param array $columns Decoded column settings.
     * @return string
     */
    public static function column_json(array $columns): string {
        return json_encode($columns, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /**
     * Extract presentation metadata separately from SQL and report columns.
     *
     * @param array $row Bundled or installed report attributes.
     * @param mixed $info Decoded element information.
     * @return array
     */
    private static function metadata(array $row, $info): array {
        $result = [];
        foreach (['title', 'type', 'theme', 'css', 'html_before', 'html_after',
                'cache', 'reload', 'block_num'] as $field) {
            $result[$field] = $row[$field] ?? null;
        }
        $result['info_other'] = is_array($info) ? array_diff_key($info, ['column' => true]) : null;
        return $result;
    }

    /**
     * Return metadata fingerprints and changed field names without values.
     *
     * @param array $actual Installed metadata.
     * @param array $target Bundled metadata.
     * @return array
     */
    private static function metadata_diff(array $actual, array $target): array {
        $oldjson = self::column_json($actual);
        $newjson = self::column_json($target);
        $changed = [];
        foreach ($target as $field => $value) {
            if (($actual[$field] ?? null) !== $value) {
                $changed[] = $field;
            }
        }
        return [
            'crc32b' => hash('crc32b', $oldjson),
            'sha256' => hash('sha256', $oldjson),
            'target_sha256' => hash('sha256', $newjson),
            'matches_current' => $oldjson === $newjson,
            'changed_fields' => $changed,
        ];
    }

    /**
     * Return differences in schema without disclosing SQL, titles or Mustache contents.
     *
     * @param array|null $before Stored columns.
     * @param array|null $after Bundled columns.
     * @return array
     */
    public static function column_diff(?array $before, ?array $after): array {
        $before = $before ?? [];
        $after = $after ?? [];
        $added = array_values(array_diff(array_keys($after), array_keys($before)));
        $removed = array_values(array_diff(array_keys($before), array_keys($after)));
        $modified = [];
        foreach (array_intersect(array_keys($before), array_keys($after)) as $key) {
            if ($before[$key] === $after[$key]) {
                continue;
            }
            $left = is_array($before[$key]) ? $before[$key] : ['value' => $before[$key]];
            $right = is_array($after[$key]) ? $after[$key] : ['value' => $after[$key]];
            $attributes = [];
            foreach (array_unique(array_merge(array_keys($left), array_keys($right))) as $attribute) {
                if (($left[$attribute] ?? null) !== ($right[$attribute] ?? null)) {
                    $attributes[] = (string)$attribute;
                }
            }
            $modified[] = ['key' => (string)$key, 'attributes' => $attributes];
        }
        return ['added' => $added, 'removed' => $removed, 'modified' => $modified];
    }

    /**
     * Simulate the exact upgrade decision for one installed and one bundled element.
     *
     * @param array $current Official definition.
     * @param object $installed Installed element.
     * @param array|null $signature Three-entry stock signature, or null.
     * @return array
     */
    public static function classify(array $current, object $installed, ?array $signature): array {
        $sql = (string)($installed->commandsql ?? '');
        $info = json_decode((string)($installed->info ?? ''), true);
        $valid = is_array($info) && isset($info['column']) && is_array($info['column']);
        $columns = $valid ? $info['column'] : null;
        $target = $current['columns'];
        $storedcolumn = $valid ? self::column_json($columns) : null;
        $targetcolumn = is_array($target) ? self::column_json($target) : null;
        $sqlcrc = hash('crc32b', $sql);
        $columncrc = $storedcolumn === null ? null : hash('crc32b', $storedcolumn);
        $newcolumncrc = $targetcolumn === null ? null : hash('crc32b', $targetcolumn);
        $result = [
            'status' => '', 'reason' => '', 'refkey' => $current['refkey'],
            'element_id' => (int)$installed->id, 'file' => $current['file'],
            'page_refkey' => $current['page_refkey'], 'block_refkey' => $current['block_refkey'],
            'sql' => ['crc32b' => $sqlcrc, 'sha256' => hash('sha256', $sql),
                'target_sha256' => hash('sha256', $current['sql']), 'matches_current' => $sql === $current['sql']],
            'columns' => ['crc32b' => $columncrc,
                'sha256' => $storedcolumn === null ? null : hash('sha256', $storedcolumn),
                'target_sha256' => $targetcolumn === null ? null : hash('sha256', $targetcolumn),
                'matches_current' => $storedcolumn !== null && $storedcolumn === $targetcolumn,
                'diff' => self::column_diff($columns, is_array($target) ? $target : null)],
        ];
        if (isset($current['metadata'])) {
            $result['metadata'] = self::metadata_diff(
                self::metadata(get_object_vars($installed), $info), $current['metadata']);
        }
        if ($signature === null) {
            $result['status'] = 'NOT_COVERED';
            $result['reason'] = 'NO_MIGRATION_SIGNATURE';
            return $result;
        }
        if (!$valid || $targetcolumn === null) {
            $result['status'] = 'INVALID_INFO';
            $result['reason'] = 'COLUMNS_UNAVAILABLE';
            return $result;
        }
        // Exactly the predicates in report_stock_refresh::refresh().
        $legacy = $sqlcrc === $signature[1];
        $stockcolumns = $columncrc === $signature[2] || $columncrc === $newcolumncrc;
        $iscurrent = $sql === $current['sql'];
        $result['sql']['matches_legacy'] = $legacy;
        $result['columns']['matches_legacy'] = $columncrc === $signature[2];
        if ((!$legacy && !$iscurrent) || !$stockcolumns) {
            $result['status'] = 'PRESERVED';
            $result['reason'] = (!$legacy && !$iscurrent) ? 'UNRECOGNIZED_SQL' : 'UNRECOGNIZED_COLUMNS';
        } else if ($iscurrent && $columncrc === $newcolumncrc) {
            $result['status'] = 'CURRENT';
            $result['reason'] = 'STOCK_CURRENT';
        } else {
            $result['status'] = 'WOULD_UPDATE';
            $result['reason'] = 'RECOGNIZED_STOCK';
        }
        return $result;
    }

    /**
     * Audit installed pages, blocks, categories and elements in memory.
     *
     * @param array $catalogue Official catalogue.
     * @param array $installed Table-name keys cat, page, block and element; arrays of records.
     * @param array $signatures Legacy stock signatures.
     * @param string $family Database family.
     * @return array
     */
    public static function inspect(array $catalogue, array $installed, array $signatures, string $family): array {
        $summary = ['CURRENT' => 0, 'WOULD_UPDATE' => 0, 'PRESERVED' => 0, 'MISSING' => 0,
            'DUPLICATE' => 0, 'NOT_COVERED' => 0, 'CUSTOM_ONLY' => 0, 'INVALID_INFO' => 0,
            'OTHER_ENGINE' => 0];
        $issues = $catalogue['issues'];
        $index = [];
        foreach (['cat', 'page', 'block', 'element'] as $table) {
            $index[$table] = [];
            foreach ($installed[$table] ?? [] as $record) {
                $ref = trim((string)($record->refkey ?? ''));
                if ($ref === '') {
                    $issues[] = ['code' => 'EMPTY_INSTALLED_REFKEY', 'table' => $table,
                        'id' => (int)$record->id];
                    continue;
                }
                $index[$table][$ref][] = $record;
            }
            foreach ($index[$table] as $ref => $records) {
                if (count($records) > 1) {
                    $issues[] = ['code' => 'DUPLICATE_INSTALLED_REFKEY', 'table' => $table,
                        'refkey' => (string)$ref, 'ids' => array_map(static function($row) {
                            return (int)$row->id;
                        }, $records)];
                }
            }
        }
        $pagesbyid = [];
        foreach ($installed['page'] ?? [] as $record) {
            $pagesbyid[$record->id] = $record;
        }
        $catsbyid = [];
        foreach ($installed['cat'] ?? [] as $row) {
            $catsbyid[$row->id] = true;
        }
        foreach ($installed['page'] ?? [] as $record) {
            if (!isset($catsbyid[$record->cat_id])) {
                $issues[] = ['code' => 'ORPHAN_PAGE', 'id' => (int)$record->id];
            }
        }
        $blocksbyid = [];
        foreach ($installed['block'] ?? [] as $record) {
            $blocksbyid[$record->id] = $record;
            if (!isset($pagesbyid[$record->page_id])) {
                $issues[] = ['code' => 'ORPHAN_BLOCK', 'id' => (int)$record->id];
            }
        }
        foreach ($installed['element'] ?? [] as $record) {
            if (!isset($blocksbyid[$record->block_id])) {
                $issues[] = ['code' => 'ORPHAN_ELEMENT', 'id' => (int)$record->id];
            }
        }
        foreach (['page' => 'pages', 'block' => 'blocks'] as $table => $section) {
            foreach ($catalogue[$section] as $ref => $entry) {
                if (empty($index[$table][$ref])) {
                    $issues[] = ['code' => 'MISSING_' . strtoupper($table), 'refkey' => $ref];
                }
            }
        }
        // Validate the expected parent, not just existence of the foreign key.
        foreach ($catalogue['pages'] as $ref => $page) {
            if (count($index['page'][$ref] ?? []) !== 1) {
                continue;
            }
            $record = $index['page'][$ref][0];
            $catref = $page['category_refkey'] ?? '';
            if ($catref !== '' && count($index['cat'][$catref] ?? []) === 1 &&
                    (int)$record->cat_id !== (int)$index['cat'][$catref][0]->id) {
                $issues[] = ['code' => 'WRONG_CATEGORY', 'refkey' => $ref,
                    'id' => (int)$record->id];
            }
        }
        foreach ($catalogue['blocks'] as $ref => $block) {
            if (count($index['block'][$ref] ?? []) !== 1) {
                continue;
            }
            $record = $index['block'][$ref][0];
            $pageref = $block['page_refkey'];
            if (count($index['page'][$pageref] ?? []) === 1 &&
                    (int)$record->page_id !== (int)$index['page'][$pageref][0]->id) {
                $issues[] = ['code' => 'WRONG_PAGE', 'refkey' => $ref,
                    'id' => (int)$record->id];
            }
        }
        foreach ($catalogue['elements'] as $ref => $element) {
            if (count($index['element'][$ref] ?? []) !== 1) {
                continue;
            }
            $record = $index['element'][$ref][0];
            $blockref = $element['block_refkey'];
            if (count($index['block'][$blockref] ?? []) === 1 &&
                    (int)$record->block_id !== (int)$index['block'][$blockref][0]->id) {
                $issues[] = ['code' => 'WRONG_BLOCK', 'refkey' => $ref,
                    'id' => (int)$record->id];
            }
        }
        $reports = [];
        foreach ($catalogue['elements'] as $ref => $official) {
            $records = $index['element'][$ref] ?? [];
            if (!$records) {
                $report = ['status' => 'MISSING', 'refkey' => $ref, 'file' => $official['file']];
            } else if (count($records) > 1) {
                $report = ['status' => 'DUPLICATE', 'refkey' => $ref,
                    'ids' => array_map(static function($record) {
                        return (int)$record->id;
                    }, $records)];
            } else {
                $report = self::classify($official, $records[0], $signatures[$ref] ?? null);
                $blockid = (int)$records[0]->block_id;
                $report['installed_block_id'] = $blockid;
                $report['installed_page_id'] = isset($blocksbyid[$blockid]) ?
                    (int)$blocksbyid[$blockid]->page_id : null;
            }
            $reports[] = $report;
            $summary[$report['status']]++;
        }
        foreach ($index['element'] as $ref => $records) {
            if (isset($catalogue['elements'][$ref])) {
                continue;
            }
            $status = isset($catalogue['allrefs'][$ref]) ? 'OTHER_ENGINE' : 'CUSTOM_ONLY';
            foreach ($records as $record) {
                $storedsql = (string)($record->commandsql ?? '');
                $storedinfo = json_decode((string)($record->info ?? ''), true);
                $columnjson = is_array($storedinfo) && isset($storedinfo['column']) &&
                    is_array($storedinfo['column']) ? self::column_json($storedinfo['column']) : null;
                $reports[] = ['status' => $status, 'refkey' => $ref, 'element_id' => (int)$record->id,
                    'installed_block_id' => (int)$record->block_id,
                    'sql' => ['crc32b' => hash('crc32b', $storedsql),
                        'sha256' => hash('sha256', $storedsql)],
                    'columns' => ['crc32b' => $columnjson === null ? null : hash('crc32b', $columnjson),
                        'sha256' => $columnjson === null ? null : hash('sha256', $columnjson)]];
                $summary[$status]++;
            }
        }
        usort($reports, static function($a, $b) {
            return strcmp((string)$a['refkey'], (string)$b['refkey']) ?: strcmp($a['status'], $b['status']);
        });
        $blockers = array_values(array_filter($issues, static function($issue) {
            return true;
        }));
        foreach ($reports as $report) {
            if (in_array($report['status'], ['MISSING', 'DUPLICATE', 'INVALID_INFO', 'PRESERVED'], true)) {
                $blockers[] = ['code' => $report['status'], 'refkey' => $report['refkey']];
            }
        }
        return [
            'audit' => ['mode' => 'read-only', 'migration' => 2026102500,
                'database' => $family, 'executed_upgrade' => false, 'executed_report_sql' => false,
                'writes' => 0],
            'summary' => array_merge(['expected_pages' => count($catalogue['pages']),
                'expected_blocks' => count($catalogue['blocks']),
                'expected_elements' => count($catalogue['elements']),
                'installed_pages' => count($installed['page'] ?? []),
                'installed_blocks' => count($installed['block'] ?? []),
                'installed_elements' => count($installed['element'] ?? [])], $summary),
            'reports' => $reports, 'issues' => $issues,
            'approval' => ['status' => $blockers ? 'BLOCKED' : 'PENDING',
                'blocking_issues' => $blockers],
        ];
    }

    /**
     * Compact and deterministic terminal output; SQL and personal data are omitted.
     *
     * @param array $audit Audit payload.
     * @return string
     */
    public static function text(array $audit): string {
        $lines = ['Kopere BI - Read-only report audit',
            'Migration: 2026102500', 'Database: ' . $audit['audit']['database']];
        foreach ($audit['summary'] as $key => $value) {
            $lines[] = strtoupper($key) . ': ' . $value;
        }
        $lines[] = 'Approval: ' . $audit['approval']['status'];
        foreach ($audit['approval']['blocking_issues'] as $issue) {
            $lines[] = 'BLOCKER: ' . $issue['code'] . ' ' . ($issue['refkey'] ?? $issue['id'] ?? '');
        }
        foreach ($audit['reports'] as $item) {
            $lines[] = $item['status'] . ' ' . $item['refkey'] . ' ' . ($item['reason'] ?? '');
        }
        return implode("\n", $lines) . "\n";
    }
    /**
     * Reproduce per-row migration eligibility, even for duplicate refkeys.
     *
     * @param array $catalogue Active-engine stock definitions.
     * @param array $installed Installed records grouped by table.
     * @param array $signatures Historical stock signatures.
     * @return array
     */
    public static function migration_projection(array $catalogue, array $installed, array $signatures): array {
        $counts = ['updated' => 0, 'current' => 0, 'preserved' => 0];
        $rows = [];
        foreach ($signatures as $ref => $signature) {
            if (!isset($catalogue['elements'][$ref])) {
                continue;
            }
            foreach ($installed['element'] ?? [] as $record) {
                if ((string)($record->refkey ?? '') !== $ref) {
                    continue;
                }
                $item = self::classify($catalogue['elements'][$ref], $record, $signature);
                $status = $item['status'];
                $counter = $status === 'WOULD_UPDATE' ? 'updated' :
                    ($status === 'CURRENT' ? 'current' : 'preserved');
                $counts[$counter]++;
                $rows[] = ['refkey' => $ref, 'element_id' => (int)$record->id,
                    'decision' => $status, 'migration_counter' => $counter,
                    'reason' => $item['reason']];
            }
        }
        usort($rows, static function($a, $b) {
            return strcmp($a['refkey'], $b['refkey']) ?:
                ($a['element_id'] <=> $b['element_id']);
        });
        return ['counts' => $counts, 'rows' => $rows];
    }

    /**
     * Validate CLI options after Moodle parses the command line.
     *
     * @param array $options Parsed options.
     * @param array $unrecognized Extra arguments.
     * @return void
     */
    public static function validate_options(array $options, array $unrecognized): void {
        if ($unrecognized || !in_array($options['format'] ?? null, ['text', 'json'], true)) {
            throw new \InvalidArgumentException('Invalid audit CLI options.');
        }
    }

    /**
     * Format a report without printing or changing any state.
     *
     * @param array $audit Inspection payload.
     * @param string $format Output format.
     * @return string
     */
    public static function render(array $audit, string $format): string {
        self::validate_options(['format' => $format], []);
        return $format === 'json' ? json_encode($audit, JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) :
            self::text($audit);
    }

    /**
     * Return an error code only for strict blocked audits.
     *
     * @param array $audit Inspection payload.
     * @param bool $strict Strict mode.
     * @return int
     */
    public static function exit_code(array $audit, bool $strict): int {
        return $strict && !empty($audit['approval']['blocking_issues']) ? 2 : 0;
    }

}
