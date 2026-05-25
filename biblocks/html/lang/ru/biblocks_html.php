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

$string['html_block'] = 'HTML-блок с поддержкой Mustache';
$string['html_block_desc'] = '<p>HTML, добавленный в это поле, должен соответствовать формату <strong>Mustache</strong>, что позволяет динамически подставлять данные на ваших страницах. Используйте двойные фигурные скобки <code>{{ }}</code>, чтобы напрямую ссылаться на значения SQL-столбцов в HTML и обеспечить правильную вставку данных.</p>
<blockquote>
    <p>Например, с использованием следующего SQL-запроса:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Вы можете ссылаться на возвращённые значения в своём HTML, используя следующий синтаксис:</p>
    <pre>&lt;p&gt;Электронная почта: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Полное имя: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>В этом примере я хочу отобразить несколько строк из результата SQL, который возвращает список пользователей, зарегистрированных с ручной аутентификацией. Для этого используется следующий SQL:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Чтобы перебрать результаты и отобразить данные в виде таблицы, я использую <code>{{#lines}}</code> в шаблоне для повторения содержимого для каждой возвращённой записи. Шаблон будет выглядеть так:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;Электронная почта&lt;/th&gt;
        &lt;th&gt;Полное имя&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Тройные фигурные скобки <code>{{{ }}}</code> позволяют вставлять значение без HTML-экранирования, что полезно для отображения содержимого, которое может содержать HTML-теги.</p>
<p>Дополнительную информацию об использовании шаблонов Mustache в Moodle смотрите в официальной документации: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Руководство по шаблонам Moodle</a>.</p>';
$string['pluginname'] = 'HTML-блок';
$string['pluginname_desc'] = 'Отображает HTML-блок с данными из базы данных';
