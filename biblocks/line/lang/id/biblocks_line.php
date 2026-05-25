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
 * Lang id file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Ingat bahwa SQL di bawah ini harus mengembalikan struktur berikut:</p>
<ul>
    <li>Kolom pertama harus berisi teks yang akan digunakan sebagai nama pada sumbu X.</li>
    <li>Kolom lainnya harus disusun sebagai berikut:
        <ul>
            <li>Nama kolom akan digunakan sebagai nama seri. Anda dapat menggunakan string terjemahan seperti yang dijelaskan di
                <a href="?classname=extra_langs&method=index" target="_blank">halaman string</a>.</li>
            <li>Nilai kolom akan mewakili data seri pada grafik.</li>
        </ul>
    </li>
</ul>
<blockquote>Pada contoh di bawah ini, kolom pertama mengembalikan nama kursus, dan kolom kedua mengembalikan jumlah item berita untuk setiap kursus:
<pre>SELECT fullname,
       newsitems AS "Jumlah berita kursus"
  FROM mdl_course</pre></blockquote>
<blockquote>Pada contoh di bawah ini, selain kolom pertama sebagai nama kursus, dua garis tambahan dibuat pada grafik, dengan nama kolom yang diterjemahkan:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Grafik Garis';
$string['pluginname_desc'] = 'Menghasilkan grafik garis';
