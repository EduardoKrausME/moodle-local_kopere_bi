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

$string['pluginname'] = 'Datentabelle';
$string['pluginname_desc'] = 'Zeigt eine Tabelle mit Datenpaginierung an.';
$string['table_col_title'] = 'Spaltentitel';
$string['table_column_not_configured'] = 'In dieser Tabelle sind keine Spalten konfiguriert';
$string['table_edit_column'] = 'Spalte';
$string['table_first_records'] = 'Die ersten zehn Datensätze der Abfrage';
$string['table_info_topo'] = 'Zuerst sehen Sie eine Vorschau der Suchergebnisse. Danach wird eine Reihe von Spalten angezeigt, damit Sie die Titel benennen und das Format der Daten jeder Spalte festlegen können.';
$string['table_info_types'] = 'Sie können jetzt einen Namen für die Spalte festlegen und anschließend das gewünschte Format angeben sowie bestimmen, ob zusätzliche Formatierung mit Mustache verwendet werden soll.
<ul>
    <li><strong>Keine Formatierung</strong>: Zeigt den Inhalt genau so an, wie er ist, oder wendet Mustache an, wenn Sie es hinzufügen.</li>
    <li><strong>Diese Spalte nicht anzeigen</strong>: Blendet die ausgewählte Spalte in der Ansicht aus, die Daten bleiben jedoch für die Verarbeitung mit Mustache verfügbar.</li>
    <ul><li>Mustache nicht verfügbar</li></ul>
    <li><strong>Zahlen</strong>: Formatiert die Spalte so, dass nur numerische Werte angezeigt werden, und wendet Standardregeln für die Zahlendarstellung an, z. B. Tausender- und Dezimaltrennzeichen.</li>
    <ul><li>Mustache nicht verfügbar</li></ul>
    <li><strong>Spalte in vollständigen Namen "fullname()" umwandeln</strong>: Führt die Funktion <code>fullname()</code> aus, um den vollständigen Namen anhand der Sprache zu erzeugen, der in derselben Spalte gespeichert wird. Damit dies funktioniert, ist die Spalte <code>lastname</code> erforderlich und sollte möglichst ausgeblendet werden.</li>
    <li><strong>Teilnehmer-ID in Profilbild umwandeln</strong>: Verwendet die ID aus dieser Spalte, um das Profilbild zu erstellen.</li>
    <li><strong>Binärfeld für Sichtbar/Unsichtbar</strong>: Verwendet den binären Wert zur Bestimmung der Sichtbarkeit, wobei "0"/"false" unsichtbar und "1"/"true" sichtbar bedeutet.</li>
    <li><strong>Binärfeld für Aktiv/Inaktiv</strong>: Verwendet den binären Wert zur Bestimmung des Status, wobei "0"/"false" Inaktiv und "1"/"true" Aktiv bedeutet.</li>
    <li><strong>Feld "Time" als Datum formatiert</strong>: Wandelt den Zeitwert (Timestamp) in der Spalte in ein lesbares Datum um und zeigt nur das Datum an (Tag/Monat/Jahr).</li>
    <ul><li>Mustache nicht verfügbar</li></ul>
    <li><strong>Feld "Time" als Datum und Uhrzeit formatiert</strong>: Zeigt den Zeitwert (Timestamp) in der Spalte als vollständiges Datum einschließlich Uhrzeit an (Tag/Monat/Jahr und Stunden:Minuten).</li>
    <ul><li>Mustache nicht verfügbar</li></ul>
    <li><strong>Feld "Time" als Uhrzeit formatiert</strong>: Formatiert den Zeitwert (Timestamp) in der Spalte so, dass nur die Uhrzeit angezeigt wird (Stunden:Minuten), ohne das Datum.</li>
    <ul><li>Mustache nicht verfügbar</li></ul>
</ul>';
$string['table_renderer_date'] = 'Feld "Time" als Datum formatiert';
$string['table_renderer_datetime'] = 'Feld "Time" als Datum und Uhrzeit formatiert';
$string['table_renderer_filesize'] = 'In Datengröße auf dem Datenträger umwandeln';
$string['table_renderer_mustache'] = 'HTML der Spalte
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Diese Spalte nicht anzeigen';
$string['table_renderer_number'] = 'Zahlen';
$string['table_renderer_seconds'] = 'Feld "Time" als Uhrzeit formatiert';
$string['table_renderer_status'] = 'Binärfeld für Aktiv/Inaktiv';
$string['table_renderer_title'] = 'Spaltenformatierung';
$string['table_renderer_translate'] = 'get_string("identifier", "component") verwenden, um die Spalte zu übersetzen';
$string['table_renderer_userfullname'] = 'Die Spalte mit der Funktion "fullname()" in den vollständigen Namen des Teilnehmers umwandeln';
$string['table_renderer_userphoto'] = 'Teilnehmer-ID in Profilbild umwandeln';
$string['table_renderer_visible'] = 'Binärfeld für Sichtbar/Unsichtbar';
