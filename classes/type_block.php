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

namespace local_kopere_bi;

use Exception;
use local_kopere_bi\block\area;
use local_kopere_bi\block\column;
use local_kopere_bi\block\html;
use local_kopere_bi\block\i_type;
use local_kopere_bi\block\info;
use local_kopere_bi\block\line;
use local_kopere_bi\block\maps;
use local_kopere_bi\block\pie;
use local_kopere_bi\block\table;
use local_kopere_bi\block\util\cache_util;
use local_kopere_bi\block\util\string_util;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_bi\vo\local_kopere_bi_page;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\header;

/**
 * Class type_block
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class type_block extends bi_all {

    /**
     * Function select_type
     *
     * @param $blockid
     * @param $blocknum
     * @throws Exception
     */
    public function select_type($blockid, $blocknum) {
        global $DB, $PAGE, $OUTPUT;

        /** @var local_kopere_bi_block $block */
        $block = $DB->get_record("local_kopere_bi_block", ["id" => $blockid]);
        header::notfound_null($block, get_string("block_not_found", "local_kopere_bi"));

        /** @var local_kopere_bi_page $page */
        $page = $DB->get_record("local_kopere_bi_page", ["id" => $block->page_id]);
        header::notfound_null($page, get_string("page_not_found", "local_kopere_bi"));

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"),
            "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(string_util::get_string($page->title),
            "?classname=bi-dashboard&method=edit_page&page_id={$page->id}");
        dashboard_util::add_breadcrumb(get_string("select_report_select_type", "local_kopere_bi"));
        dashboard_util::start_page();

        echo '<div class="element-box">';
        echo "<h3>" . get_string("select_report_select_type_desc", "local_kopere_bi") . "</h3>";

        $buttons = [
            [
                "id" => "pie",
                "title" => pie::get_name(),
                "description" => pie::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=pie",
            ], [
                "id" => "line",
                "title" => line::get_name(),
                "description" => line::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=line",
            ], [
                "id" => "area",
                "title" => area::get_name(),
                "description" => area::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=area",
            ], [
                "id" => "column",
                "title" => column::get_name(),
                "description" => column::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=column",
            ], [
                "id" => "table",
                "title" => table::get_name(),
                "description" => table::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=table",
            ], [
                "id" => "maps",
                "title" => maps::get_name(),
                "description" => maps::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=maps",
            ], [
                "id" => "info",
                "title" => info::get_name(),
                "description" => info::get_description(),
                "link" =>
                    "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=info",
            ], [
                "id" => "html",
                "title" => html::get_name(),
                "description" => html::get_description(),
                "link" => "?classname=bi-dashboard&method=type_block_edit&block_id={$blockid}&block_num={$blocknum}&type=html",
            ],
        ];

        foreach ($buttons as $button) {
            echo $OUTPUT->render_from_template("local_kopere_bi/type_block-select_type", $button);
        }

        echo "</div>";
        dashboard_util::end_page();

        $PAGE->requires->js_call_amd("local_kopere_bi/choose-block-type", "charts", []);
    }

    /**
     * Function type_block_edit_salvar
     *
     * @param local_kopere_bi_element $koperebielement
     * @param local_kopere_bi_page $koperebipage
     * @param i_type $block
     * @throws Exception
     */
    public static function type_block_edit_salvar($koperebielement, $koperebipage, i_type $block) {
        global $DB;

        if (form::check_post()) {
            $koperebielement->title = required_param("title", PARAM_TEXT);
            $koperebielement->cache = optional_param("cache", "none", PARAM_TEXT);
            $koperebielement->reload = optional_param("reload", "none", PARAM_TEXT);

            $koperebielement->commandsql = required_param("commandsql", PARAM_TEXT);
            $koperebielement->commandsql = str_replace("\t", "    ", $koperebielement->commandsql);
            $koperebielement->commandsql = preg_replace("/;(\s+)?$/s", "", $koperebielement->commandsql);

            $koperebielement->theme = optional_param("theme", "", PARAM_TEXT);
            $koperebielement->css = optional_param("css", "", PARAM_TEXT);
            $koperebielement->html_before = optional_param("html_before", "", PARAM_RAW);
            $koperebielement->html_after = optional_param("html_after", "", PARAM_RAW);

            $info = optional_param_array("info", [], PARAM_TEXT);

            $koperebielement->info_obj = array_merge($koperebielement->info_obj, $info);

            $infohtml = optional_param("infohtml", false, PARAM_RAW);
            if ($infohtml) {
                $koperebielement->info_obj["html"] = $infohtml;
            }

            $elementtype = optional_param("element_type", false, PARAM_TEXT);
            if ($elementtype) {
                $koperebielement->type = $elementtype;
            }

            $koperebielement->info = json_encode($koperebielement->info_obj, JSON_PRETTY_PRINT);

            if (isset($koperebielement->id)) {
                $DB->update_record("local_kopere_bi_element", $koperebielement);

                cache_util::delete($koperebielement->id);

            } else {

                $blockid = required_param("block_id", PARAM_TEXT);
                $blocknum = required_param("block_num", PARAM_TEXT);

                /** @var local_kopere_bi_element $element */
                $element = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);

                if ($element) {
                    $koperebielement->id = $element->id;
                    $DB->update_record("local_kopere_bi_element", $koperebielement);

                    cache_util::delete($koperebielement->id);
                } else {
                    $koperebielement->refkey = \local_kopere_dashboard\util\html::link($koperebielement->title);
                    $koperebielement->block_id = $blockid;
                    $koperebielement->block_num = $blocknum;
                    $koperebielement->type = required_param("type", PARAM_TEXT);
                    $koperebielement->time = time();
                    $koperebielement->id = $DB->insert_record("local_kopere_bi_element", $koperebielement);
                }
            }

            if ($block->is_edit_columns()) {
                header::location("?classname=bi-dashboard&method=type_block_edit_columns&item_id={$koperebielement->id}");
            } else {
                header::location("?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}");
            }
        }
    }

    /**
     * Function type_block_edit_columns_salvar
     *
     * @param $koperebielement
     * @param $koperebipage
     * @param i_type $block
     * @throws Exception
     */
    public static function type_block_edit_columns_salvar($koperebielement, $koperebipage, i_type $block) {
        global $DB;

        if (form::check_post()) {
            $columntitle = required_param_array("column-title", PARAM_TEXT);
            $columntype = required_param_array("column-type", PARAM_TEXT);
            $columnmustache = required_param_array("column-mustache", PARAM_RAW);

            if (!is_array($koperebielement->info_obj)) {
                $koperebielement->info_obj = [];
            }

            $koperebielement->info_obj["column"] = [];
            foreach ($columntitle as $key => $title) {
                $koperebielement->info_obj["column"][$key] = [
                    "key" => $key,
                    "title" => $title,
                    "type" => $columntype[$key],
                    "mustache" => @$columnmustache[$key],
                ];
            }

            $koperebielement->info = json_encode($koperebielement->info_obj, JSON_PRETTY_PRINT);

            $DB->update_record("local_kopere_bi_element", $koperebielement);
            cache_util::delete($koperebielement->id);

            header::location("?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}");
        }
    }
}
