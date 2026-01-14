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

$string['maps_1_city'] = '{a1} and one more city';
$string['maps_many_city'] = '{a1} and {a2} more cities';
$string['maps_online'] = '{a1} student online';
$string['maps_onlines'] = '{a1} students online';
$string['maps_sql_warning'] = '<p>Remember that the SQL below should return only one column, and that column should contain a valid IP.<br>Example: the SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> returns all students who accessed Moodle in the last 10 minutes</p>';
$string['pluginname'] = 'Online students map';
$string['pluginname_desc'] = 'Creates a map of online students based on their IPs';
