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

$string['maps_1_city'] = '{a1} e un\'altra città';
$string['maps_many_city'] = '{a1} e altre {a2} città';
$string['maps_online'] = '{a1} studente online';
$string['maps_onlines'] = '{a1} studenti online';
$string['maps_sql_warning'] =
    '<p>Ricorda che l’SQL seguente deve restituire una sola colonna, e che tale colonna deve contenere un IP valido.<br>Esempio: l’SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> restituisce tutti gli studenti che hanno effettuato l’accesso a Moodle negli ultimi 10 minuti</p>';
$string['pluginname'] = 'Mappa degli studenti online';
$string['pluginname_desc'] = 'Crea una mappa degli studenti online in base ai loro IP';
