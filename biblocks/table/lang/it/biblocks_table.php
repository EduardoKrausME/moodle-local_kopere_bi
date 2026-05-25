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

$string['pluginname'] = 'Tabella dati';
$string['pluginname_desc'] = 'Visualizza una tabella con paginazione dei dati.';
$string['table_col_title'] = 'Titolo della colonna';
$string['table_column_not_configured'] = 'Colonne non configurate in questa tabella';
$string['table_edit_column'] = 'Colonna';
$string['table_first_records'] = 'I primi dieci record della query';
$string['table_info_topo'] = 'Per prima cosa vedrai un\'anteprima dei risultati della query. Poi verrà presentata una serie di colonne per assegnare i titoli e definire il formato dei dati di ciascuna colonna.';
$string['table_info_types'] = 'Ora puoi impostare un nome per la colonna e poi specificare il formato desiderato, oltre a indicare se vuoi una formattazione aggiuntiva con Mustache.
<ul>
    <li><strong>Nessuna formattazione</strong>: Visualizza il contenuto esattamente così com\'è oppure applica Mustache se lo aggiungi.</li>
    <li><strong>Non mostrare questa colonna</strong>: Nasconde la colonna selezionata nella vista, ma i dati restano disponibili per l\'elaborazione con Mustache.</li>
    <ul><li>Mustache non disponibile</li></ul>
    <li><strong>Numeri</strong>: Formatta la colonna per visualizzare solo valori numerici, applicando le regole standard di visualizzazione dei numeri, come separatori delle migliaia e dei decimali.</li>
    <ul><li>Mustache non disponibile</li></ul>
    <li><strong>Converti colonna in nome completo "fullname()"</strong>: Esegue la funzione <code>fullname()</code> per generare il nome completo in base alla lingua, che verrà memorizzato in questa stessa colonna. Per funzionare, la colonna <code>lastname</code> è obbligatoria e dovrebbe essere nascosta, se possibile.</li>
    <li><strong>Converti ID dello studente in immagine del profilo</strong>: Usa l\'ID di questa colonna per creare l\'immagine del profilo.</li>
    <li><strong>Campo binario per Visibile/Invisibile</strong>: Usa il valore binario per determinare la visibilità, dove "0"/"false" significa invisibile e "1"/"true" significa visibile.</li>
    <li><strong>Campo binario per Attivo/Inattivo</strong>: Usa il valore binario per determinare lo stato, dove "0"/"false" significa Inattivo e "1"/"true" significa Attivo.</li>
    <li><strong>Campo "Time" formattato come data</strong>: Converte il valore temporale (timestamp) nella colonna in una data leggibile, visualizzando solo la data (giorno/mese/anno).</li>
    <ul><li>Mustache non disponibile</li></ul>
    <li><strong>Campo "Time" formattato come data e ora</strong>: Visualizza il valore temporale (timestamp) nella colonna come data completa, includendo l\'ora (giorno/mese/anno e ore:minuti).</li>
    <ul><li>Mustache non disponibile</li></ul>
    <li><strong>Campo "Time" formattato come ora</strong>: Formatta il valore temporale (timestamp) della colonna per visualizzare solo l\'ora (ore:minuti), omettendo la data.</li>
    <ul><li>Mustache non disponibile</li></ul>
</ul>';
$string['table_renderer_date'] = 'Campo "Time" formattato come data';
$string['table_renderer_datetime'] = 'Campo "Time" formattato come data e ora';
$string['table_renderer_filesize'] = 'Converte in dimensione dei dati su disco';
$string['table_renderer_mustache'] = 'HTML della colonna
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Non visualizzare questa colonna';
$string['table_renderer_number'] = 'Numeri';
$string['table_renderer_seconds'] = 'Campo "Time" formattato come ora';
$string['table_renderer_status'] = 'Campo binario per Attivo/Inattivo';
$string['table_renderer_title'] = 'Formattazione della colonna';
$string['table_renderer_translate'] = 'Usa get_string("identifier", "component") per tradurre la colonna';
$string['table_renderer_userfullname'] = 'Converti la colonna nel nome completo dello studente con la funzione "fullname()"';
$string['table_renderer_userphoto'] = 'Converti ID dello studente in immagine del profilo';
$string['table_renderer_visible'] = 'Campo binario per Visibile/Invisibile';
