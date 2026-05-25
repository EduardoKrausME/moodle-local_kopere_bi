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
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\block\util\string_util;
use local_kopere_bi\output\renderer_bi_mustache;
use local_kopere_dashboard\html\data_table;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_select;
use local_kopere_dashboard\html\inputs\input_text;
use local_kopere_dashboard\html\inputs\input_textarea;
use local_kopere_dashboard\html\table_header_item;
use local_kopere_dashboard\util\json;
use local_kopere_dashboard\util\message;

/**
 * Class table
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table implements i_type {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws Exception
     */
    public static function get_name() {
        return get_string("table_name", "local_kopere_bi");
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("table_desc", "local_kopere_bi");
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
        code_util::input_commandsql($form, $koperebielement);
    }

    /**
     * Function is_edit_columns
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
     * @return bool
     * @throws Exception
     */
    public function edit_columns(form $form, $koperebielement) {

        $comand = sql_util::prepare_sql($koperebielement->commandsql);

        try {
            $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params, false, 5);
        } catch (\dml_read_exception $e) {
            message::print_danger("<div style='white-space:break-spaces'>{$e->debuginfo}</div>");
            return false;
        }
        message::print_info(get_string("table_info_topo", "local_kopere_bi"));
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

            message::print_info(get_string("table_info_types", "local_kopere_bi"));
            foreach ($lines[0] as $id => $line) {
                echo
                    "<fieldset><legend>" . get_string("table_edit_column", "local_kopere_bi") .
                    ": <strong><em>{$id}</em></strong></legend>";
                $this->select_data($koperebielement, $id);
                echo "</fieldset>";
            }
        } else {
            message::print_warning(get_string("sql_no_rows", "local_kopere_bi"));
            return false;
        }

        return true;
    }

    /**
     * Function select_data
     *
     * @param $koperebielement
     * @param $collkey
     * @throws Exception
     */
    private function select_data($koperebielement, $collkey) {
        global $PAGE;

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
                "key" => "userfullname",
                "value" => get_string("table_renderer_userfullname", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_USERPHOTO,
                "value" => get_string("table_renderer_userphoto", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_VISIBLE,
                "value" => get_string("table_renderer_visible", "local_kopere_bi"),
            ], [
                "key" => table_header_item::RENDERER_STATUS,
                "value" => get_string("table_renderer_status", "local_kopere_bi"),
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

        // Titles.
        $valuedefault = $collkey;
        if (isset($koperebielement->info_obj["column"][$collkey]["title"])) {
            $valuedefault = $koperebielement->info_obj["column"][$collkey]["title"];
        }
        $form = new form();
        $form->add_input(
            input_text::new_instance()
                ->set_title(get_string("table_col_title", "local_kopere_bi"))
                ->set_name("column-title[{$collkey}]")
                ->set_value($valuedefault)
        );

        // Types.
        $typedefault = "string";
        if (isset($koperebielement->info_obj["column"][$collkey]["type"])) {
            $typedefault = $koperebielement->info_obj["column"][$collkey]["type"];
            $changemustache = false;
        } else {
            if ($collkey == "firstname") {
                $typedefault = "userfullname";
            } else if ($collkey == "lastname") {
                $typedefault = "none";
            } else if (str_ends_with($collkey, "_id")) {
                $typedefault = "number";
            } else if (strpos($collkey, "time") !== false || strpos($collkey, "date") !== false) {
                $typedefault = table_header_item::RENDERER_DATETIME;
            } else if (strpos($collkey, "status") !== false || strpos($collkey, "enable") !== false) {
                $typedefault = table_header_item::RENDERER_STATUS;
            } else if (strpos($collkey, "visible") !== false) {
                $typedefault = table_header_item::RENDERER_VISIBLE;
            } else if (strpos($collkey, "filesize") !== false) {
                $typedefault = table_header_item::RENDERER_FILESIZE;
            }

            $changemustache = true;
        }
        $form->add_input(
            input_select::new_instance()
                ->set_title(get_string("table_renderer_title", "local_kopere_bi"))
                ->set_name("column-type[{$collkey}]")
                ->set_values($types)
                ->set_value($typedefault)
        );
        $PAGE->requires->strings_for_js(['inactive', 'active', 'visible', 'emaildisplayno'], 'moodle');
        $PAGE->requires->js_call_amd("local_kopere_bi/table", "select", [$collkey, $changemustache]);

        // Mustaches.
        $valuemustache = "";
        if (isset($koperebielement->info_obj["column"][$collkey]["mustache"])) {
            $valuemustache = $koperebielement->info_obj["column"][$collkey]["mustache"];
        }
        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("table_renderer_mustache", "local_kopere_bi"))
                ->set_name("column-mustache[{$collkey}]")
                ->set_value($valuemustache)
                ->set_style("height:45px;max-height:350px;")
                ->add_extras('oninput="this.style.height=\'\';this.style.height=(this.scrollHeight+5)+\'px\'"')
                ->add_extras('onfocus="this.style.height=\'\';this.style.height=(this.scrollHeight+5)+\'px\'"')
        );
        $PAGE->requires->js_call_amd("local_kopere_bi/load_ace", "getScript", ["columnmustache{$collkey}", "html", 3]);
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     * @return mixed|string
     * @throws Exception
     */
    public function preview($koperebielement) {
        $table = new data_table();

        if (!isset($koperebielement->info_obj["column"])) {
            return message::danger(get_string("table_column_not_configured", "local_kopere_bi"));
        }

        foreach ($koperebielement->info_obj["column"] as $key => $column) {

            if ($column["type"] == "none") {
                continue;
            }

            $name = $koperebielement->info_obj["column"][$key]["title"];
            $name = string_util::get_string($name);

            switch ($column["type"]) {
                case "number":
                    $table->add_header($name, $key, table_header_item::TYPE_INT);
                    break;
                case table_header_item::RENDERER_USERPHOTO:
                    if (isset($column["mustache"][3])) {
                        $table->add_header($name, $key);
                    } else {
                        $table->add_header($name, $key, table_header_item::RENDERER_USERPHOTO);
                    }
                    break;
                case table_header_item::RENDERER_STATUS:
                    if (isset($column["mustache"][3])) {
                        $table->add_header($name, $key);
                    } else {
                        $table->add_header($name, $key, table_header_item::RENDERER_STATUS);
                    }
                    break;
                case table_header_item::RENDERER_VISIBLE:
                    if (isset($column["mustache"][3])) {
                        $table->add_header($name, $key);
                    } else {
                        $table->add_header($name, $key, table_header_item::RENDERER_VISIBLE);
                    }
                    break;
                case table_header_item::RENDERER_DELETED:
                    if (!isset($column["mustache"][3])) {
                        $column["mustache"] = "<span class='kopere-bi-renderer-deleted-{{{{$key}}}}'></span>";
                        $table->add_header($name, $key);
                    } else {
                        $table->add_header($name, $key, table_header_item::RENDERER_DELETED);
                    }
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

        $table->set_ajax_url("view-ajax.php?classname=bi-chart_data&method=load_data&item_id={$koperebielement->id}");
        $returnhtml = $table->print_header("", true, true);
        $returnhtml .= $table->close(false, null, true, string_util::get_string($koperebielement->title));

        return $returnhtml;
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     * @return mixed|void
     * @throws Exception
     */
    public function get_chart_data($koperebielement) {
        global $CFG;

        $numexecs = 0;
        $execs = [];
        foreach ($koperebielement->info_obj["column"] as $key => $column) {
            if ($column["type"] == "userfullname" || $column["type"] == "translate") {
                $execs[$key] = $column["type"];
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

            $CFG->debugdeveloper = false;
            $mustache = new renderer_bi_mustache();
            $newlines = [];
            foreach ($lines as $line) {
                if ($numexecs) {
                    foreach ($execs as $key => $type) {
                        if ($type == "userfullname") {
                            $line->u_fullname = fullname($line);
                        }
                        if ($type == "translate") {
                            $line->$key = string_util::get_string($line->$key);
                        }
                    }
                }

                $mustacheline = $line;

                $value = $line->$key;
                foreach ($koperebielement->info_obj["column"] as $key => $column) {
                    if (isset($column["mustache"][3]) &&
                        $column["mustache"] != "{{{{$key}}}}" &&
                        isset($value[0])
                    ) {
                        $line->{"{$key}_mustache"} = $mustache->render_from_string($column["mustache"], $mustacheline);
                    }
                }

                $newlines[] = $line;
            }

            $lines = $newlines;
            $cache->set($koperebielement->id, $lines);
        }

        json::encode($lines);
        die();
    }

    /**
     * https://developers.google.com/chart/interactive/docs/gallery/table?hl=pt_br
     *
     * @param $koperebielement
     * @return string
     * @throws Exception
     */
    public function preview_google($koperebielement) {
        global $OUTPUT;

        $addcolumn = [];
        $formatter = [];

        $comand = sql_util::prepare_sql($koperebielement->commandsql);
        try {
            $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
        } catch (Exception $e) {
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

if (!function_exists('str_ends_with')) {
    /**
     * Function str_ends_with
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function str_ends_with($haystack, $needle) {
        if ('' === $needle || $needle === $haystack) {
            return true;
        }

        if ('' === $haystack) {
            return false;
        }

        $needlelength = \strlen($needle);

        return $needlelength <= \strlen($haystack) && 0 === substr_compare($haystack, $needle, -$needlelength);
    }
}
