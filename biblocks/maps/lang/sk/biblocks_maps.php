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

$string['maps_1_city'] = '{a1} a ešte jedno mesto';
$string['maps_many_city'] = '{a1} a ďalších {a2} miest';
$string['maps_online'] = '{a1} študent online';
$string['maps_onlines'] = '{a1} študentov online';
$string['maps_sql_warning'] =
    '<p>Pamätajte, že SQL uvedené nižšie má vrátiť iba jeden stĺpec a tento stĺpec musí obsahovať platnú IP adresu.<br>Príklad: SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> vráti všetkých študentov, ktorí pristúpili do Moodle za posledných 10 minút</p>';
$string['pluginname'] = 'Mapa online študentov';
$string['pluginname_desc'] = 'Vytvorí mapu online študentov na základe ich IP adries';
