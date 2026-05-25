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

$string['html_block'] = 'HTML-Block mit Mustache-Unterstützung';
$string['html_block_desc'] = '<p>Das in dieses Feld eingefügte HTML sollte dem Format <strong>Mustache</strong> folgen und damit eine dynamische Datensubstitution auf Ihren Seiten ermöglichen. Verwenden Sie doppelte geschweifte Klammern <code>{{ }}</code>, um SQL-Spaltenwerte direkt im HTML zu referenzieren und sicherzustellen, dass die Daten korrekt eingefügt werden.</p>
<blockquote>
    <p>Zum Beispiel mit der folgenden SQL-Abfrage:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Sie können die zurückgegebenen Werte in Ihrem HTML mit der folgenden Syntax referenzieren:</p>
    <pre>&lt;p&gt;E-Mail: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Vollständiger Name: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>In diesem Beispiel möchte ich mehrere Zeilen aus einem SQL-Ergebnis anzeigen, das eine Liste von Benutzern zurückgibt, die mit manueller Authentifizierung registriert sind. Das dafür verwendete SQL lautet:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Um die Ergebnisse zu durchlaufen und die Daten im Tabellenformat anzuzeigen, verwende ich <code>{{#lines}}</code> im Template, um den Inhalt für jeden zurückgegebenen Datensatz zu wiederholen. Das Template würde so aussehen:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;E-Mail&lt;/th&gt;
        &lt;th&gt;Vollständiger Name&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Dreifache geschweifte Klammern <code>{{{ }}}</code> ermöglichen das Einfügen des Werts ohne HTML-Escaping. Das ist nützlich, um Inhalte anzuzeigen, die HTML-Tags enthalten können.</p>
<p>Weitere Informationen zur Verwendung von Mustache-Templates in Moodle finden Sie in der offiziellen Dokumentation: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Moodle Templates Guide</a>.</p>';
$string['pluginname'] = 'HTML-Block';
$string['pluginname_desc'] = 'Zeigt einen HTML-Block mit Daten aus der Datenbank an';
