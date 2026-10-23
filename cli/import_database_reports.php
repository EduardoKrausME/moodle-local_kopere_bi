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
 * Add missing database-specific built-in reports to an existing installation.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define("CLI_SCRIPT", true);

require_once(__DIR__ . "/../../../config.php");
require_once($CFG->libdir . "/clilib.php");

use local_kopere_bi\install\reports;

[$options] = cli_get_params(["help" => false], ["h" => "help"]);
if ($options["help"]) {
    cli_writeln("Import missing MySQL/PostgreSQL built-in report variants without overwriting installed reports.");
    cli_writeln("Usage: php local/kopere_bi/cli/import_database_reports.php");
    exit(0);
}

if (!in_array($DB->get_dbfamily(), ["mysql", "postgres"], true)) {
    cli_error("Unsupported Moodle database family: " . $DB->get_dbfamily());
}

$files = glob(__DIR__ . "/../db/files/page-*-postgres.json");
if ($files === false || count($files) === 0) {
    cli_error("No bundled PostgreSQL report definitions were found.");
}

foreach ($files as $file) {
    reports::from_file($file, true);
}

// These three existing pages contain separate MySQL/PostgreSQL blocks.
foreach ([1, 4, 8] as $number) {
    reports::from_file(__DIR__ . "/../db/files/page-" . sprintf("%03d", $number) . ".json", true);
}

cli_writeln("Database-specific report import completed without replacing existing report definitions.");
