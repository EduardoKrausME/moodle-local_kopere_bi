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
 * Read-only CLI inventory and simulation of stock report upgrade 2026102500.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use local_kopere_bi\audit\report_inventory;
use local_kopere_bi\upgrade\report_stock_refresh;

[$options, $unrecognized] = cli_get_params(
    ['help' => false, 'format' => 'text', 'strict' => false],
    ['h' => 'help']
);

if ($options['help']) {
    cli_writeln("Kopere BI report audit (read-only)\n" .
        "Usage: php local/kopere_bi/cli/audit_reports.php [--format=text|json] [--strict]\n" .
        "--strict exits with status 2 if approval blockers are found.\n" .
        "No reports are executed and no records are modified.");
    exit(0);
}
if ($unrecognized || !in_array($options['format'], ['text', 'json'], true)) {
    cli_error('Invalid arguments. Run with --help for usage.');
}

try {
    $family = $DB->get_dbfamily();
    $catalogue = report_inventory::catalogue(__DIR__ . '/../db/files', $family);
    $tables = [
        'cat' => ['local_kopere_bi_cat', 'id, refkey'],
        'page' => ['local_kopere_bi_page', 'id, refkey, cat_id'],
        'block' => ['local_kopere_bi_block', 'id, refkey, page_id'],
        'element' => ['local_kopere_bi_element', 'id, refkey, block_id, commandsql, info'],
    ];
    $installed = [];
    foreach ($tables as $key => [$name, $fields]) {
        $installed[$key] = array_values($DB->get_records($name, [], 'id ASC', $fields));
    }
    $signatures = report_stock_refresh::stock_signatures();
    $audit = report_inventory::inspect($catalogue, $installed, $signatures, $family);

    // This method does not update records when true is passed. Reconcile only
    // the three counters which the migration actually calculates.
    $dryrun = report_stock_refresh::refresh(true);
    $audit['migration_dryrun'] = $dryrun;
    $comparison = ['updated' => 'WOULD_UPDATE', 'current' => 'CURRENT', 'preserved' => 'PRESERVED'];
    foreach ($comparison as $key => $status) {
        if ($audit['summary'][$status] !== $dryrun[$key]) {
            $issue = ['code' => 'MIGRATION_COUNT_MISMATCH', 'counter' => $key,
                'inventory' => $audit['summary'][$status], 'dryrun' => $dryrun[$key]];
            $audit['issues'][] = $issue;
            $audit['approval']['blocking_issues'][] = $issue;
            $audit['approval']['status'] = 'BLOCKED';
        }
    }
    // The migration's missing counter is not populated: our independent
    // catalogue comparison is the authoritative absence check.
    if ($options['format'] === 'json') {
        cli_writeln(json_encode($audit, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    } else {
        cli_writeln(report_inventory::text($audit));
        cli_writeln('Migration dry-run: ' . json_encode($dryrun, JSON_THROW_ON_ERROR));
    }
    exit($options['strict'] && $audit['approval']['blocking_issues'] ? 2 : 0);
} catch (\Throwable $exception) {
    // Do not expose installed SQL or configuration contents in an error log.
    cli_error('Unable to complete the read-only audit (' . get_class($exception) . ').');
}
