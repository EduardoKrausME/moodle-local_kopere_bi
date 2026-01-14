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

namespace biblocks_area;

use Exception;
use local_kopere_bi\block\util\cache_util;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\reload_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\block\util\string_util;
use local_kopere_dashboard\util\message;
use local_kopere_dashboard\util\url_util;

/**
 * Class area
 *
 * @package   biblocks_area
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider extends \biblocks_line\provider {

    /**
     * Function get_name
     *
     * @return string
     * @throws Exception
     */
    public static function get_name() {
        return get_string("pluginname", "biblocks_area");
    }

    /**
     * Function get_description
     *
     * @return string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("pluginname_desc", "biblocks_area");
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
     * https://apexcharts.com/samples/vanilla-js/dashboards/dark/index.html
     *
     * @param $koperebielement
     * @return string
     * @throws Exception
     */
    public function preview($koperebielement) {
        global $OUTPUT;

        code_util::add_js_apexcharts();

        return $OUTPUT->render_from_template("biblocks_area/preview", [
            "ajax_url" => url_util::makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "element_id" => $koperebielement->id,
            "chart_default" => get_config("local_kopere_bi", "chart_area_default"),
            "chart_options" => code_util::get_js_options($koperebielement->info_obj["chart_options"]),
            "code_util_get_js_theme" => code_util::get_js_theme($koperebielement),
            "error_chart_renderer" => get_string("error_chart_renderer", "local_kopere_bi"),
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

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if (false && $cache->has($koperebielement->id)) {
            $lines = $cache->get($koperebielement->id);
        } else {

            $comand = sql_util::prepare_sql($koperebielement->commandsql);
            try {
                $rowscolumns = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
            } catch (Exception $e) {
                if (AJAX_SCRIPT) {
                    echo json_encode([
                        "sql" => $comand->sql,
                        "error" => $e->getMessage(),
                        "trace" => $e->getTraceAsString(),
                    ]);
                    die;
                } else {
                    message::print_danger($e->getMessage());
                }
            }

            $columns = array_keys((array)$rowscolumns[0]);

            $optionsxaxiscategories = false;
            $optionsseries = [];
            foreach ($columns as $column) {
                if ($optionsxaxiscategories === false) {

                    // Here take the first column to be the X.

                    $optionsxaxiscategories = array_column($rowscolumns, $column);
                } else {

                    // Other columns are series.
                    // Column name is the name of the series.

                    $valores = array_column($rowscolumns, $column);
                    $optionsseries[] = (object)[
                        "name" => string_util::get_string($column),
                        "data" => $valores,
                    ];
                }
            }

            $lines = [
                "xaxis_categories" => $optionsxaxiscategories,
                "series" => $optionsseries,
            ];
            $cache->set($koperebielement->id, $lines);
        }
        ob_clean();
        header('Content-Type: application/json; charset: utf-8');
        echo json_encode($lines, JSON_NUMERIC_CHECK + JSON_PRETTY_PRINT);
        die();
    }
}
