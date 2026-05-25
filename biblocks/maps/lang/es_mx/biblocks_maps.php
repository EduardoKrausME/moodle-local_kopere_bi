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

$string['maps_1_city'] = '{a1} y una ciudad más';
$string['maps_many_city'] = '{a1} y {a2} ciudades más';
$string['maps_online'] = '{a1} estudiante en línea';
$string['maps_onlines'] = '{a1} estudiantes en línea';
$string['maps_sql_warning'] =
    '<p>Recuerda que el SQL siguiente debe devolver solo una columna, y esa columna debe contener una IP válida.<br>Ejemplo: el SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> devuelve todos los estudiantes que ingresaron a Moodle en los últimos 10 minutos</p>';
$string['pluginname'] = 'Mapa de estudiantes en línea';
$string['pluginname_desc'] = 'Crea un mapa de estudiantes en línea basado en sus IPs';
