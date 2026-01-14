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

namespace biblocks_info;

use Exception;
use local_kopere_bi\block\i_block_provider;
use local_kopere_bi\block\util\cache_util;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\util\message;

/**
 * Class info
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements i_block_provider {

    /**
     * Function get_name
     *
     * @return string
     * @throws Exception
     */
    public static function get_name() {
        return get_string("pluginname", "biblocks_info");
    }

    /**
     * Function get_description
     *
     * @return string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("pluginname_desc", "biblocks_info");
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
     * @return string
     */
    public function title_extra($koperebielement) {
        return "";
    }

    /**
     * Function edit
     *
     * @param form $form
     * @param $koperebielement
     * @return void
     * @throws Exception
     */
    public function edit(form $form, $koperebielement) {

        message::print_warning(get_string("info_sql_warning", "local_kopere_bi"));

        code_util::input_commandsql($form, $koperebielement);
    }

    /**
     * Function is_edit_columns
     *
     * @return bool
     */
    public function is_edit_columns() {
        return false;
    }

    /**
     * Function edit_columns
     *
     * @param form $form
     * @param $koperebielement
     * @return void
     */
    public function edit_columns(form $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     * @return string
     * @throws Exception
     */
    public function preview($koperebielement) {

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if (false && $cache->has($koperebielement->id)) {
            $retorno = $cache->get($koperebielement->id);
        } else {

            $comand = sql_util::prepare_sql($koperebielement->commandsql);

            try {
                $line = (new database_util())->get_record_sql_block($comand->sql, $comand->params);
            } catch (Exception $e) {
                if (AJAX_SCRIPT) {
                    echo json_encode([
                        "sql" => $comand->sql,
                        "error" => $e->getMessage(),
                        "trace" => $e->getTraceAsString(),
                    ]);
                    die;
                } else {
                    message::print_danger(get_string("info_error_sql", "local_kopere_bi"));
                    return "";
                }
            }

            foreach ($line as $column) {
                if (is_float($column)) {
                    $column = "{$column}";
                } else if (is_number($column)) {
                    $column = number_format($column, 0, "", ".");
                }
                $retorno = "<span class='big-text'>{$column}</span>";
                $cache->set($koperebielement->id, $retorno);
            }
        }

        return $retorno;
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     * @return void
     */
    public function get_chart_data($koperebielement) {
    }
}
