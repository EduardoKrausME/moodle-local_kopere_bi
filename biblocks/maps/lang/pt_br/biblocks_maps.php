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
 * @package   biblocks_maps
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['maps_1_city'] = '{a1} e mais uma cidade';
$string['maps_many_city'] = '{a1} e mais {a2} cidades';
$string['maps_online'] = '{a1} aluno online';
$string['maps_onlines'] = '{a1} alunos online';
$string['maps_sql_warning'] =
    '<p>Lembre-se de que o SQL abaixo deve retornar apenas uma coluna, e essa coluna deve conter um IP válido.<br>Exemplo: o SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> retorna todos os alunos que acessaram o Moodle nos últimos 10 minutos</p>';
$string['pluginname'] = 'Mapa de alunos online';
$string['pluginname_desc'] = 'Cria um mapa de alunos online com base em seus IPs';
