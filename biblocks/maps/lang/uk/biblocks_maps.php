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

$string['maps_1_city'] = '{a1} та ще одне місто';
$string['maps_many_city'] = '{a1} та ще {a2} міст';
$string['maps_online'] = '{a1} студент онлайн';
$string['maps_onlines'] = '{a1} студентів онлайн';
$string['maps_sql_warning'] =
    '<p>Пам’ятайте, що наведений нижче SQL має повертати лише один стовпець, і цей стовпець має містити дійсну IP-адресу.<br>Приклад: SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> повертає всіх студентів, які заходили в Moodle протягом останніх 10 хвилин</p>';
$string['pluginname'] = 'Мапа студентів онлайн';
$string['pluginname_desc'] = 'Створює мапу студентів онлайн на основі їхніх IP-адрес';
