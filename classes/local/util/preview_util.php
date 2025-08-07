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

namespace local_kopere_bi\local\util;

use Exception;
use local_kopere_bi\local\block\i_type;
use local_kopere_bi\output\renderer_bi_mustache;
use local_kopere_bi\local\vo\local_kopere_bi_block;
use local_kopere_bi\local\vo\local_kopere_bi_element;
use local_kopere_dashboard\html\button;
use local_kopere_dashboard\util\message;

/**
 * Class preview_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_util {

    /**
     * Function details_block
     *
     * @param local_kopere_bi_block $block
     * @return string
     * @throws Exception
     */
    public function details_block($block) {
        global $OUTPUT;

        switch ($block->type) {
            case 'block-1':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-1", $data);
                break;
            case 'block-2':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-2", $data);
                break;
            case 'block-3':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-3", $data);
                break;
            case 'block-4':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                    'details_block_item_4' => $this->details_block_item($block->id, 4),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-4", $data);
                break;
            case 'block-12':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-12", $data);
                break;
            case 'block-21':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                    'details_block_item_3' => $this->details_block_item($block->id, 3),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-21", $data);
                break;
            case 'block-25':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-25", $data);
                break;
            case 'block-52':
                $data = [
                    'details_block_item_1' => $this->details_block_item($block->id, 1),
                    'details_block_item_2' => $this->details_block_item($block->id, 2),
                ];
                return $OUTPUT->render_from_template("local_kopere_bi/blocks-preview-block-52", $data);
                break;
        }

        return "";
    }

    /**
     * Function details_block_item
     *
     * @param $blockid
     * @param $blocknum
     *
     * @return string
     * @throws Exception
     */
    private function details_block_item($blockid, $blocknum) {
        global $DB, $OUTPUT, $USER;

        $return = "";

        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);

        if (isset($koperebielement->id)) {
            $koperebielement->info_obj = json_decode($koperebielement->info, true);

            $return .= "<div class='chart-box chart-box-{$koperebielement->type}' id='chart-box-{$koperebielement->id}'>";
            $return .= "<div class='element-box theme-{$koperebielement->theme} type-{$koperebielement->type}'>";

            /** @var i_type $blockclass */
            $blockclass = "\\local_kopere_bi\\local\\block\\{$koperebielement->type}";
            if (class_exists($blockclass)) {
                $title = string_util::get_string($koperebielement->title);

                if (isset($USER->editing) && $USER->editing) {
                    $title .= button::edit(get_string("edit_report", "local_kopere_bi"),
                        "?classname=bi-dashboard&method=type_block_edit&item_id={$koperebielement->id}", 'ml-2', false, true);
                }

                /** @var i_type $block */
                $block = new $blockclass();

                $mustache = new renderer_bi_mustache();

                $htmlbefore = $mustache->render_from_string($koperebielement->html_before, [], "html-before");
                $htmlafter = $mustache->render_from_string($koperebielement->html_after, [], "html-after");
                if (class_exists($blockclass)) {
                    $data = [
                        "title" => $title,
                        "title_extra" => $block->title_extra($koperebielement),
                        "preview" => $block->preview($koperebielement),
                        "htmlbefore" => $htmlbefore,
                        "htmlafter" => $htmlafter,
                    ];
                    $return .= $OUTPUT->render_from_template("local_kopere_bi/blocks-details_block_item", $data);
                } else {
                    $data = ["title" => $title];
                    $return .= $OUTPUT->render_from_template("local_kopere_bi/blocks-details_block_item", $data);
                }

            } else {
                message::print_danger(get_string("block_not_found6", "local_kopere_bi"));
            }

            $return .= scss_util::build_css($koperebielement);

            $return .= "</div></div>";
        }
        return $return;
    }
}
