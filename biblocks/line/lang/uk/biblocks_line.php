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
 * Lang uk file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Пам’ятайте, що наведений нижче SQL має повернути дані з такою структурою:</p>
<ul>
    <li>Перший стовпець має містити текст, який буде використано як назви осі X.</li>
    <li>Інші стовпці мають бути структуровані так:
        <ul>
            <li>Назва стовпця буде використана як назва серії. Ви можете використовувати рядки перекладу, як пояснено на
                <a href="?classname=extra_langs&method=index" target="_blank">сторінці рядків</a>.</li>
            <li>Значення стовпця представлятиме дані серії на діаграмі.</li>
        </ul>
    </li>
</ul>
<blockquote>У наведеному нижче прикладі перший стовпець повертає назву курсу, а другий стовпець повертає кількість новин для кожного курсу:
<pre>SELECT fullname,
       newsitems AS "Кількість новин курсу"
  FROM mdl_course</pre></blockquote>
<blockquote>У наведеному нижче прикладі, окрім того що перший стовпець є назвою курсу, на діаграмі створюються дві додаткові лінії з перекладеними назвами стовпців:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Лінійна діаграма';
$string['pluginname_desc'] = 'Створює лінійну діаграму';
