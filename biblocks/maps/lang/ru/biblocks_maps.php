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
 * @package   biblocks_maps
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['maps_1_city'] = '{a1} и ещё один город';
$string['maps_many_city'] = '{a1} и ещё {a2} городов';
$string['maps_online'] = '{a1} студент онлайн';
$string['maps_onlines'] = '{a1} студентов онлайн';
$string['maps_sql_warning'] =
    '<p>Помните, что приведённый ниже SQL должен возвращать только один столбец, и этот столбец должен содержать действительный IP-адрес.<br>Пример: SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> возвращает всех студентов, которые заходили в Moodle за последние 10 минут</p>';
$string['pluginname'] = 'Карта студентов онлайн';
$string['pluginname_desc'] = 'Создаёт карту студентов онлайн на основе их IP-адресов';
