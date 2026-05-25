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

$string['html_block'] = 'HTML blok s podporou Mustache';
$string['html_block_desc'] = '<p>HTML pridané do tohto poľa by malo dodržiavať formát <strong>Mustache</strong>, ktorý umožňuje dynamické nahrádzanie údajov na vašich stránkach. Použite dvojité zložené zátvorky <code>{{ }}</code> na priame odkazovanie na hodnoty stĺpcov SQL v HTML, aby sa údaje vložili správne.</p>
<blockquote>
    <p>Napríklad pri použití nasledujúceho SQL dotazu:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Vrátené hodnoty môžete vo svojom HTML odkazovať pomocou nasledujúcej syntaxe:</p>
    <pre>&lt;p&gt;E-mail: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Celé meno: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>V tomto príklade chcem zobraziť viac riadkov z výsledku SQL, ktorý vracia zoznam používateľov zaregistrovaných s manuálnou autentifikáciou. Použitý SQL dotaz je:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Na prechádzanie výsledkov a zobrazenie údajov vo forme tabuľky používam v šablóne <code>{{#lines}}</code>, aby sa obsah zopakoval pre každý vrátený záznam. Šablóna by vyzerala takto:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;E-mail&lt;/th&gt;
        &lt;th&gt;Celé meno&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Trojité zložené zátvorky <code>{{{ }}}</code> umožňujú vložiť hodnotu bez HTML escapovania, čo je užitočné pri zobrazovaní obsahu, ktorý môže obsahovať HTML značky.</p>
<p>Viac informácií o používaní šablón Mustache v Moodle nájdete v oficiálnej dokumentácii: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Sprievodca šablónami Moodle</a>.</p>';
$string['pluginname'] = 'HTML blok';
$string['pluginname_desc'] = 'Zobrazuje HTML blok s údajmi pochádzajúcimi z databázy';
