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

use Exception;
use local_kopere_bi\block\util\cache_util;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\reload_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\block\util\string_util;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_select;
use local_kopere_dashboard\util\message;
use local_kopere_dashboard\util\url_util;

/**
 * Class line
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class line implements i_type {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws Exception
     */
    public static function get_name() {
        return get_string("line_name", "local_kopere_bi");
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("line_desc", "local_kopere_bi");
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
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
     * @return mixed|void
     * @throws Exception
     */
    public function edit(form $form, $koperebielement) {

        $values = [
            [
                "key" => "line",
                "value" => self::get_name(),
            ], [
                "key" => "area",
                "value" => area::get_name(),
            ], [
                "key" => "column",
                "value" => column::get_name(),
            ],
        ];
        $names = [
            "line" => self::get_name(),
            "area" => area::get_name(),
            "column" => column::get_name(),
        ];
        $form->add_input(
            input_select::new_instance()
                ->set_title(get_string("select_report_type", "local_kopere_bi"))
                ->set_name("element_type")
                ->set_value($koperebielement->type)
                ->set_values($values)
                ->set_description(get_string("select_report_type_desc", "local_kopere_bi", $names)));

        message::print_warning(get_string("line_sql_warning", "local_kopere_bi"));

        code_util::input_commandsql($form, $koperebielement);

        if (isset($koperebielement->info_obj["chart_options"])) {
            code_util::options($form, $koperebielement->info_obj["chart_options"]);
        } else {
            code_util::options($form, trim("
{
    stroke : {
        colors : [\"#2E93fA\", \"#66DA26\", \"#546E7A\", \"#E91E63\", \"#FF9800\"]
    },
    yaxis : {
        labels : {
            formatter: (value) => { return value },
        },
    },
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
     * @return mixed|void
     */
    public function edit_columns(form $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     * @return mixed
     * @throws Exception
     */
    public function preview($koperebielement) {
        global $OUTPUT;

        code_util::add_js_apexcharts();

        $data = [
            "ajax_url" => url_util::makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "local_kopere_bi_id" => $koperebielement->id,
            "chart_line_default" => get_config("local_kopere_bi", "chart_line_default"),
            "chart_options" => code_util::get_js_options($koperebielement->info_obj["chart_options"]),
            "code_util_get_js_theme" => code_util::get_js_theme($koperebielement),
            "error_chart_renderer" => get_string("error_chart_renderer", "local_kopere_bi"),
            "error_data_loader" => get_string("error_data_loader", "local_kopere_bi"),
            "reload_time" => reload_util::convert($koperebielement->reload),
        ];
        return $OUTPUT->render_from_template("local_kopere_bi/block_line_preview", $data);
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     * @return mixed|void
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
                    return;
                }
            }

            $columns = array_keys((array)$rowscolumns[0]);

            $optionslabels = false;
            $optionsseries = [];
            foreach ($columns as $column) {
                if ($optionslabels === false) {

                    // Here, take the first column to be the X.
                    // And the name of the column is the value of the Y Axis.

                    $optionslabels = array_column($rowscolumns, $column);
                } else {

                    // Other columns are series.

                    $values = array_column($rowscolumns, $column);
                    $values = array_map(function ($value) {
                        return $value == null ? 0 : intval($value);
                    }, $values);
                    $optionsseries[] = (object)[
                        "name" => string_util::get_string($column),
                        "data" => $values,
                    ];

                }
            }

            $lines = [
                "labels" => $optionslabels,
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
     * @return string
     * @throws Exception
     */
    public function preview_google($koperebielement) {
        global $OUTPUT;

        $comand = sql_util::prepare_sql($koperebielement->commandsql);
        try {
            $rowsline = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
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
                return "";
            }
        }

        $arraylines = false;
        foreach ($rowsline as $key => $values) {

            if (!$arraylines) {
                $names = [];
                foreach ($values as $column => $value) {
                    $names[] = $column;
                }
                $arraylines = json_encode($names) . ",\n";
            }

            $values = [];
            foreach ($values as $value) {
                if (count($values) == 0) {
                    $values[] = $value;
                } else {
                    $values[] = intval($value);
                }
            }
            $arraylines .= json_encode($values) . ",\n";
        }

        return $OUTPUT->render_from_template("local_kopere_bi/block_line_preview_google", [
            "koperebiitem_id" => $koperebielement->id,
            "arraylines" => $arraylines,
        ]);
    }
}
