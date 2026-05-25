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
 * Lang fr file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Rappelez-vous que la requête SQL ci-dessous doit retourner la structure suivante :</p>
<ul>
    <li>La première colonne doit contenir le texte qui sera utilisé comme noms de l’axe X.</li>
    <li>Les autres colonnes doivent être structurées comme suit :
        <ul>
            <li>Le nom de la colonne sera utilisé comme nom de la série. Vous pouvez utiliser des chaînes de traduction comme expliqué sur la
                <a href="?classname=extra_langs&method=index" target="_blank">page des chaînes</a>.</li>
            <li>La valeur de la colonne représentera les données de la série dans le graphique.</li>
        </ul>
    </li>
</ul>
<blockquote>Dans l’exemple ci-dessous, la première colonne retourne le nom du cours, et la deuxième colonne retourne le nombre d’actualités pour chaque cours :
<pre>SELECT fullname,
       newsitems AS "Nombre d’actualités du cours"
  FROM mdl_course</pre></blockquote>
<blockquote>Dans l’exemple ci-dessous, en plus de la première colonne qui correspond au nom du cours, deux lignes supplémentaires sont générées dans le graphique, avec des noms de colonnes traduits :
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Graphique en courbes';
$string['pluginname_desc'] = 'Génère un graphique en courbes';
