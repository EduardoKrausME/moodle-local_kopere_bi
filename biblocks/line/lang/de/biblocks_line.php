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
 * Lang de file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Beachten Sie, dass das folgende SQL die folgende Struktur zurückgeben muss:</p>
<ul>
    <li>Die erste Spalte sollte den Text enthalten, der als Beschriftung der X-Achse verwendet wird.</li>
    <li>Die weiteren Spalten sollten wie folgt aufgebaut sein:
        <ul>
            <li>Der Spaltenname wird als Name der Datenreihe verwendet. Sie können Übersetzungsstrings verwenden, wie auf der
                <a href="?classname=extra_langs&method=index" target="_blank">String-Seite</a> erklärt.</li>
            <li>Der Spaltenwert stellt die Daten der Reihe im Diagramm dar.</li>
        </ul>
    </li>
</ul>
<blockquote>Im folgenden Beispiel gibt die erste Spalte den Kursnamen zurück, und die zweite Spalte gibt die Anzahl der Nachrichtenbeiträge für jeden Kurs zurück:
<pre>SELECT fullname,
       newsitems AS "Anzahl der Kursnachrichten"
  FROM mdl_course</pre></blockquote>
<blockquote>Im folgenden Beispiel werden neben der ersten Spalte als Kursname zwei zusätzliche Linien im Diagramm erzeugt, mit übersetzten Spaltennamen:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Liniendiagramm';
$string['pluginname_desc'] = 'Erzeugt ein Liniendiagramm';
