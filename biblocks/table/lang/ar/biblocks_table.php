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
 * @package   biblocks_table
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'جدول البيانات';
$string['pluginname_desc'] = 'يعرض جدولاً مع ترقيم صفحات البيانات.';
$string['table_col_title'] = 'عنوان العمود';
$string['table_column_not_configured'] = 'الأعمدة غير مهيأة في هذا الجدول';
$string['table_edit_column'] = 'العمود';
$string['table_first_records'] = 'أول عشرة سجلات من الاستعلام';
$string['table_info_topo'] = 'أولاً، سترى معاينة لنتائج الاستعلام. بعد ذلك، ستظهر مجموعة من الأعمدة لتسمية العناوين وتحديد تنسيق بيانات كل عمود.';
$string['table_info_types'] = 'يمكنك الآن تعيين اسم للعمود، ثم تحديد التنسيق المطلوب وما إذا كنت تريد أي تنسيق إضافي باستخدام Mustache.
<ul>
    <li><strong>بدون تنسيق</strong>: يعرض المحتوى كما هو تماماً أو يطبق Mustache إذا أضفته.</li>
    <li><strong>عدم عرض هذا العمود</strong>: يخفي العمود المحدد في العرض، لكن البيانات تبقى متاحة للمعالجة باستخدام Mustache.</li>
    <ul><li>Mustache غير متاح</li></ul>
    <li><strong>أرقام</strong>: ينسق العمود لعرض القيم الرقمية فقط، مع تطبيق قواعد عرض الأرقام القياسية، مثل فواصل الآلاف والفواصل العشرية.</li>
    <ul><li>Mustache غير متاح</li></ul>
    <li><strong>تحويل العمود إلى الاسم الكامل "fullname()"</strong>: يشغل الدالة <code>fullname()</code> لإنشاء الاسم الكامل حسب اللغة، وسيتم تخزينه في العمود نفسه. لكي يعمل ذلك، يكون عمود <code>lastname</code> مطلوباً ويجب إخفاؤه إن أمكن.</li>
    <li><strong>تحويل معرف الطالب إلى صورة ملف شخصي</strong>: يستخدم المعرف الموجود في هذا العمود لإنشاء صورة الملف الشخصي.</li>
    <li><strong>حقل ثنائي لمرئي/غير مرئي</strong>: يستخدم القيمة الثنائية لتحديد الرؤية، حيث تعني "0"/"false" غير مرئي وتعني "1"/"true" مرئي.</li>
    <li><strong>حقل ثنائي لنشط/غير نشط</strong>: يستخدم القيمة الثنائية لتحديد الحالة، حيث تعني "0"/"false" غير نشط وتعني "1"/"true" نشط.</li>
    <li><strong>حقل "Time" منسق كتاريخ</strong>: يحول قيمة الوقت (timestamp) في العمود إلى تاريخ قابل للقراءة، مع عرض التاريخ فقط (اليوم/الشهر/السنة).</li>
    <ul><li>Mustache غير متاح</li></ul>
    <li><strong>حقل "Time" منسق كتاريخ ووقت</strong>: يعرض قيمة الوقت (timestamp) في العمود كتاريخ كامل، بما في ذلك الوقت (اليوم/الشهر/السنة والساعة:الدقيقة).</li>
    <ul><li>Mustache غير متاح</li></ul>
    <li><strong>حقل "Time" منسق كوقت</strong>: ينسق قيمة الوقت (timestamp) في العمود لعرض الوقت فقط (الساعة:الدقيقة)، مع حذف التاريخ.</li>
    <ul><li>Mustache غير متاح</li></ul>
</ul>';
$string['table_renderer_date'] = 'حقل "Time" منسق كتاريخ';
$string['table_renderer_datetime'] = 'حقل "Time" منسق كتاريخ ووقت';
$string['table_renderer_filesize'] = 'يحول إلى حجم البيانات على القرص';
$string['table_renderer_mustache'] = 'HTML العمود
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'عدم عرض هذا العمود';
$string['table_renderer_number'] = 'أرقام';
$string['table_renderer_seconds'] = 'حقل "Time" منسق كوقت';
$string['table_renderer_status'] = 'حقل ثنائي لنشط/غير نشط';
$string['table_renderer_title'] = 'تنسيق العمود';
$string['table_renderer_translate'] = 'استخدم get_string("identifier", "component") لترجمة العمود';
$string['table_renderer_userfullname'] = 'تحويل العمود إلى الاسم الكامل للطالب باستخدام الدالة "fullname()"';
$string['table_renderer_userphoto'] = 'تحويل معرف الطالب إلى صورة ملف شخصي';
$string['table_renderer_visible'] = 'حقل ثنائي لمرئي/غير مرئي';
