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
 * Lang ru file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Помните, что приведённый ниже SQL должен возвращать данные со следующей структурой:</p>
<ul>
    <li>Первый столбец должен содержать текст, который будет использоваться как подписи оси X.</li>
    <li>Остальные столбцы должны быть структурированы следующим образом:
        <ul>
            <li>Имя столбца будет использоваться как название серии. Можно использовать строки перевода, как описано на
                <a href="?classname=extra_langs&method=index" target="_blank">странице строк</a>.</li>
            <li>Значение столбца будет представлять данные серии на графике.</li>
        </ul>
    </li>
</ul>
<blockquote>В примере ниже первый столбец возвращает название курса, а второй столбец возвращает количество новостей для каждого курса:
<pre>SELECT fullname,
       newsitems AS "Количество новостей курса"
  FROM mdl_course</pre></blockquote>
<blockquote>В примере ниже, помимо того что первый столбец является названием курса, на графике создаются две дополнительные линии с переведёнными названиями столбцов:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Линейный график';
$string['pluginname_desc'] = 'Создаёт линейный график';
