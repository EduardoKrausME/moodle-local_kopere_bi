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
 * Function import_reports
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @throws Exception
 */

use local_kopere_bi\local\util\install_reports;

/**
 * Function import_reports
 *
 * @throws Exception
 */
function import_reports() {
    global $DB;

    set_config("theme_palette", "default", "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_pie_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_pie_default", $json, "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_column_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_column_default", $json, "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_area_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_area_default", $json, "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_line_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_line_default", $json, "local_kopere_bi");

    try {
        $DB->delete_records("local_kopere_bi_cat");
        $DB->delete_records("local_kopere_bi_page");
        $DB->delete_records("local_kopere_bi_block");
        $DB->delete_records("local_kopere_bi_element");
    } catch (Exception $e) {
        return;
    }

    // Load report pages.
    $pagefiles = glob(__DIR__ . "/files/page-*.json");
    foreach ($pagefiles as $pagefile) {
        install_reports::from_file($pagefile);
    }
}
