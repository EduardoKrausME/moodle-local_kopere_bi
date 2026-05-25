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

$string['html_block'] = 'Bloc HTML avec prise en charge de Mustache';
$string['html_block_desc'] = '<p>Le HTML ajouté à ce champ doit respecter le format <strong>Mustache</strong>, ce qui permet la substitution dynamique des données dans vos pages. Utilisez des doubles accolades <code>{{ }}</code> pour référencer directement les valeurs des colonnes SQL dans le HTML, afin de garantir que les données sont correctement insérées.</p>
<blockquote>
    <p>Par exemple, avec la requête SQL suivante :</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Vous pouvez référencer les valeurs retournées dans votre HTML avec la syntaxe suivante :</p>
    <pre>&lt;p&gt;E-mail : {{{email}}}&lt;/p&gt;
    &lt;p&gt;Nom complet : {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>Dans cet exemple, je souhaite afficher plusieurs lignes d\'un résultat SQL, qui retourne une liste d\'utilisateurs enregistrés avec une authentification manuelle. Le SQL utilisé est :</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Pour parcourir les résultats et afficher les données sous forme de tableau, j\'utilise <code>{{#lines}}</code> dans le template afin de répéter le contenu pour chaque enregistrement retourné. Le template ressemblerait à ceci :</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;E-mail&lt;/th&gt;
        &lt;th&gt;Nom complet&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Les triples accolades <code>{{{ }}}</code> permettent d\'insérer la valeur sans échappement HTML, ce qui est utile pour afficher du contenu pouvant contenir des balises HTML.</p>
<p>Pour plus d\'informations sur l\'utilisation des templates Mustache dans Moodle, consultez la documentation officielle : <a href="https://moodledev.io/docs/guides/templates" target="_blank">Guide des Templates Moodle</a>.</p>';
$string['pluginname'] = 'Bloc HTML';
$string['pluginname_desc'] = 'Affiche un Bloc HTML avec des données provenant de la base de données';
