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
 * @package   biblocks_table
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Tabel data';
$string['pluginname_desc'] = 'Menampilkan tabel dengan paginasi data.';
$string['table_col_title'] = 'Judul kolom';
$string['table_column_not_configured'] = 'Kolom belum dikonfigurasi di tabel ini';
$string['table_edit_column'] = 'Kolom';
$string['table_first_records'] = 'Sepuluh rekaman pertama dari kueri';
$string['table_info_topo'] = 'Pertama, Anda akan melihat pratinjau hasil kueri. Setelah itu, serangkaian kolom akan ditampilkan agar Anda dapat memberi nama judul dan menentukan format data untuk setiap kolom.';
$string['table_info_types'] = 'Sekarang Anda dapat menetapkan nama untuk kolom, lalu menentukan format yang diinginkan serta apakah ingin menggunakan pemformatan tambahan dengan Mustache.
<ul>
    <li><strong>Tanpa pemformatan</strong>: Menampilkan konten persis seperti aslinya atau menerapkan Mustache jika Anda menambahkannya.</li>
    <li><strong>Jangan tampilkan kolom ini</strong>: Menyembunyikan kolom yang dipilih pada tampilan, tetapi datanya tetap tersedia untuk diproses dengan Mustache.</li>
    <ul><li>Mustache tidak tersedia</li></ul>
    <li><strong>Angka</strong>: Memformat kolom untuk hanya menampilkan nilai numerik, dengan menerapkan aturan tampilan angka standar, seperti pemisah ribuan dan desimal.</li>
    <ul><li>Mustache tidak tersedia</li></ul>
    <li><strong>Ubah kolom menjadi nama lengkap "fullname()"</strong>: Menjalankan fungsi <code>fullname()</code> untuk menghasilkan nama lengkap berdasarkan bahasa, yang akan disimpan di kolom yang sama. Agar ini berfungsi, kolom <code>lastname</code> diperlukan dan sebaiknya disembunyikan jika memungkinkan.</li>
    <li><strong>Ubah ID siswa menjadi foto profil</strong>: Menggunakan ID dari kolom ini untuk membuat foto profil.</li>
    <li><strong>Kolom biner untuk Terlihat/Tidak terlihat</strong>: Menggunakan nilai biner untuk menentukan visibilitas, di mana "0"/"false" berarti tidak terlihat dan "1"/"true" berarti terlihat.</li>
    <li><strong>Kolom biner untuk Aktif/Tidak aktif</strong>: Menggunakan nilai biner untuk menentukan status, di mana "0"/"false" berarti Tidak aktif dan "1"/"true" berarti Aktif.</li>
    <li><strong>Kolom "Time" diformat sebagai tanggal</strong>: Mengubah nilai waktu (timestamp) pada kolom menjadi tanggal yang mudah dibaca, hanya menampilkan tanggal (hari/bulan/tahun).</li>
    <ul><li>Mustache tidak tersedia</li></ul>
    <li><strong>Kolom "Time" diformat sebagai tanggal dan waktu</strong>: Menampilkan nilai waktu (timestamp) pada kolom sebagai tanggal lengkap, termasuk waktu (hari/bulan/tahun dan jam:menit).</li>
    <ul><li>Mustache tidak tersedia</li></ul>
    <li><strong>Kolom "Time" diformat sebagai waktu</strong>: Memformat nilai waktu (timestamp) pada kolom untuk hanya menampilkan waktu (jam:menit), tanpa tanggal.</li>
    <ul><li>Mustache tidak tersedia</li></ul>
</ul>';
$string['table_renderer_date'] = 'Kolom "Time" diformat sebagai tanggal';
$string['table_renderer_datetime'] = 'Kolom "Time" diformat sebagai tanggal dan waktu';
$string['table_renderer_filesize'] = 'Mengubah menjadi ukuran data di disk';
$string['table_renderer_mustache'] = 'HTML kolom
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Jangan tampilkan kolom ini';
$string['table_renderer_number'] = 'Angka';
$string['table_renderer_seconds'] = 'Kolom "Time" diformat sebagai waktu';
$string['table_renderer_status'] = 'Kolom biner untuk Aktif/Tidak aktif';
$string['table_renderer_title'] = 'Pemformatan kolom';
$string['table_renderer_translate'] = 'Gunakan get_string("identifier", "component") untuk menerjemahkan kolom';
$string['table_renderer_userfullname'] = 'Ubah kolom menjadi nama lengkap siswa dengan fungsi "fullname()"';
$string['table_renderer_userphoto'] = 'Ubah ID siswa menjadi foto profil';
$string['table_renderer_visible'] = 'Kolom biner untuk Terlihat/Tidak terlihat';
