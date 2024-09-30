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
use local_kopere_bi\util\sql_util;
use local_kopere_bi\util\string_util;
use local_kopere_dashboard\html\data_table;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_select;
use local_kopere_dashboard\html\inputs\input_text;
use local_kopere_dashboard\html\table_header_item;
use local_kopere_dashboard\util\json;
use local_kopere_dashboard\util\mensagem;

/**
 * Class table
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table implements i_type {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws \Exception
     */
    public static function get_name() {
        return get_string("table_name", "local_kopere_bi");
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws \Exception
     */
    public static function get_description() {
        return get_string("table_desc", "local_kopere_bi");
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
        code_util::load_ace_commandsql($form, $koperebielement);
    }

    /**
     * Function is_edit_columns
     *
     * @return bool|mixed
     */
    public function is_edit_columns() {
        return true;
    }

    /**
     * Function edit_columns
     *
     * @param form $form
     * @param $koperebielement
     *
     * @return bool
     * @throws \Exception
     */
    public function edit_columns(form $form, $koperebielement) {

        $comand = sql_util::prepare_sql($koperebielement->commandsql . " LIMIT 5");

        try {
            $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
        } catch (\Exception $e) {
            mensagem::print_danger($e->getMessage());
            return false;
        }

        mensagem::print_info(get_string("table_info_topo", "local_kopere_bi"));
        if (isset($lines[0])) {
            echo "<h3>" . get_string("table_first_5", "local_kopere_bi") . "</h3>";
            echo "<div style='white-space: nowrap;overflow: auto;margin-bottom: 20px;'>";
            echo "<table class='table table-bordered' style='margin-bottom: 0;'>";
            echo "<tr>";
            foreach ($lines[0] as $id => $line) {
                echo "<th>{$id}</div></th>";
            }
            echo "</tr>";
            foreach ($lines as $line) {
                echo "<tr>";
                foreach ($lines[0] as $id => $a) {
                    echo "<td>{$line->$id}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";

            mensagem::print_info(get_string("table_info_secound", "local_kopere_bi"));
            foreach ($lines[0] as $id => $line) {
                echo "<h3>" . get_string("table_edit_column", "local_kopere_bi") . ": <strong><em>{$id}</em></strong></h3>";
                $this->select_data($koperebielement, $id);
            }
        } else {
            mensagem::print_warning("0 rows");
            return false;
        }

        return true;
    }

    /**
     * Function select_data
     *
     * @param $koperebielement
     * @param $collname
     *
     * @throws \Exception
     */
    private function select_data($koperebielement, $collname) {

        $types = [
            [
                "key" => "none",
                "value" => get_string("table_renderer_none", "local_kopere_bi"),
            ], [
                "key" => "string",
                "value" => "...",
            ], [
                "key" => "number",
                "value" => get_string("table_renderer_number", "local_kopere_bi"),
            ], [
                "key" => "fullname",
                "value" => get_string("table_renderer_fullname", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_USERPHOTO,
                "value" => get_string("table_renderer_userphoto", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_TRUEFALSE,
                "value" => get_string("table_renderer_truefalse", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_STATUS,
                "value" => get_string("table_renderer_status", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_VISIBLE,
                "value" => get_string("table_renderer_visible", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_DELETED,
                "value" => get_string("table_renderer_deleted", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_DATE,
                "value" => get_string("table_renderer_date", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_DATETIME,
                "value" => get_string("table_renderer_datetime", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_TIME,
                "value" => get_string("table_renderer_seconds", "local_kopere_bi"),
            ], [
                "key" => "translate",
                "value" => get_string("table_renderer_translate", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_FILESIZE,
                "value" => get_string("table_renderer_filesize", "local_kopere_bi"),
            ],
        ];

        $typedefault = null;
        if (isset($koperebielement->info_obj["column"]["type"][$collname])) {
            $typedefault = $koperebielement->info_obj["column"]["type"][$collname];
        } else {
            if ($collname == "firstname") {
                $typedefault = "fullname";
            } else if ($collname == "lastname") {
                $typedefault = "none";
            } else if (str_ends_with($collname, '_id')) {
                $typedefault = "number";
            } else if (strpos($collname, "time") !== false || strpos($collname, "date") !== false) {
                $typedefault = table_header_item::RENDERER_DATETIME;
            } else if (strpos($collname, "status") !== false || strpos($collname, "enable") !== false) {
                $typedefault = table_header_item::RENDERER_STATUS;
            } else if (strpos($collname, "visible") !== false) {
                $typedefault = table_header_item::RENDERER_VISIBLE;
            } else if (strpos($collname, "filesize") !== false) {
                $typedefault = table_header_item::RENDERER_FILESIZE;
            } else {
                $typedefault = "string";
            }
        }

        $valuedefault = $collname;
        if (isset($koperebielement->info_obj["column"]["name"][$collname])) {
            $valuedefault = $koperebielement->info_obj["column"]["name"][$collname];
        }

        $form = new form();
        echo "<div class='d-flex' style='gap:10px'>";
        $form->add_input(
            input_text::new_instance()
                ->set_title(get_string("table_col_title", "local_kopere_bi"))
                ->set_name("column-name[{$collname}]")
                ->set_value($valuedefault)
        );
        $form->add_input(
            input_select::new_instance()
                ->set_title(get_string("table_renderer_title", "local_kopere_bi"))
                ->set_name("column-type[{$collname}]")
                ->set_values($types)
                ->set_value($typedefault)
        );
        echo "</div>";
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     *
     * @return mixed|string
     * @throws \coding_exception
     */
    public function preview($koperebielement) {
        $table = new data_table();

        if (!isset($koperebielement->info_obj["column"]["type"])) {
            return mensagem::danger(get_string("table_column_not_configured", "local_kopere_bi"));
        }

        foreach ($koperebielement->info_obj["column"]["type"] as $key => $type) {
            if ($type == "none") {
                continue;
            }

            $name = $koperebielement->info_obj["column"]["name"][$key];
            $name = string_util::get_string($name);

            switch ($type) {
                case "number":
                    $table->add_header($name, $key, table_header_item::TYPE_INT);
                    break;
                case table_header_item::RENDERER_USERPHOTO:
                    $table->add_header($name, $key, table_header_item::RENDERER_USERPHOTO);
                    break;
                case table_header_item::RENDERER_TRUEFALSE:
                    $table->add_header($name, $key, table_header_item::RENDERER_TRUEFALSE);
                    break;
                case table_header_item::RENDERER_STATUS:
                    $table->add_header($name, $key, table_header_item::RENDERER_STATUS);
                    break;
                case table_header_item::RENDERER_VISIBLE:
                    $table->add_header($name, $key, table_header_item::RENDERER_VISIBLE);
                    break;
                case table_header_item::RENDERER_DELETED:
                    $table->add_header($name, $key, table_header_item::RENDERER_DELETED);
                    break;
                case table_header_item::RENDERER_DATE:
                    $table->add_header($name, $key, table_header_item::RENDERER_DATE);
                    break;
                case table_header_item::RENDERER_DATETIME:
                    $table->add_header($name, $key, table_header_item::RENDERER_DATETIME);
                    break;
                case table_header_item::RENDERER_TIME:
                    $table->add_header($name, $key, table_header_item::RENDERER_TIME);
                    break;
                case table_header_item::RENDERER_FILESIZE:
                    $table->add_header($name, $key, table_header_item::RENDERER_FILESIZE);
                    break;
                default:
                    $table->add_header($name, $key);
            }
        }

        $returnhtml = "";

        $table->set_ajax_url("?classname=bi-chart_data&method=load_data&item_id={$koperebielement->id}");
        $returnhtml .= $table->print_header("", true, true);
        $returnhtml .= $table->close(false, null, true, string_util::get_string($koperebielement->title));

        return $returnhtml;
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

        $numexecs = 0;
        $execs = [];
        foreach ($koperebielement->info_obj["column"]["type"] as $key => $type) {
            if ($type == "fullname" || $type == "translate") {
                $execs[$key] = $type;
                $numexecs++;
            }
        }

        $cache = cache_util::get_cache_make($koperebielement->cache);
        if (false && $cache->has($koperebielement->id)) {
            $lines = $cache->get($koperebielement->id);
        } else {
            $comand = sql_util::prepare_sql($koperebielement->commandsql);
            try {
                $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
            } catch (\Exception $e) {
                mensagem::print_danger($e->getMessage());
                return;
            }

            if ($numexecs) {
                $returnlines = [];
                foreach ($lines as $line) {
                    foreach ($execs as $key => $type) {
                        if ($type == "fullname") {
                            $line->$key = fullname($line);
                        }
                        if ($type == "translate") {
                            $line->$key = string_util::get_string($line->$key);
                        }
                    }

                    $returnlines[] = $line;
                }

                $lines = $returnlines;
            }

            $cache->set($koperebielement->id, $lines);
        }

        json::encode($lines);
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

        $addcolumn = [];
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
            $addcolumn[] = "data.addColumn('string', '{$column}');";
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

        return $OUTPUT->render_from_template("local_kopere_bi/block_table_preview_google", [
            "koperebiitem_id" => $koperebielement->id,
            "addcolumn" => $addcolumn,
            "linechart" => json_encode($linechart, JSON_PRETTY_PRINT),
            "formatter" => implode("\n\t\t\t\t", $formatter),
        ]);
    }
}
