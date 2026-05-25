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
 * Lang it file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Ricorda che l\'SQL seguente deve restituire la seguente struttura:</p>
<ul>
    <li>La prima colonna deve contenere il testo che verrà usato come nome dell\'asse X.</li>
    <li>Le altre colonne devono essere strutturate come segue:
        <ul>
            <li>Il nome della colonna verrà usato come nome della serie. Puoi usare stringhe di traduzione come spiegato nella
                <a href="?classname=extra_langs&method=index" target="_blank">pagina delle stringhe</a>.</li>
            <li>Il valore della colonna rappresenterà i dati della serie nel grafico.</li>
        </ul>
    </li>
</ul>
<blockquote>Nell\'esempio seguente, la prima colonna restituisce il nome del corso e la seconda colonna restituisce il numero di notizie per ciascun corso:
<pre>SELECT fullname,
       newsitems AS "Numero di notizie del corso"
  FROM mdl_course</pre></blockquote>
<blockquote>Nell\'esempio seguente, oltre al fatto che la prima colonna sia il nome del corso, vengono generate due linee aggiuntive nel grafico, con nomi di colonna tradotti:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Grafico a linee';
$string['pluginname_desc'] = 'Genera un grafico a linee';
