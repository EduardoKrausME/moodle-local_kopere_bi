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

$string['maps_1_city'] = '{a1} et une ville de plus';
$string['maps_many_city'] = '{a1} et {a2} villes de plus';
$string['maps_online'] = '{a1} étudiant en ligne';
$string['maps_onlines'] = '{a1} étudiants en ligne';
$string['maps_sql_warning'] =
    '<p>N’oubliez pas que le SQL ci-dessous doit retourner une seule colonne, et que cette colonne doit contenir une IP valide.<br>Exemple : le SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> retourne tous les étudiants ayant accédé à Moodle au cours des 10 dernières minutes</p>';
$string['pluginname'] = 'Carte des étudiants en ligne';
$string['pluginname_desc'] = 'Crée une carte des étudiants en ligne à partir de leurs IP';
