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

$string['maps_1_city'] = '{a1} dan satu kota lagi';
$string['maps_many_city'] = '{a1} dan {a2} kota lainnya';
$string['maps_online'] = '{a1} siswa online';
$string['maps_onlines'] = '{a1} siswa online';
$string['maps_sql_warning'] =
    '<p>Ingat bahwa SQL di bawah ini harus mengembalikan hanya satu kolom, dan kolom tersebut harus berisi IP yang valid.<br>Contoh: SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> mengembalikan semua siswa yang mengakses Moodle dalam 10 menit terakhir</p>';
$string['pluginname'] = 'Peta siswa online';
$string['pluginname_desc'] = 'Membuat peta siswa online berdasarkan IP mereka';
