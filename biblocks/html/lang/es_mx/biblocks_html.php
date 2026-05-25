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

$string['html_block'] = 'Bloque HTML con soporte para Mustache';
$string['html_block_desc'] = '<p>El HTML agregado a este campo debe seguir el formato <strong>Mustache</strong>, lo que permite la sustitución dinámica de datos en tus páginas. Usa llaves dobles <code>{{ }}</code> para hacer referencia directamente a los valores de las columnas SQL dentro del HTML, asegurando que los datos se inserten correctamente.</p>
<blockquote>
    <p>Por ejemplo, usando la siguiente consulta SQL:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>Puedes hacer referencia a los valores devueltos en tu HTML usando la siguiente sintaxis:</p>
    <pre>&lt;p&gt;Correo electrónico: {{{email}}}&lt;/p&gt;
    &lt;p&gt;Nombre completo: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>En este ejemplo, quiero mostrar varias filas de un resultado SQL, que devuelve una lista de usuarios registrados con autenticación manual. El SQL usado para esto es:</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>Para recorrer los resultados y mostrar los datos en formato de tabla, uso <code>{{#lines}}</code> en la plantilla para repetir el contenido por cada registro devuelto. La plantilla quedaría así:</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;Correo electrónico&lt;/th&gt;
        &lt;th&gt;Nombre completo&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>Las llaves triples <code>{{{ }}}</code> permiten insertar el valor sin escapar HTML, lo cual es útil para mostrar contenido que puede contener etiquetas HTML.</p>
<p>Para obtener más información sobre el uso de plantillas Mustache en Moodle, consulta la documentación oficial: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Guía de Plantillas de Moodle</a>.</p>';
$string['pluginname'] = 'Bloque HTML';
$string['pluginname_desc'] = 'Muestra un Bloque HTML con datos provenientes de la base de datos';
