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
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Line Chart';
$string['pluginname_desc'] = 'Generates a line chart';
$string['line_sql_warning'] = '<p>Remember that the SQL below must return with the following structure:</p>
<ul>
    <li>The first column should contain the text that will be used as the X-axis names.</li>
    <li>The other columns should be structured as follows:
        <ul>
            <li>The column name will be used as the series name. You can use translation strings as explained on the
                <a href="?classname=bi-extra_langs&method=index" target="_blank">strings page</a>.</li>
            <li>The column value will represent the series data in the chart.</li>
        </ul>
    </li>
</ul>
<blockquote>In the example below, the first column returns the course name, and the second column returns the number of news items for each course:
<pre>SELECT fullname,
       newsitems AS "Course news items count"
  FROM mdl_course</pre></blockquote>
<blockquote>In the example below, besides the first column being the course name, it generates two additional lines on the chart, with translated column names:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
