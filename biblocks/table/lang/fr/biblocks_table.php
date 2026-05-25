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

$string['pluginname'] = 'Table de données';
$string['pluginname_desc'] = 'Affiche un tableau avec pagination des données.';
$string['table_col_title'] = 'Titre de la colonne';
$string['table_column_not_configured'] = 'Colonnes non configurées dans ce tableau';
$string['table_edit_column'] = 'Colonne';
$string['table_first_records'] = 'Les dix premiers enregistrements de la requête';
$string['table_info_topo'] = 'Vous verrez d\'abord un aperçu des résultats de la requête. Ensuite, une série de colonnes sera présentée afin de nommer les titres et de définir le format des données de chaque colonne.';
$string['table_info_types'] = 'Vous pouvez maintenant définir un nom pour la colonne, puis préciser le format souhaité et indiquer si vous voulez une mise en forme supplémentaire avec Mustache.
<ul>
    <li><strong>Aucune mise en forme</strong>: Affiche le contenu exactement tel quel ou applique Mustache si vous l\'ajoutez.</li>
    <li><strong>Ne pas afficher cette colonne</strong>: Masque la colonne sélectionnée dans la vue, mais les données restent disponibles pour le traitement avec Mustache.</li>
    <ul><li>Mustache non disponible</li></ul>
    <li><strong>Nombres</strong>: Met en forme la colonne pour afficher uniquement des valeurs numériques, en appliquant les règles standard d\'affichage des nombres, comme les séparateurs de milliers et de décimales.</li>
    <ul><li>Mustache non disponible</li></ul>
    <li><strong>Convertir la colonne en nom complet "fullname()"</strong>: Exécute la fonction <code>fullname()</code> pour générer le nom complet selon la langue, qui sera stocké dans cette même colonne. Pour que cela fonctionne, la colonne <code>lastname</code> est obligatoire et doit être masquée si possible.</li>
    <li><strong>Convertir l\'ID de l\'étudiant en photo de profil</strong>: Utilise l\'ID de cette colonne pour créer la photo de profil.</li>
    <li><strong>Champ binaire pour Visible/Invisible</strong>: Utilise la valeur binaire pour déterminer la visibilité, où "0"/"false" signifie invisible et "1"/"true" signifie visible.</li>
    <li><strong>Champ binaire pour Actif/Inactif</strong>: Utilise la valeur binaire pour déterminer le statut, où "0"/"false" signifie Inactif et "1"/"true" signifie Actif.</li>
    <li><strong>Champ "Time" formaté comme date</strong>: Convertit la valeur temporelle (timestamp) de la colonne en date lisible, en affichant uniquement la date (jour/mois/année).</li>
    <ul><li>Mustache non disponible</li></ul>
    <li><strong>Champ "Time" formaté comme date et heure</strong>: Affiche la valeur temporelle (timestamp) de la colonne sous forme de date complète, avec l\'heure (jour/mois/année et heures:minutes).</li>
    <ul><li>Mustache non disponible</li></ul>
    <li><strong>Champ "Time" formaté comme heure</strong>: Met en forme la valeur temporelle (timestamp) de la colonne pour afficher uniquement l\'heure (heures:minutes), sans la date.</li>
    <ul><li>Mustache non disponible</li></ul>
</ul>';
$string['table_renderer_date'] = 'Champ "Time" formaté comme date';
$string['table_renderer_datetime'] = 'Champ "Time" formaté comme date et heure';
$string['table_renderer_filesize'] = 'Convertit en taille de données sur disque';
$string['table_renderer_mustache'] = 'HTML de la colonne
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Ne pas afficher cette colonne';
$string['table_renderer_number'] = 'Nombres';
$string['table_renderer_seconds'] = 'Champ "Time" formaté comme heure';
$string['table_renderer_status'] = 'Champ binaire pour Actif/Inactif';
$string['table_renderer_title'] = 'Mise en forme de la colonne';
$string['table_renderer_translate'] = 'Utiliser get_string("identifier", "component") pour traduire la colonne';
$string['table_renderer_userfullname'] = 'Convertir la colonne en nom complet de l’étudiant avec la fonction "fullname()"';
$string['table_renderer_userphoto'] = 'Convertir l\'ID de l\'étudiant en photo de profil';
$string['table_renderer_visible'] = 'Champ binaire pour Visible/Invisible';
