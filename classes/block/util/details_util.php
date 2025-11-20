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

namespace local_kopere_bi\block\util;

use context_system;
use Exception;
use local_kopere_bi\block\i_type;
use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_dashboard\html\button;

/**
 * Class details_util
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class details_util {

    /**
     * Function html_details_block
     *
     * @param $block
     * @return string
     * @throws Exception
     */
    public function html_details_block($block) {
        global $OUTPUT;

        switch ($block->type) {
            case 'block-1':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-1", $data);
                break;
            case 'block-2':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-2", $data);
                break;
            case 'block-3':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-3", $data);
                break;
            case 'block-12':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-12", $data);
                break;
            case 'block-21':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-21", $data);
                break;
            case 'block-25':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-25", $data);
                break;
            case 'block-52':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-52", $data);
                break;
            case 'block-4':
                $data = [
                    "block_id" => $block->id,
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                    'details_block_item_4' => $this->details_block_item($block->id, 4),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-edit-block-4", $data);
                break;
        }

        return "";
    }

    /**
     * Function details_block_item
     *
     * @param $blockid
     * @param $blocknum
     * @return string
     * @throws Exception
     */
    private static function details_block_item($blockid, $blocknum) {
        global $DB;

        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);
        if ($koperebielement) {

            /** @var i_type $blockclass */
            $blockclass = "\\local_kopere_bi\\block\\{$koperebielement->type}";
            if (class_exists($blockclass)) {
                $title = string_util::get_string($koperebielement->title) . ": {$blockclass::get_name()}";
            } else {
                $title = string_util::get_string($koperebielement->title);
            }

            if (has_capability("local/kopere_bi:manage", context_system::instance())) {
                return "<h4 class='block-title details_block_item'>{$title}</h4>" .
                    button::edit(
                        get_string("edit_report", "local_kopere_bi"),
                        "?classname=bi-dashboard&method=type_block_edit&item_id={$koperebielement->id}", 'mr-4', false, true
                    ) .
                    button::icon_confirm(
                        "delete", "?classname=bi-dashboard&method=type_block_delete&item_id={$koperebielement->id}",
                        get_string("delete_report_text", "local_kopere_bi"), get_string("delete_report_title", "local_kopere_bi")
                    );
            } else {
                return "<h4 class='block-title details_block_item'>{$title}</h4>";
            }

        } else {
            return button::add(get_string("create_report", "local_kopere_bi"),
                "?classname=bi-dashboard&method=type_block_select_type&block_id={$blockid}&block_num={$blocknum}",
                "", false, true);
        }
    }

    /**
     * Function html_details_add
     *
     * @param $pageid
     * @throws Exception
     */
    public static function html_details_add($pageid) {
        global $OUTPUT;

        echo $OUTPUT->render_from_template("local_kopere_bi/blocks-details_add", []);
    }
}
