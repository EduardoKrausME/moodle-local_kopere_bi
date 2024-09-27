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

use local_kopere_bi\block\i_type;
use local_kopere_bi\filter;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_dashboard\util\mensagem;

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
     *
     * @return string
     * @throws \Exception
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    public function details_block($block) {
        switch ($block->type) {
            case 'block-1':
                return "
                    <div class='row'>
                        <div class='col-md-12'>
                            {$this->details_block_item($block->id, 1)}
                        </div>
                    </div>";
                break;
            case 'block-2':
                return "
                    <div class='row'>
                        <div class='col-lg-6'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-lg-6'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                    </div>";
                break;

            case 'block-25':
                return "
                    <div class='row'>
                        <div class='col-xl-8'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-xl-4'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                    </div>";
                break;

            case 'block-52':
                return "
                    <div class='row'>
                        <div class='col-xl-4'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-xl-8'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                    </div>";
                break;

            case 'block-3':
                return "
                    <div class='row'>
                        <div class='col-xl-4'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-xl-4'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                        <div class='col-xl-4'>
                            {$this->details_block_item($block->id, 3)}
                          </div>
                    </div>";
                break;
            case 'block-12':
                return "
                    <div class='row'>
                        <div class='col-xl-6'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 3)}
                          </div>
                    </div>";
                break;
            case 'block-21':
                return "
                    <div class='row'>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                        <div class='col-xl-6'>
                            {$this->details_block_item($block->id, 3)}
                          </div>
                    </div>";
                break;
            case 'block-4':
                return "
                    <div class='row'>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 1)}
                          </div>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 2)}
                          </div>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 3)}
                          </div>
                        <div class='col-xl-3'>
                            {$this->details_block_item($block->id, 4)}
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
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    private function details_block_item($blockid, $blocknum) {
        global $DB;

        $return = "";

        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);

        if (isset($koperebielement->id)) {
            $koperebielement->info_obj = json_decode($koperebielement->info, true);

            $return .= "<div class='chart-box' id='chart-box-{$koperebielement->id}'>";
            $return .= "<div class='element-box theme-{$koperebielement->theme} type-{$koperebielement->type}'>";

            $class = "\\local_kopere_bi\\block\\{$koperebielement->type}";
            if (class_exists($class)) {
                /** @var i_type $block */
                $block = new $class();

                $return .= "
                    <div class='block-heading'>
                        <h4 class='block-title'>".string_util::get_string($koperebielement->title)."</h4>
                        {$block->title_extra($koperebielement)}
                        <div class='block-controls'></div>
                    </div>
                    {$koperebielement->html_before}
                    {$block->preview($koperebielement)}
                    {$koperebielement->html_after}";
            } else {
                mensagem::print_danger(get_string('block_not_found', 'local_kopere_bi'));
            }

            $return .= scss_util::build_css($koperebielement);

            $return .= '</div></div>';
        }
        return $return;
    }
}
