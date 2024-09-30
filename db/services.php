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
 * service file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    "local_kopere_bi_cat_sortorder" => [
        "classpath" => "local/kopere_bi/classes/external/categorie.php",
        "classname" => "\\local_kopere_bi\\external\\categorie",
        "methodname" => "sortorder",
        "description" => "Salva o sortorder das categorias",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kopere_bi_block_sequence" => [
        "classpath" => "local/kopere_bi/classes/external/block.php",
        "classname" => "\\local_kopere_bi\\external\\block",
        "methodname" => "sequence",
        "description" => "Salva a sequence dos blocks na página",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kopere_bi_block_delete" => [
        "classpath" => "local/kopere_bi/classes/external/block.php",
        "classname" => "\\local_kopere_bi\\external\\block",
        "methodname" => "delete",
        "description" => "Exclui um block da página",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kopere_bi_block_add" => [
        "classpath" => "local/kopere_bi/classes/external/block.php",
        "classname" => "\\local_kopere_bi\\external\\block",
        "methodname" => "add",
        "description" => "Adicionar novo block na página",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    'local_kopere_bi_online_update' => [
        "classpath" => "local/kopere_bi/classes/external/online_update.php",
        'classname' => '\local_kopere_bi\external\online_update',
        'methodname' => 'api',
        'description' => 'Contabiliza o tempo gasto de um usuário para o dashboard',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_kopere_bi_page_html' => [
        "classpath" => "local/kopere_bi/classes/external/page_html.php",
        'classname' => '\local_kopere_bi\external\page_html',
        'methodname' => 'api',
        'description' => 'Contabiliza o tempo gasto de um usuário para o dashboard',
        'type' => 'write',
        'ajax' => true,
    ],
];
