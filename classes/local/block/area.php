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

namespace local_kopere_bi\local\block;

use local_kopere_bi\local\block\util\cache_util;
use local_kopere_bi\local\block\util\code_util;
use local_kopere_bi\local\block\util\database_util;
use local_kopere_bi\local\block\util\reload_util;
use local_kopere_bi\local\util\sql_util;
use local_kopere_bi\local\util\string_util;
use local_kopere_dashboard\util\message;
use local_kopere_dashboard\util\url_util;

/**
 * Class area
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class area extends line {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string("area_name", "local_kopere_bi");
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function get_description() {
        return get_string("area_desc", "local_kopere_bi");
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
     * https://apexcharts.com/samples/vanilla-js/dashboards/dark/index.html
     *
     * @param $koperebielement
     *
     * @return string
     * @throws \Exception
     */
    public function preview($koperebielement) {
        global $OUTPUT;

        code_util::add_js_apexcharts();

        return $OUTPUT->render_from_template("local_kopere_bi/block_area_preview", [
            "ajax_url" => url_util::makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "local_kopere_bi_id" => $koperebielement->id,
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
     *
     * @throws \Exception
     */
    public function get_chart_data($koperebielement) {

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if (false && $cache->has($koperebielement->id)) {
            $lines = $cache->get($koperebielement->id);
        } else {

            $comand = sql_util::prepare_sql($koperebielement->commandsql);
            try {
                $rowscolumns = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
            } catch (\Exception $e) {
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

    /**
     * https://developers.google.com/chart/interactive/docs/gallery/linechart?hl=pt_br
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
            $rowsarea = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
        } catch (\Exception $e) {
            if (AJAX_SCRIPT) {
                echo json_encode([
                    "error" => $e->getMessage(),
                    "trace" => $e->getTraceAsString(),
                ]);
                die;
            } else {
                message::print_danger($e->getMessage());
                return "";
            }
        }

        $arrayareas = false;
        foreach ($rowsarea as $key => $values) {

            if (!$arrayareas) {
                $names = [];
                foreach ($values as $column => $value) {
                    $names[] = $column;
                }
                $arrayareas = json_encode($names) . ",\n";
            }

            $values = [];
            foreach ($values as $value) {
                if (count($values) == 0) {
                    $values[] = $value;
                } else {
                    $values[] = intval($value);
                }
            }
            $arrayareas .= json_encode($values) . ",\n";
        }

        return $OUTPUT->render_from_template("local_kopere_bi/block_area_preview_google", [
            "koperebiitem_id" => $koperebielement->id,
            "arrayareas" => $arrayareas,
        ]);
    }
}
