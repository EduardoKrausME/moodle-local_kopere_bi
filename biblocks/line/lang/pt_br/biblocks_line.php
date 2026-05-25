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
 * Lang pt_br file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>Lembre-se de que o SQL abaixo deve retornar com a seguinte estrutura:</p>
<ul>
    <li>A primeira coluna deve conter o texto que será usado como nome no eixo X.</li>
    <li>As demais colunas devem ser estruturadas da seguinte forma:
        <ul>
            <li>O nome da coluna será usado como nome da série. Você pode usar strings de tradução conforme explicado na
                <a href="?classname=extra_langs&method=index" target="_blank">página de strings</a>.</li>
            <li>O valor da coluna representará os dados da série no gráfico.</li>
        </ul>
    </li>
</ul>
<blockquote>No exemplo abaixo, a primeira coluna retorna o nome do curso, e a segunda coluna retorna o número de notícias de cada curso:
<pre>SELECT fullname,
       newsitems AS "Quantidade de notícias do curso"
  FROM mdl_course</pre></blockquote>
<blockquote>No exemplo abaixo, além de a primeira coluna ser o nome do curso, são geradas duas linhas adicionais no gráfico, com nomes de colunas traduzidos:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'Gráfico de linhas';
$string['pluginname_desc'] = 'Gera um gráfico de linhas';
