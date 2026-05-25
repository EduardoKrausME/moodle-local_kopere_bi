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
 * Lang sk file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Pamätajte, že nižšie uvedené SQL musí vrátiť nasledujúcu štruktúru:</p>
<ul>
    <li>Prvý stĺpec má obsahovať text, ktorý sa použije ako názvy osi X.</li>
    <li>Ostatné stĺpce majú byť štruktúrované takto:
        <ul>
            <li>Názov stĺpca sa použije ako názov série. Môžete použiť prekladové reťazce, ako je vysvetlené na
                <a href="?classname=extra_langs&method=index" target="_blank">stránke reťazcov</a>.</li>
            <li>Hodnota stĺpca bude predstavovať údaje série v grafe.</li>
        </ul>
    </li>
</ul>
<blockquote>V príklade nižšie prvý stĺpec vráti názov kurzu a druhý stĺpec vráti počet noviniek pre každý kurz:
<pre>SELECT fullname,
       newsitems AS "Počet noviniek kurzu"
  FROM mdl_course</pre></blockquote>
<blockquote>V príklade nižšie sa okrem prvého stĺpca s názvom kurzu v grafe vygenerujú dve ďalšie čiary s preloženými názvami stĺpcov:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Čiarový graf';
$string['pluginname_desc'] = 'Generuje čiarový graf';
