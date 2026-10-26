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
try {
    report_inventory::validate_options($options, $unrecognized);
} catch (\InvalidArgumentException $exception) {
    cli_error('Invalid arguments. Run with --help for usage.');
}

try {
    $family = $DB->get_dbfamily();
    $catalogue = report_inventory::catalogue(__DIR__ . '/../db/files', $family);
    $tables = [
        'cat' => ['local_kopere_bi_cat', 'id, refkey'],
        'page' => ['local_kopere_bi_page', 'id, refkey, cat_id'],
        'block' => ['local_kopere_bi_block', 'id, refkey, page_id'],
        'element' => ['local_kopere_bi_element',
            'id, refkey, block_id, commandsql, info, title, type, theme, css, ' .
            'html_before, html_after, cache, reload, block_num'],
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
    $projection = report_inventory::migration_projection($catalogue, $installed, $signatures);
    $audit['migration_dryrun'] = $dryrun;
    $audit['migration_projection'] = $projection;
    foreach (['updated', 'current', 'preserved'] as $key) {
        if ($projection['counts'][$key] !== $dryrun[$key]) {
            $issue = ['code' => 'MIGRATION_COUNT_MISMATCH', 'counter' => $key,
                'inventory' => $projection['counts'][$key], 'dryrun' => $dryrun[$key]];
            $audit['issues'][] = $issue;
            $audit['approval']['blocking_issues'][] = $issue;
            $audit['approval']['status'] = 'BLOCKED';
        }
    }
    // refresh(true) does not update its missing counter.
    cli_writeln(report_inventory::render($audit, $options['format']));
    exit(report_inventory::exit_code($audit, (bool)$options['strict']));
} catch (\Throwable $exception) {
    // Do not expose installed SQL or configuration contents in an error log.
    cli_error('Unable to complete the read-only audit (' . get_class($exception) . ').');
}
