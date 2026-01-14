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
 * Lang file
 *
 * @package   biblocks_table
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Data table';
$string['pluginname_desc'] = 'Displays a table with data pagination.';
$string['table_info_topo'] = 'First, you will see a preview of the search results. Then, a series of columns will be presented for you to name the titles and define the format of each column’s data.';
$string['table_first_records'] = 'The first ten records of the query';
$string['table_info_types'] = 'You can now set a name for the column and then specify the desired format and whether you want any extra formatting with Mustache.
<ul>
    <li><strong>No formatting</strong>: Displays the content exactly as it is or applies Mustache if you add it.</li>
    <li><strong>Do not show this column</strong>: Hides the selected column in the view, but the data remains available for Mustache processing.</li>
    <ul><li>Mustache not available</li></ul>
    <li><strong>Numbers</strong>: Formats the column to display only numeric values, applying standard number display rules, such as thousands and decimal separators.</li>
    <ul><li>Mustache not available</li></ul>
    <li><strong>Convert column to full name "fullname()"</strong>: Runs the <code>fullname()</code> function to generate the full name based on the language, which will be stored in this same column. For this to work, the <code>lastname</code> column is required and should be hidden if possible.</li>
    <li><strong>Convert student ID to profile picture</strong>: Uses the ID from this column to create the profile picture.</li>
    <li><strong>Binary field for Visible/Invisible</strong>: Uses the binary value to determine visibility, where "0"/"false" means invisible and "1"/"true" means visible.</li>
    <li><strong>Binary field for Active/Inactive</strong>: Uses the binary value to determine the status, where "0"/"false" means Inactive and "1"/"true" means Active.</li>
    <li><strong>"Time" field formatted as date</strong>: Converts the time value (timestamp) in the column to a readable date, displaying only the date (day/month/year).</li>
    <ul><li>Mustache not available</li></ul>
    <li><strong>"Time" field formatted as date and time</strong>: Displays the time value (timestamp) in the column as a full date, including the time (day/month/year and hours:minutes).</li>
    <ul><li>Mustache not available</li></ul>
    <li><strong>"Time" field formatted as time</strong>: Formats the time value (timestamp) in the column to display only the time (hours:minutes), omitting the date.</li>
    <ul><li>Mustache not available</li></ul>
</ul>';
$string['table_edit_column'] = 'Column';
$string['table_renderer_date'] = '"Time" field formatted as date';
$string['table_renderer_datetime'] = '"Time" field formatted as date and time';
$string['table_renderer_filesize'] = 'Converts to disk data size';
$string['table_renderer_mustache'] = 'HTML of the
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a> column';
$string['table_renderer_none'] = 'Do not display this column';
$string['table_renderer_number'] = 'Numbers';
$string['table_renderer_seconds'] = '"Time" field formatted as time';
$string['table_renderer_status'] = 'Binary field for Active/Inactive';
$string['table_renderer_title'] = 'Column formatting';
$string['table_renderer_translate'] = 'Use get_string("identifier", "component") to translate the column';
$string['table_renderer_userfullname'] = 'Convert the column to the student’s full name with the "fullname()" function';
$string['table_renderer_userphoto'] = 'Convert student ID to profile picture';
$string['table_renderer_visible'] = 'Binary field for Visible/Invisible';
$string['table_col_title'] = 'Column title';
$string['table_column_not_configured'] = 'Columns not configured in this table';

$string['filter_plugin_status'] = 'Habilitar o Plugin <em>{$a}</em>?';
$string['filter_active'] = 'Sim';
$string['filter_inactive'] = 'Não';
$string['filter_choose_field_to_filter'] = 'Choose which field you want to filter';
$string['filter_save'] = 'Save';
$string['filter_select'] = 'Selecione um campo';