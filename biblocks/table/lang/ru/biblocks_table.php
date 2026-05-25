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

$string['pluginname'] = 'Таблица данных';
$string['pluginname_desc'] = 'Отображает таблицу с постраничным выводом данных.';
$string['table_col_title'] = 'Заголовок столбца';
$string['table_column_not_configured'] = 'Столбцы в этой таблице не настроены';
$string['table_edit_column'] = 'Столбец';
$string['table_first_records'] = 'Первые десять записей запроса';
$string['table_info_topo'] = 'Сначала вы увидите предварительный просмотр результатов запроса. Затем будет показан набор столбцов, чтобы вы могли задать заголовки и определить формат данных каждого столбца.';
$string['table_info_types'] = 'Теперь вы можете задать имя столбца, затем указать нужный формат и определить, требуется ли дополнительное форматирование с помощью Mustache.
<ul>
    <li><strong>Без форматирования</strong>: Отображает содержимое точно в исходном виде или применяет Mustache, если вы его добавите.</li>
    <li><strong>Не показывать этот столбец</strong>: Скрывает выбранный столбец в представлении, но данные остаются доступными для обработки Mustache.</li>
    <ul><li>Mustache недоступен</li></ul>
    <li><strong>Числа</strong>: Форматирует столбец для отображения только числовых значений, применяя стандартные правила отображения чисел, например разделители тысяч и десятичных знаков.</li>
    <ul><li>Mustache недоступен</li></ul>
    <li><strong>Преобразовать столбец в полное имя "fullname()"</strong>: Выполняет функцию <code>fullname()</code> для создания полного имени с учетом языка, которое будет сохранено в этом же столбце. Для работы требуется столбец <code>lastname</code>, и его следует скрыть, если возможно.</li>
    <li><strong>Преобразовать ID студента в фотографию профиля</strong>: Использует ID из этого столбца для создания фотографии профиля.</li>
    <li><strong>Двоичное поле для Видимый/Невидимый</strong>: Использует двоичное значение для определения видимости, где "0"/"false" означает невидимый, а "1"/"true" означает видимый.</li>
    <li><strong>Двоичное поле для Активный/Неактивный</strong>: Использует двоичное значение для определения статуса, где "0"/"false" означает Неактивный, а "1"/"true" означает Активный.</li>
    <li><strong>Поле "Time", отформатированное как дата</strong>: Преобразует значение времени (timestamp) в столбце в читаемую дату, отображая только дату (день/месяц/год).</li>
    <ul><li>Mustache недоступен</li></ul>
    <li><strong>Поле "Time", отформатированное как дата и время</strong>: Отображает значение времени (timestamp) в столбце как полную дату, включая время (день/месяц/год и часы:минуты).</li>
    <ul><li>Mustache недоступен</li></ul>
    <li><strong>Поле "Time", отформатированное как время</strong>: Форматирует значение времени (timestamp) в столбце для отображения только времени (часы:минуты), без даты.</li>
    <ul><li>Mustache недоступен</li></ul>
</ul>';
$string['table_renderer_date'] = 'Поле "Time", отформатированное как дата';
$string['table_renderer_datetime'] = 'Поле "Time", отформатированное как дата и время';
$string['table_renderer_filesize'] = 'Преобразует в размер данных на диске';
$string['table_renderer_mustache'] = 'HTML столбца
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Не отображать этот столбец';
$string['table_renderer_number'] = 'Числа';
$string['table_renderer_seconds'] = 'Поле "Time", отформатированное как время';
$string['table_renderer_status'] = 'Двоичное поле для Активный/Неактивный';
$string['table_renderer_title'] = 'Форматирование столбца';
$string['table_renderer_translate'] = 'Использовать get_string("identifier", "component") для перевода столбца';
$string['table_renderer_userfullname'] = 'Преобразовать столбец в полное имя студента с помощью функции "fullname()"';
$string['table_renderer_userphoto'] = 'Преобразовать ID студента в фотографию профиля';
$string['table_renderer_visible'] = 'Двоичное поле для Видимый/Невидимый';
