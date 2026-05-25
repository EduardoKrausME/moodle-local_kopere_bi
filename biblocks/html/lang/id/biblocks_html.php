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
 * @package   biblocks_html
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['html_block'] = 'Blok HTML dengan dukungan Mustache';
$string['html_block_desc'] = '<p>HTML yang ditambahkan ke kolom ini harus mengikuti format <strong>Mustache</strong>, sehingga memungkinkan penggantian data secara dinamis di halaman Anda. Gunakan kurung kurawal ganda <code>{{ }}</code> untuk merujuk langsung ke nilai kolom SQL di dalam HTML, agar data dimasukkan dengan benar.</p>
<blockquote>
    <p>Sebagai contoh, menggunakan kueri SQL berikut:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Anda dapat merujuk nilai yang dikembalikan di HTML Anda menggunakan sintaks berikut:</p>
    <pre>&lt;p&gt;Email: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Nama lengkap: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>Dalam contoh ini, saya ingin menampilkan beberapa baris dari hasil SQL, yang mengembalikan daftar pengguna yang terdaftar dengan autentikasi manual. SQL yang digunakan adalah:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Untuk menelusuri hasil dan menampilkan data dalam format tabel, saya menggunakan <code>{{#lines}}</code> pada template untuk mengulang konten bagi setiap record yang dikembalikan. Template-nya akan terlihat seperti ini:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;Email&lt;/th&gt;
        &lt;th&gt;Nama lengkap&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Kurung kurawal tiga <code>{{{ }}}</code> memungkinkan nilai dimasukkan tanpa escaping HTML, yang berguna untuk menampilkan konten yang mungkin berisi tag HTML.</p>
<p>Untuk informasi lebih lanjut tentang penggunaan template Mustache di Moodle, lihat dokumentasi resmi: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Panduan Template Moodle</a>.</p>';
$string['pluginname'] = 'Blok HTML';
$string['pluginname_desc'] = 'Menampilkan Blok HTML dengan data yang berasal dari basis data';
