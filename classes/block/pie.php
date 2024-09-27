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
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\reload_util;
use local_kopere_bi\util\sql_util;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\util\mensagem;

/**
 * Class pie
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pie implements i_type {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('pie_name', 'local_kopere_bi');
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function get_description() {
        return get_string('pie_desc', 'local_kopere_bi');
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

        mensagem::print_warning(get_string('pie_sql_warning', 'local_kopere_bi'));

        code_util::load_ace_commandsql($form, $koperebielement);

        if (isset($koperebielement->info_obj['chart_options'])) {
            code_util::options($form, $koperebielement->info_obj['chart_options']);
        } else {
            code_util::options($form, trim("
{
    colors : [\"#2E93fA\", \"#66DA26\", \"#546E7A\", \"#E91E63\", \"#FF9800\"],
}"));
        }
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
     * @return mixed
     * @throws \Exception
     */
    public function preview($koperebielement) {
        global $OUTPUT;

        code_util::add_js_apexcharts();

        return $OUTPUT->render_from_template("local_kopere_bi/block_pie_preview", [
            "ajax_url" => local_kopere_dashboard_makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "local_kopere_bi_id" => $koperebielement->id,
            "chart_pie_default" => get_config("local_kopere_bi", "chart_pie_default"),
            "chart_options" => code_util::get_js_options($koperebielement->info_obj['chart_options']),
            "code_util_get_js_theme" => code_util::get_js_theme($koperebielement),
            "error_chart_renderer" => get_string('error_chart_renderer', 'local_kopere_bi'),
            "error_data_loader" => get_string('error_data_loader', 'local_kopere_bi'),
            "reload_time" => reload_util::convert($koperebielement->reload),
        ]);
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function get_chart_data($koperebielement) {
        $cache = cache_util::get_cache_make($koperebielement->cache);

        if ($cache->has($koperebielement->id)) {
            $lines = $cache->get($koperebielement->id);
        } else {
            $comand = sql_util::prepare_sql($koperebielement->commandsql);
            try {
                $dados = (new database_util())->get_records_sql_block_array($comand->sql, $comand->params);
            } catch (\Exception $e) {
                mensagem::print_danger($e->getMessage());
                return;
            }

            $keys = [];
            foreach ($dados[0] as $key => $value) {
                if (!isset($keys[0])) {
                    $keys[0] = $key;
                } else if (!isset($keys[1])) {
                    $keys[1] = $key;
                } else {
                    break;
                }
            }

            $optionslabels = [];
            $optionsserie = [];
            foreach ($dados as $dado) {
                $optionslabels[] = $dado[$keys[0]];
                $optionsserie[] = $dado[$keys[1]];
            }

            $lines = [
                "labels" => $optionslabels,
                "series" => $optionsserie,
            ];
            $cache->set($koperebielement->id, $lines);
        }

        ob_clean();
        header('Content-Type: application/json; charset: utf-8');
        echo json_encode($lines, JSON_NUMERIC_CHECK);
        die();
    }

    /**
     * https://developers.google.com/chart/interactive/docs/gallery/piechart?hl=pt_br
     *
     * @param $koperebielement
     *
     * @return string
     * @throws \Exception
     */
    public function preview_google($koperebielement) {
        global $OUTPUT;

        $comand = sql_util::prepare_sql($koperebielement->commandsql);
        try {
            $dadospie = (new database_util())->get_record_sql_block($comand->sql, $comand->params);
        } catch (\Exception $e) {
            mensagem::print_danger($e->getMessage());
            return "";
        }

        $arraypie = "";
        foreach ($dadospie as $key => $dadopie) {
            $dadopie = intval($dadopie);
            $arraypie .= "['{$key}', {$dadopie}],\n";
        }

        return $OUTPUT->render_from_template("local_kopere_bi/block_pie_preview_google", [
            "koperebiitem_id" => $koperebielement->id,
            "arraypie" => $arraypie,
        ]);
    }
}
