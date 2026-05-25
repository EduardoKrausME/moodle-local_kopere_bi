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
 * @package   biblocks_maps
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['maps_1_city'] = '{a1} ومدينة أخرى';
$string['maps_many_city'] = '{a1} و{a2} مدن أخرى';
$string['maps_online'] = '{a1} طالب متصل';
$string['maps_onlines'] = '{a1} طلاب متصلون';
$string['maps_sql_warning'] =
    '<p>تذكّر أن استعلام SQL أدناه يجب أن يُرجع عمودًا واحدًا فقط، ويجب أن يحتوي هذا العمود على عنوان IP صالح.<br>مثال: يعيد استعلام SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> جميع الطلاب الذين دخلوا إلى Moodle خلال آخر 10 دقائق</p>';
$string['pluginname'] = 'خريطة الطلاب المتصلين';
$string['pluginname_desc'] = 'ينشئ خريطة للطلاب المتصلين بناءً على عناوين IP الخاصة بهم';
