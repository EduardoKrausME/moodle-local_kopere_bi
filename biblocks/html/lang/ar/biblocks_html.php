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
 * Lang file
 *
 * @package   biblocks_html
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['html_block'] = 'كتلة HTML مع دعم Mustache';
$string['html_block_desc'] = '<p>يجب أن يتبع HTML المضاف إلى هذا الحقل صيغة <strong>Mustache</strong>، مما يسمح باستبدال البيانات ديناميكياً في صفحاتك. استخدم الأقواس المزدوجة <code>{{ }}</code> للإشارة مباشرةً إلى قيم أعمدة SQL داخل HTML، لضمان إدراج البيانات بشكل صحيح.</p>
<blockquote>
    <p>على سبيل المثال، باستخدام استعلام SQL التالي:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>يمكنك الإشارة إلى القيم التي تم إرجاعها في HTML باستخدام الصيغة التالية:</p>
    <pre>&lt;p&gt;البريد الإلكتروني: {{{email}}}&lt;/p&gt;
    &lt;p&gt;الاسم الكامل: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>في هذا المثال، أريد عرض عدة صفوف من نتيجة SQL، والتي تُرجع قائمة بالمستخدمين المسجلين باستخدام المصادقة اليدوية. استعلام SQL المستخدم لذلك هو:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>للتكرار على النتائج وعرض البيانات بتنسيق جدول، أستخدم <code>{{#lines}}</code> في القالب لتكرار المحتوى لكل سجل يتم إرجاعه. سيبدو القالب كما يلي:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;البريد الإلكتروني&lt;/th&gt;
        &lt;th&gt;الاسم الكامل&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>تسمح الأقواس الثلاثية <code>{{{ }}}</code> بإدراج القيمة دون تهريب HTML، وهو أمر مفيد لعرض محتوى قد يحتوي على وسوم HTML.</p>
<p>لمزيد من المعلومات حول استخدام قوالب Mustache في Moodle، راجع الوثائق الرسمية: <a href="https://moodledev.io/docs/guides/templates" target="_blank">دليل قوالب Moodle</a>.</p>';
$string['pluginname'] = 'كتلة HTML';
$string['pluginname_desc'] = 'يعرض كتلة HTML تحتوي على بيانات قادمة من قاعدة البيانات';
