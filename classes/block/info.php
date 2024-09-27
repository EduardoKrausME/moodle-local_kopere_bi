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

namespace local_kopere_bi\block;

use local_kopere_bi\block\util\cache_util;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\util\sql_util;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\util\mensagem;

/**
 * Class info
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info implements i_type {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('info_name', 'local_kopere_bi');
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function get_description() {
        return get_string('info_desc', 'local_kopere_bi');
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
     *
     * @return mixed|string
     */
    public function title_extra($koperebielement) {
        return "";
    }

    /**
     * Function edit
     *
     * @param form $form
     * @param $koperebielement
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function edit(form $form, $koperebielement) {

        mensagem::print_warning(get_string('info_sql_warning', 'local_kopere_bi'));

        code_util::load_ace_commandsql($form, $koperebielement);
    }

    /**
     * Function is_edit_columns
     *
     * @return bool|mixed
     */
    public function is_edit_columns() {
        return false;
    }

    /**
     * Function edit_columns
     *
     * @param form $form
     * @param $koperebielement
     *
     * @return mixed|void
     */
    public function edit_columns(form $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function preview($koperebielement) {
        global $DB;

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if ($cache->has($koperebielement->id)) {
            $retorno = $cache->get($koperebielement->id);
        } else {

            $comand = sql_util::prepare_sql($koperebielement->commandsql);

            try {
                $line = $DB->get_record_sql($comand->sql, $comand->params, IGNORE_MULTIPLE);
            } catch (\dml_exception $e) {
                mensagem::print_danger(get_string('info_error_sql', 'local_kopere_bi'));
                return "";
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
     *
     * @return mixed|void
     */
    public function get_chart_data($koperebielement) {

    }
}
