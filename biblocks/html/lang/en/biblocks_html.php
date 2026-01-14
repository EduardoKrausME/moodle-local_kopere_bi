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
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'HTML Block';
$string['pluginname_desc'] = 'Displays an HTML Block with data coming from the database';
$string['html_block'] = 'HTML Block with Mustache support';
$string['html_block_desc'] = '<p>The HTML added to this field should follow the <strong>Mustache</strong> format, allowing dynamic data substitution in your pages. Use double braces <code>{{ }}</code> to reference SQL column values directly in the HTML, ensuring that the data is correctly inserted.</p>
<blockquote>
    <p>For example, using the following SQL query:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>You can reference the returned values in your HTML using the following syntax:</p>
    <pre>&lt;p&gt;Email: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Full name: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>In this example, I want to display multiple rows from an SQL result, which returns a list of users registered with manual authentication. The SQL used for this is:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>To iterate over the results and display the data in a table format, I use the <code>{{#lines}}</code> in the template to repeat the content for each returned record. The template would look like this:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;E-mail&lt;/th&gt;
        &lt;th&gt;Full name&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Triple braces <code>{{{ }}}</code> allow the value to be inserted without escaping HTML, which is useful for displaying content that may contain HTML tags.</p>
<p>For more information on using Mustache templates in Moodle, see the official documentation: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Moodle Templates Guide</a>.</p>';
