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

namespace biblocks_html;

use Exception;
use local_kopere_bi\block\i_block_provider;
use local_kopere_bi\block\util\cache_util;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\reload_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\output\renderer_bi_mustache;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_textarea;
use local_kopere_dashboard\util\message;
use local_kopere_dashboard\util\url_util;

/**
 * Class html
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
        return get_string("pluginname", "biblocks_html");
    }

    /**
     * Function get_description
     *
     * @return string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("pluginname_desc", "biblocks_html");
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
     * @throws Exception
     */
    public function edit(form $form, $koperebielement) {
        global $PAGE;

        message::print_info(get_string("html_block_desc", "biblocks_html"));

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("html_block", "biblocks_html"))
                ->set_style("width:100%;font-family:monospace;white-space:nowrap;")
                ->set_name("infohtml")
                ->set_value(@$koperebielement->info_obj["html"]));
        $PAGE->requires->js_call_amd("local_kopere_bi/load_ace", "getScript", ["infohtml", "html"]);

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
        global $OUTPUT;

        return $OUTPUT->render_from_template("biblocks_html/preview", [
            "ajax_url" => url_util::makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "element_id" => $koperebielement->id,
            "error_data_loader" => get_string("error_data_loader", "local_kopere_bi"),
            "reload_time" => reload_util::convert($koperebielement->reload),
        ]);
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     * @throws Exception
     */
    public function get_chart_data($koperebielement) {

        ob_clean();
        header('Content-Type: application/json; charset: utf-8');

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if (false && $cache->has($koperebielement->id)) {
            $returnhtml = $cache->get($koperebielement->id);
        } else {
            $comand = sql_util::prepare_sql($koperebielement->commandsql);

            $mustache = new renderer_bi_mustache();
            $html = $koperebielement->info_obj["html"];
            if (strpos($html, "{{#lines}}") === false) {
                try {
                    $line = (new database_util())->get_record_sql_block($comand->sql, $comand->params);
                } catch (Exception $e) {
                    echo json_encode([
                        "sql" => $comand->sql,
                        "html" => message::danger($e->getMessage()),
                        "trace" => $e->getTrace(),
                    ]);
                    die();
                }
                $returnhtml = $mustache->render_from_string($html, $line);
            } else {
                try {
                    $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
                } catch (Exception $e) {
                    echo json_encode([
                        "sql" => $comand->sql,
                        "html" => message::danger($e->getMessage()),
                        "trace" => $e->getTrace(),
                    ]);
                    die();
                }
                $returnhtml = $mustache->render_from_string($html, ["lines" => json_decode(json_encode($lines))]);
            }

            $cache->set($koperebielement->id, $returnhtml);
        }

        echo json_encode(["html" => $returnhtml]);
        die();
    }
}
