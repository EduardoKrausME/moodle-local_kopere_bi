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

$string['pluginname'] = 'Таблиця даних';
$string['pluginname_desc'] = 'Відображає таблицю з посторінковим переглядом даних.';
$string['table_col_title'] = 'Заголовок стовпця';
$string['table_column_not_configured'] = 'Стовпці в цій таблиці не налаштовані';
$string['table_edit_column'] = 'Стовпець';
$string['table_first_records'] = 'Перші десять записів запиту';
$string['table_info_topo'] = 'Спочатку ви побачите попередній перегляд результатів запиту. Потім буде показано набір стовпців, щоб ви могли назвати заголовки та визначити формат даних кожного стовпця.';
$string['table_info_types'] = 'Тепер ви можете задати назву стовпця, а потім вказати потрібний формат і чи потрібне додаткове форматування за допомогою Mustache.
<ul>
    <li><strong>Без форматування</strong>: Відображає вміст саме таким, яким він є, або застосовує Mustache, якщо ви його додасте.</li>
    <li><strong>Не показувати цей стовпець</strong>: Приховує вибраний стовпець у перегляді, але дані залишаються доступними для обробки Mustache.</li>
    <ul><li>Mustache недоступний</li></ul>
    <li><strong>Числа</strong>: Форматує стовпець для відображення лише числових значень, застосовуючи стандартні правила показу чисел, зокрема розділювачі тисяч і десяткових знаків.</li>
    <ul><li>Mustache недоступний</li></ul>
    <li><strong>Перетворити стовпець на повне ім’я "fullname()"</strong>: Виконує функцію <code>fullname()</code> для створення повного імені відповідно до мови, яке буде збережено в цьому самому стовпці. Щоб це працювало, потрібен стовпець <code>lastname</code>, і його бажано приховати, якщо можливо.</li>
    <li><strong>Перетворити ID студента на фото профілю</strong>: Використовує ID з цього стовпця для створення фото профілю.</li>
    <li><strong>Бінарне поле для Видимий/Невидимий</strong>: Використовує бінарне значення для визначення видимості, де "0"/"false" означає невидимий, а "1"/"true" означає видимий.</li>
    <li><strong>Бінарне поле для Активний/Неактивний</strong>: Використовує бінарне значення для визначення статусу, де "0"/"false" означає Неактивний, а "1"/"true" означає Активний.</li>
    <li><strong>Поле "Time", відформатоване як дата</strong>: Перетворює значення часу (timestamp) у стовпці на читабельну дату, показуючи лише дату (день/місяць/рік).</li>
    <ul><li>Mustache недоступний</li></ul>
    <li><strong>Поле "Time", відформатоване як дата й час</strong>: Відображає значення часу (timestamp) у стовпці як повну дату, включно з часом (день/місяць/рік і години:хвилини).</li>
    <ul><li>Mustache недоступний</li></ul>
    <li><strong>Поле "Time", відформатоване як час</strong>: Форматує значення часу (timestamp) у стовпці, щоб показати лише час (години:хвилини), без дати.</li>
    <ul><li>Mustache недоступний</li></ul>
</ul>';
$string['table_renderer_date'] = 'Поле "Time", відформатоване як дата';
$string['table_renderer_datetime'] = 'Поле "Time", відформатоване як дата й час';
$string['table_renderer_filesize'] = 'Перетворює на розмір даних на диску';
$string['table_renderer_mustache'] = 'HTML стовпця
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Не показувати цей стовпець';
$string['table_renderer_number'] = 'Числа';
$string['table_renderer_seconds'] = 'Поле "Time", відформатоване як час';
$string['table_renderer_status'] = 'Бінарне поле для Активний/Неактивний';
$string['table_renderer_title'] = 'Форматування стовпця';
$string['table_renderer_translate'] = 'Використовувати get_string("identifier", "component") для перекладу стовпця';
$string['table_renderer_userfullname'] = 'Перетворити стовпець на повне ім’я студента за допомогою функції "fullname()"';
$string['table_renderer_userphoto'] = 'Перетворити ID студента на фото профілю';
$string['table_renderer_visible'] = 'Бінарне поле для Видимий/Невидимий';
