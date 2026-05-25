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
 * Lang ar file
 *
 * @package   biblocks_line
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['line_sql_warning'] = '<p>تذكّر أن استعلام SQL أدناه يجب أن يُرجع البيانات بالبنية التالية:</p>
<ul>
    <li>يجب أن يحتوي العمود الأول على النص الذي سيُستخدم كأسماء لمحور X.</li>
    <li>يجب تنظيم الأعمدة الأخرى على النحو التالي:
        <ul>
            <li>سيُستخدم اسم العمود كاسم للسلسلة. يمكنك استخدام نصوص الترجمة كما هو موضح في
                <a href="?classname=extra_langs&method=index" target="_blank">صفحة النصوص</a>.</li>
            <li>ستمثل قيمة العمود بيانات السلسلة في المخطط.</li>
        </ul>
    </li>
</ul>
<blockquote>في المثال أدناه، يُرجع العمود الأول اسم المقرر، ويُرجع العمود الثاني عدد عناصر الأخبار لكل مقرر:
<pre>SELECT fullname,
       newsitems AS "عدد أخبار المقرر"
  FROM mdl_course</pre></blockquote>
<blockquote>في المثال أدناه، إضافةً إلى أن العمود الأول هو اسم المقرر، يتم إنشاء خطين إضافيين في المخطط بأسماء أعمدة مترجمة:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['pluginname'] = 'مخطط خطي';
$string['pluginname_desc'] = 'ينشئ مخططًا خطيًا';
