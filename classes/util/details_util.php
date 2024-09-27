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

namespace local_kopere_bi\util;

use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_dashboard\html\button;
use local_kopere_dashboard\util\header;
use local_kopere_dashboard\util\title_util;

/**
 * Class details_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class details_util {

    /**
     * Function html_details_block
     *
     * @param $block
     *
     * @return string
     * @throws \Exception
     */
    public function html_details_block($block) {
        switch ($block->type) {
            case 'block-1':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block'>{$this->details_block_item($block->id, 1)}</div>
                        </div>
                    </div>";
                break;
            case 'block-2':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 2)}</div>
                        </div>
                    </div>";
                break;
            case 'block-3':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 2)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 3)}</div>
                        </div>
                    </div>";
                break;
            case 'block-12':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block' style='width:225%'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 2)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 3)}</div>
                        </div>
                    </div>";
                break;
            case 'block-21':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 2)}</div>
                            <div class='block' style='width:225%'>{$this->details_block_item($block->id, 3)}</div>
                        </div>
                    </div>";
                break;
            case 'block-25':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block' style='width:225%'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 2)}</div>
                        </div>
                    </div>";
                break;
            case 'block-52':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block' style='width:225%'>{$this->details_block_item($block->id, 2)}</div>
                        </div>
                    </div>";
                break;
            case 'block-4':
                return "
                    <div class='line' id='blockid-{$block->id}' data-blockid='{$block->id}'>
                        <div class='blocks'>
                            <div class='block'>{$this->details_block_item($block->id, 1)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 2)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 3)}</div>
                            <div class='block'>{$this->details_block_item($block->id, 4)}</div>
                        </div>
                    </div>";
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
     * @throws \Exception
     */
    private static function details_block_item($blockid, $blocknum) {
        global $DB;

        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);
        if ($koperebielement) {
            return
                "<h4 class='block-title'>".string_util::get_string($koperebielement->title)."</h4>" .
                button::edit(get_string('edit_report', 'local_kopere_bi'),
                    "?classname=bi-dashboard&method=type_block_edit&item_id={$koperebielement->id}", 'mr-4', false, true) .
                button::icon_confirm('delete',
                    "?classname=bi-dashboard&method=type_block_delete&item_id={$koperebielement->id}",
                    get_string("delete_report_text", "local_kopere_bi"),
                    get_string("delete_report_title", "local_kopere_bi"));

        } else {
            return button::add(get_string('create_report', 'local_kopere_bi'),
                "?classname=bi-dashboard&method=type_block_select_type&block_id={$blockid}&block_num={$blocknum}",
                '', false, true);
        }
    }

    /**
     * Function html_details_add
     *
     * @param $pageid
     *
     * @throws \coding_exception
     */
    public static function html_details_add($pageid) {

        echo "
            <div style='display:none' id='dialog-confirm-block-add'
                 title='" . get_string('new_block', 'local_kopere_bi') . "'>
                <div>" . get_string('click_new_block', 'local_kopere_bi') . "</div>

                <span class='line line-add' data-type='block-1'>
                    <span>" . get_string('new_block_1', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-2'>
                    <span>" . get_string('new_block_2', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-25'>
                    <span>" . get_string('new_block_25', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty' style='width:225%'></div>
                        <div class='block block-empty'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-52'>
                    <span>" . get_string('new_block_52', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty'></div>
                        <div class='block block-empty' style='width:225%'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-3'>
                    <span>" . get_string('new_block_3', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-12'>
                    <span>" . get_string('new_block_12', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty' style='width:225%'></div>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-21'>
                    <span>" . get_string('new_block_21', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                        <div class='block block-empty' style='width:225%'></div>
                    </div>
                </span>

                <span class='line line-add' data-type='block-4'>
                    <span>" . get_string('new_block_4', 'local_kopere_bi') . "</span>
                    <div class='blocks'>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                        <div class='block block-empty'></div>
                    </div>
                </span>
            </div>";
    }
}
