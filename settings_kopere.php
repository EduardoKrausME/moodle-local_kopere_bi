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
 * setting kopere file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $removetitle;
if (!$removetitle) {
    $settings->add(
        new admin_setting_heading("kopere_bi_title", get_string("pluginname", "local_kopere_bi"), "")
    );
}

$options = [
    "default" => get_string("theme_palette_default", "local_kopere_bi"),
    "palette1" => get_string("theme_palette_palette", "local_kopere_bi", "1"),
    "palette2" => get_string("theme_palette_palette", "local_kopere_bi", "2"),
    "palette3" => get_string("theme_palette_palette", "local_kopere_bi", "3"),
    "palette4" => get_string("theme_palette_palette", "local_kopere_bi", "4"),
    "palette5" => get_string("theme_palette_palette", "local_kopere_bi", "5"),
    "palette6" => get_string("theme_palette_palette", "local_kopere_bi", "6"),
    "palette7" => get_string("theme_palette_palette", "local_kopere_bi", "7"),
    "palette8" => get_string("theme_palette_palette", "local_kopere_bi", "8"),
    "palette9" => get_string("theme_palette_palette", "local_kopere_bi", "9"),
    "palette10" => get_string("theme_palette_palette", "local_kopere_bi", "10"),
];
$name = 'local_kopere_bi/theme_palette';
$title = get_string("theme_palette_title", "local_kopere_bi");
$description =
    "<div id='id_s_local_kopere_bi_theme_palette_html'>" . get_string("theme_palette_desc", "local_kopere_bi") . " </div>" .
    "<a target='_blank' href='https://apexcharts.com/docs/options/theme/#palette'>" .
    get_string("theme_palette_desc2", "local_kopere_bi") . "</a>";
$setting = new admin_setting_configselect($name, $title, $description, "default", $options);
$settings->add($setting);

// JS from the theme_palette field.
$PAGE->requires->js_call_amd("local_kopere_bi/setting", "theme_palette");

$name = 'local_kopere_bi/chart_pie_default';
$title = get_string("chart_pie_default", "local_kopere_bi");
$setting = new admin_setting_configtextarea($name, $title, get_string("chart_default_desc", "local_kopere_bi"), "");
$settings->add($setting);

$name = 'local_kopere_bi/chart_column_default';
$title = get_string("chart_column_default", "local_kopere_bi");
$setting = new admin_setting_configtextarea($name, $title, get_string("chart_default_desc", "local_kopere_bi"), "");
$settings->add($setting);

$name = 'local_kopere_bi/chart_area_default';
$title = get_string("chart_area_default", "local_kopere_bi");
$setting = new admin_setting_configtextarea($name, $title, get_string("chart_default_desc", "local_kopere_bi"), "");
$settings->add($setting);

$name = 'local_kopere_bi/chart_line_default';
$title = get_string("chart_line_default", "local_kopere_bi");
$setting = new admin_setting_configtextarea($name, $title, get_string("chart_default_desc", "local_kopere_bi"), "");
$settings->add($setting);

// JS of the fields chart_XXX_default.
$PAGE->requires->js_call_amd("local_kopere_bi/setting", "chart_default");
