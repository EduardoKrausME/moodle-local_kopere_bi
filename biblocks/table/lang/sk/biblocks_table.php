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

$string['pluginname'] = 'Dátová tabuľka';
$string['pluginname_desc'] = 'Zobrazuje tabuľku s stránkovaním údajov.';
$string['table_col_title'] = 'Názov stĺpca';
$string['table_column_not_configured'] = 'Stĺpce v tejto tabuľke nie sú nakonfigurované';
$string['table_edit_column'] = 'Stĺpec';
$string['table_first_records'] = 'Prvých desať záznamov dotazu';
$string['table_info_topo'] = 'Najprv uvidíte ukážku výsledkov dotazu. Potom sa zobrazí séria stĺpcov, aby ste mohli pomenovať nadpisy a definovať formát údajov v každom stĺpci.';
$string['table_info_types'] = 'Teraz môžete nastaviť názov stĺpca a potom určiť požadovaný formát a tiež to, či chcete použiť ďalšie formátovanie pomocou Mustache.
<ul>
    <li><strong>Bez formátovania</strong>: Zobrazí obsah presne tak, ako je, alebo použije Mustache, ak ho pridáte.</li>
    <li><strong>Nezobrazovať tento stĺpec</strong>: Skryje vybraný stĺpec v zobrazení, ale údaje zostanú dostupné na spracovanie pomocou Mustache.</li>
    <ul><li>Mustache nie je dostupný</li></ul>
    <li><strong>Čísla</strong>: Naformátuje stĺpec tak, aby zobrazoval iba číselné hodnoty, pričom použije štandardné pravidlá zobrazovania čísel, napríklad oddeľovače tisícov a desatinných miest.</li>
    <ul><li>Mustache nie je dostupný</li></ul>
    <li><strong>Previesť stĺpec na celé meno "fullname()"</strong>: Spustí funkciu <code>fullname()</code> na vytvorenie celého mena podľa jazyka, ktoré sa uloží do toho istého stĺpca. Aby to fungovalo, je potrebný stĺpec <code>lastname</code> a ak je to možné, mal by byť skrytý.</li>
    <li><strong>Previesť ID študenta na profilovú fotografiu</strong>: Použije ID z tohto stĺpca na vytvorenie profilovej fotografie.</li>
    <li><strong>Binárne pole pre Viditeľné/Neviditeľné</strong>: Používa binárnu hodnotu na určenie viditeľnosti, kde "0"/"false" znamená neviditeľné a "1"/"true" znamená viditeľné.</li>
    <li><strong>Binárne pole pre Aktívne/Neaktívne</strong>: Používa binárnu hodnotu na určenie stavu, kde "0"/"false" znamená Neaktívne a "1"/"true" znamená Aktívne.</li>
    <li><strong>Pole "Time" formátované ako dátum</strong>: Prevedie časovú hodnotu (timestamp) v stĺpci na čitateľný dátum a zobrazí iba dátum (deň/mesiac/rok).</li>
    <ul><li>Mustache nie je dostupný</li></ul>
    <li><strong>Pole "Time" formátované ako dátum a čas</strong>: Zobrazí časovú hodnotu (timestamp) v stĺpci ako úplný dátum vrátane času (deň/mesiac/rok a hodiny:minúty).</li>
    <ul><li>Mustache nie je dostupný</li></ul>
    <li><strong>Pole "Time" formátované ako čas</strong>: Naformátuje časovú hodnotu (timestamp) v stĺpci tak, aby sa zobrazil iba čas (hodiny:minúty), bez dátumu.</li>
    <ul><li>Mustache nie je dostupný</li></ul>
</ul>';
$string['table_renderer_date'] = 'Pole "Time" formátované ako dátum';
$string['table_renderer_datetime'] = 'Pole "Time" formátované ako dátum a čas';
$string['table_renderer_filesize'] = 'Prevedie na veľkosť údajov na disku';
$string['table_renderer_mustache'] = 'HTML stĺpca
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Nezobrazovať tento stĺpec';
$string['table_renderer_number'] = 'Čísla';
$string['table_renderer_seconds'] = 'Pole "Time" formátované ako čas';
$string['table_renderer_status'] = 'Binárne pole pre Aktívne/Neaktívne';
$string['table_renderer_title'] = 'Formátovanie stĺpca';
$string['table_renderer_translate'] = 'Použiť get_string("identifier", "component") na preklad stĺpca';
$string['table_renderer_userfullname'] = 'Previesť stĺpec na celé meno študenta pomocou funkcie "fullname()"';
$string['table_renderer_userphoto'] = 'Previesť ID študenta na profilovú fotografiu';
$string['table_renderer_visible'] = 'Binárne pole pre Viditeľné/Neviditeľné';
