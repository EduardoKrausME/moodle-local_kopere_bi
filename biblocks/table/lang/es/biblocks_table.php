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

$string['pluginname'] = 'Tabla de datos';
$string['pluginname_desc'] = 'Muestra una tabla con paginación de datos.';
$string['table_col_title'] = 'Título de la columna';
$string['table_column_not_configured'] = 'Columnas no configuradas en esta tabla';
$string['table_edit_column'] = 'Columna';
$string['table_first_records'] = 'Los diez primeros registros de la consulta';
$string['table_info_topo'] = 'Primero, verás una vista previa de los resultados de la consulta. Después, se presentará una serie de columnas para que puedas nombrar los títulos y definir el formato de los datos de cada columna.';
$string['table_info_types'] = 'Ahora puedes definir un nombre para la columna y luego especificar el formato deseado, además de indicar si deseas algún formato adicional con Mustache.
<ul>
    <li><strong>Sin formato</strong>: Muestra el contenido exactamente como está o aplica Mustache si lo añades.</li>
    <li><strong>No mostrar esta columna</strong>: Oculta la columna seleccionada en la vista, pero los datos siguen disponibles para el procesamiento con Mustache.</li>
    <ul><li>Mustache no disponible</li></ul>
    <li><strong>Números</strong>: Da formato a la columna para mostrar solo valores numéricos, aplicando las reglas estándar de visualización de números, como separadores de miles y decimales.</li>
    <ul><li>Mustache no disponible</li></ul>
    <li><strong>Convertir columna a nombre completo "fullname()"</strong>: Ejecuta la función <code>fullname()</code> para generar el nombre completo según el idioma, que se almacenará en esta misma columna. Para que esto funcione, la columna <code>lastname</code> es obligatoria y debe ocultarse si es posible.</li>
    <li><strong>Convertir ID del estudiante en imagen de perfil</strong>: Usa el ID de esta columna para crear la imagen de perfil.</li>
    <li><strong>Campo binario para Visible/Invisible</strong>: Usa el valor binario para determinar la visibilidad, donde "0"/"false" significa invisible y "1"/"true" significa visible.</li>
    <li><strong>Campo binario para Activo/Inactivo</strong>: Usa el valor binario para determinar el estado, donde "0"/"false" significa Inactivo y "1"/"true" significa Activo.</li>
    <li><strong>Campo "Time" formateado como fecha</strong>: Convierte el valor de tiempo (timestamp) de la columna en una fecha legible, mostrando solo la fecha (día/mes/año).</li>
    <ul><li>Mustache no disponible</li></ul>
    <li><strong>Campo "Time" formateado como fecha y hora</strong>: Muestra el valor de tiempo (timestamp) de la columna como una fecha completa, incluida la hora (día/mes/año y horas:minutos).</li>
    <ul><li>Mustache no disponible</li></ul>
    <li><strong>Campo "Time" formateado como hora</strong>: Da formato al valor de tiempo (timestamp) de la columna para mostrar solo la hora (horas:minutos), omitiendo la fecha.</li>
    <ul><li>Mustache no disponible</li></ul>
</ul>';
$string['table_renderer_date'] = 'Campo "Time" formateado como fecha';
$string['table_renderer_datetime'] = 'Campo "Time" formateado como fecha y hora';
$string['table_renderer_filesize'] = 'Convierte al tamaño de datos en disco';
$string['table_renderer_mustache'] = 'HTML de la columna
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'No mostrar esta columna';
$string['table_renderer_number'] = 'Números';
$string['table_renderer_seconds'] = 'Campo "Time" formateado como hora';
$string['table_renderer_status'] = 'Campo binario para Activo/Inactivo';
$string['table_renderer_title'] = 'Formato de la columna';
$string['table_renderer_translate'] = 'Usar get_string("identifier", "component") para traducir la columna';
$string['table_renderer_userfullname'] = 'Convertir la columna al nombre completo del estudiante con la función "fullname()"';
$string['table_renderer_userphoto'] = 'Convertir ID del estudiante en imagen de perfil';
$string['table_renderer_visible'] = 'Campo binario para Visible/Invisible';
