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

$string['html_block'] = 'HTML-блок із підтримкою Mustache';
$string['html_block_desc'] = '<p>HTML, доданий у це поле, має відповідати формату <strong>Mustache</strong>, що дає змогу динамічно підставляти дані на ваших сторінках. Використовуйте подвійні фігурні дужки <code>{{ }}</code>, щоб безпосередньо посилатися на значення SQL-стовпців у HTML і забезпечити правильне вставлення даних.</p>
<blockquote>
    <p>Наприклад, використовуючи такий SQL-запит:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Ви можете посилатися на повернені значення у своєму HTML за допомогою такого синтаксису:</p>
    <pre>&lt;p&gt;Електронна пошта: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Повне ім\'я: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>У цьому прикладі потрібно відобразити кілька рядків із результату SQL, який повертає список користувачів, зареєстрованих із ручною автентифікацією. Для цього використовується такий SQL:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Щоб пройтися результатами та відобразити дані у форматі таблиці, у шаблоні використовується <code>{{#lines}}</code>, щоб повторити вміст для кожного поверненого запису. Шаблон виглядатиме так:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;Електронна пошта&lt;/th&gt;
        &lt;th&gt;Повне ім\'я&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Потрійні фігурні дужки <code>{{{ }}}</code> дають змогу вставити значення без HTML-екранування, що корисно для відображення вмісту, який може містити HTML-теги.</p>
<p>Докладніше про використання шаблонів Mustache у Moodle дивіться в офіційній документації: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Посібник із шаблонів Moodle</a>.</p>';
$string['pluginname'] = 'HTML-блок';
$string['pluginname_desc'] = 'Відображає HTML-блок із даними з бази даних';
