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

$string['html_block'] = 'Blocco HTML con supporto Mustache';
$string['html_block_desc'] = '<p>L\'HTML aggiunto a questo campo deve seguire il formato <strong>Mustache</strong>, consentendo la sostituzione dinamica dei dati nelle pagine. Usa le doppie parentesi graffe <code>{{ }}</code> per fare riferimento direttamente ai valori delle colonne SQL nell\'HTML, assicurando che i dati vengano inseriti correttamente.</p>
<blockquote>
    <p>Ad esempio, usando la seguente query SQL:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Puoi fare riferimento ai valori restituiti nel tuo HTML usando la seguente sintassi:</p>
    <pre>&lt;p&gt;E-mail: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Nome completo: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>In questo esempio, voglio visualizzare più righe da un risultato SQL, che restituisce un elenco di utenti registrati con autenticazione manuale. L\'SQL usato per questo è:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Per scorrere i risultati e visualizzare i dati in formato tabella, uso <code>{{#lines}}</code> nel template per ripetere il contenuto per ogni record restituito. Il template sarebbe così:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;E-mail&lt;/th&gt;
        &lt;th&gt;Nome completo&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Le triple parentesi graffe <code>{{{ }}}</code> consentono di inserire il valore senza escape HTML, utile per visualizzare contenuti che possono contenere tag HTML.</p>
<p>Per maggiori informazioni sull\'uso dei template Mustache in Moodle, consulta la documentazione ufficiale: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Guida ai Template di Moodle</a>.</p>';
$string['pluginname'] = 'Blocco HTML';
$string['pluginname_desc'] = 'Visualizza un Blocco HTML con dati provenienti dal database';
