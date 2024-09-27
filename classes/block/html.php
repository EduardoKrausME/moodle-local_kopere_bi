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
use local_kopere_dashboard\html\inputs\input_textarea;
use local_kopere_dashboard\util\mensagem;

/**
 * Class html
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class html implements i_type {

    /**
     * Function get_name
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('html_name', 'local_kopere_bi');
    }

    /**
     * Function get_description
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_description() {
        return get_string('html_desc', 'local_kopere_bi');
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
     *
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
     *
     * @throws \Exception
     */
    public function edit(form $form, $koperebielement) {
        mensagem::print_warning(get_string('html_sql_warning', 'local_kopere_bi'));

        code_util::load_ace_commandsql($form, $koperebielement);

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string('html_block', 'local_kopere_bi'))
                ->set_style("width:100%;font-family:monospace;white-space:nowrap;")
                ->set_name("infohtml")
                ->set_value(@$koperebielement->info_obj['html'])
                ->set_description(get_string('html_block_desc', 'local_kopere_bi')));
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
     */
    public function edit_columns(form $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function preview($koperebielement) {
        global $OUTPUT;

        return $OUTPUT->render_from_template("local_kopere_bi/block_html_preview", [
            "ajax_url" => local_kopere_dashboard_makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "local_kopere_bi_id" => $koperebielement->id,
            "error_data_loader" => get_string('error_data_loader', 'local_kopere_bi'),
            "reload_time" => reload_util::convert($koperebielement->reload),
        ]);
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     *
     * @throws \Exception
     */
    public function get_chart_data($koperebielement) {

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if ($cache->has($koperebielement->id)) {
            $returnhtml = $cache->get($koperebielement->id);
        } else {
            $comand = sql_util::prepare_sql($koperebielement->commandsql);
            try {
                $line = (new database_util())->get_record_sql_block($comand->sql, $comand->params);
            } catch (\Exception $e) {
                mensagem::print_danger($e->getMessage());
                return;
            }

            $returnhtml = $koperebielement->info_obj['html'];
            foreach ($line as $key => $value) {
                echo "replace \"{{$key}}\" => \"$value\"\n";

                $returnhtml = str_replace("{{$key}}", $value, $returnhtml);
            }

            $cache->set($koperebielement->id, $returnhtml);
        }

        ob_clean();
        header('Content-Type: application/json; charset: utf-8');
        echo json_encode(["html" => $returnhtml]);
        die();
    }

    /**
     * https://developers.google.com/chart/interactive/docs/gallery/table?hl=pt_br
     *
     * @param $koperebielement
     *
     * @return string
     * @throws \Exception
     */
    public function preview_google($koperebielement) {
        global $OUTPUT;

        $addcollumn = [];
        $formatter = [];

        $comand = sql_util::prepare_sql($koperebielement->commandsql);
        try {
            $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
        } catch (\Exception $e) {
            mensagem::print_danger($e->getMessage());
            return "";
        }

        $columns = array_keys((array)$lines[0]);

        foreach ($columns as $column) {
            $addcollumn[] = "data.addColumn('string', '{$column}');";
        }

        $linechart = [];
        foreach ($lines as $line) {
            $linereturn = [];
            foreach ($columns as $column) {

                $valor = $line->{$column};
                $linereturn[] = $valor;
            }
            $linechart[] = $linereturn;
        }

        return $OUTPUT->render_from_template("local_kopere_bi/block_html_preview_google", [
                "koperebiitem_id" => $koperebielement->id,
                "collumn" => implode("\n\t\t\t\t", $addcollumn),
                "linechart" => json_encode($linechart, JSON_PRETTY_PRINT),
                "formatter" => implode("\n\t\t\t\t", $formatter),
            ]);
    }
}
