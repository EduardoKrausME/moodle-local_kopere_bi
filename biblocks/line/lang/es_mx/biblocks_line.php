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
 * Lang es_mx file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Recuerda que el SQL siguiente debe devolver la siguiente estructura:</p>
<ul>
    <li>La primera columna debe contener el texto que se usará como nombres del eje X.</li>
    <li>Las demás columnas deben estructurarse de la siguiente forma:
        <ul>
            <li>El nombre de la columna se usará como nombre de la serie. Puedes usar cadenas de traducción como se explica en la
                <a href="?classname=extra_langs&method=index" target="_blank">página de cadenas</a>.</li>
            <li>El valor de la columna representará los datos de la serie en la gráfica.</li>
        </ul>
    </li>
</ul>
<blockquote>En el siguiente ejemplo, la primera columna devuelve el nombre del curso, y la segunda columna devuelve la cantidad de noticias de cada curso:
<pre>SELECT fullname,
       newsitems AS "Cantidad de noticias del curso"
  FROM mdl_course</pre></blockquote>
<blockquote>En el siguiente ejemplo, además de que la primera columna sea el nombre del curso, se generan dos líneas adicionales en la gráfica, con nombres de columnas traducidos:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Gráfica de líneas';
$string['pluginname_desc'] = 'Genera una gráfica de líneas';
