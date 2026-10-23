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
 * Refresh selected built-in analytics reports without requiring a plugin version bump.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define("CLI_SCRIPT", true);

require_once(__DIR__ . "/../../../config.php");
require_once($CFG->libdir . "/clilib.php");

use local_kopere_bi\upgrade\analytics_report_refresh;

[$options] = cli_get_params(["help" => false, "dry-run" => false],
    ["h" => "help"]);

if ($options["help"]) {
    cli_writeln("Refresh six built-in analytics reports, preserving locally customised SQL.");
    cli_writeln("Usage: php local/kopere_bi/cli/refresh_analytics_reports.php [--dry-run]");
    exit(0);
}

$counts = analytics_report_refresh::refresh((bool)$options["dry-run"]);
foreach ($counts as $status => $count) {
    cli_writeln($status . ": " . $count);
}
cli_writeln($options["dry-run"] ? "Dry run finished; nothing changed." :
    "Built-in analytics definitions refreshed.");
