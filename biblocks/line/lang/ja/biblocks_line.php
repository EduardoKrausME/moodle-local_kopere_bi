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
 * Lang ja file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>以下の SQL は、次の構造で返す必要があります。</p>
<ul>
    <li>最初の列には、X 軸名として使用されるテキストを含める必要があります。</li>
    <li>その他の列は、次のような構造にしてください。
        <ul>
            <li>列名は系列名として使用されます。翻訳文字列は、
                <a href="?classname=extra_langs&method=index" target="_blank">文字列ページ</a>で説明されているように使用できます。</li>
            <li>列の値は、グラフ内の系列データを表します。</li>
        </ul>
    </li>
</ul>
<blockquote>以下の例では、最初の列がコース名を返し、2 番目の列が各コースのニュース項目数を返します。
<pre>SELECT fullname,
       newsitems AS "コースニュース項目数"
  FROM mdl_course</pre></blockquote>
<blockquote>以下の例では、最初の列がコース名であることに加えて、翻訳された列名を持つ 2 本の追加の線がグラフに生成されます。
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = '折れ線グラフ';
$string['pluginname_desc'] = '折れ線グラフを生成します';
