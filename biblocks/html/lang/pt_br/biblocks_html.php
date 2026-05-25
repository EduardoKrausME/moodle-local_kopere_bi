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

$string['html_block'] = 'Bloco HTML com suporte a Mustache';
$string['html_block_desc'] = '<p>O HTML adicionado a este campo deve seguir o formato <strong>Mustache</strong>, permitindo a substituição dinâmica de dados nas suas páginas. Use chaves duplas <code>{{ }}</code> para referenciar diretamente os valores das colunas SQL no HTML, garantindo que os dados sejam inseridos corretamente.</p>
<blockquote>
    <p>Por exemplo, usando a seguinte consulta SQL:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Você pode referenciar os valores retornados no seu HTML usando a seguinte sintaxe:</p>
    <pre>&lt;p&gt;E-mail: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Nome completo: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>Neste exemplo, quero exibir várias linhas de um resultado SQL, que retorna uma lista de usuários cadastrados com autenticação manual. O SQL usado para isso é:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Para percorrer os resultados e exibir os dados em formato de tabela, uso <code>{{#lines}}</code> no template para repetir o conteúdo para cada registro retornado. O template ficaria assim:</p>
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
<p>Chaves triplas <code>{{{ }}}</code> permitem que o valor seja inserido sem escape de HTML, o que é útil para exibir conteúdos que podem conter tags HTML.</p>
<p>Para saber mais sobre o uso de templates Mustache no Moodle, consulte a documentação oficial: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Guia de Templates do Moodle</a>.</p>';
$string['pluginname'] = 'Bloco HTML';
$string['pluginname_desc'] = 'Exibe um Bloco HTML com dados vindos do banco de dados';
