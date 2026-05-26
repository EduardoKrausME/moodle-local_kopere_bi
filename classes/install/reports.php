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

/**
 * reports file
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\install;

use Exception;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_cat;
use local_kopere_bi\vo\local_kopere_bi_element;

/**
 * Class reports
 *
 * @package local_kopere_bi
 */
class reports {
    /**
     * Function from_file
     *
     * @param string $pagefile
     * @throws Exception
     */
    public static function from_file($pagefile) {
        $jsonpage = file_get_contents($pagefile);

        $page = json_decode($jsonpage);
        if (!isset($page->title)) {
            return;
        }
        self::from_json($page);
    }

    /**
     * from_json
     *
     * @param $page
     * @return void
     * @throws Exception
     */
    public static function from_json($page) {
        global $DB;

        $koperebipage = clone $page;
        unset($koperebipage->blocks);

        if (isset($koperebipage->pre_requisit) && $koperebipage->pre_requisit == "mysql") {
            if ($DB->get_dbfamily() != "mysql") {
                return;
            }
        }

        /** @var local_kopere_bi_cat $category */
        $category = $page->category;
        $category->id = self::save_record_by_refkey("local_kopere_bi_cat", $category);

        $koperebipage->time = time();
        $koperebipage->cat_id = $category->id;
        $page->id = self::save_record_by_refkey("local_kopere_bi_page", $koperebipage);

        foreach ($page->blocks as $block) {
            /** @var local_kopere_bi_block $koperebiblock */
            $koperebiblock = clone $block;
            unset($koperebiblock->elements);

            if (isset($koperebiblock->pre_requisit) && $koperebiblock->pre_requisit == "mysql") {
                if ($DB->get_dbfamily() != "mysql") {
                    continue;
                }
            }

            $koperebiblock->page_id = $page->id;
            $koperebiblock->time = time();
            $block->id = self::save_record_by_refkey("local_kopere_bi_block", $koperebiblock);

            foreach ($block->elements as $element) {
                /** @var local_kopere_bi_element $koperebielement */
                $koperebielement = clone $element;

                if (isset($koperebielement->pre_requisit) && $koperebielement->pre_requisit == "mysql") {
                    if ($DB->get_dbfamily() != "mysql") {
                        continue;
                    }
                }

                if (!is_string($koperebielement->info)) {
                    $koperebielement->info = json_encode($koperebielement->info);
                }
                $koperebielement->block_id = $block->id;
                $koperebielement->time = time();
                $element->id = self::save_record_by_refkey("local_kopere_bi_element", $koperebielement);
            }
        }
    }

    /**
     * Inserts a record or updates it when the same refkey already exists.
     *
     * @param string $table
     * @param object $record
     * @return int
     */
    private static function save_record_by_refkey($table, $record) {
        global $DB;

        if (!empty($record->refkey)) {
            $conditions = ["refkey" => $record->refkey];
            $itens = $DB->get_records($table, $conditions);
            if ($itens) {
                if (count($itens) == 1) {
                    $record->id = reset($itens)->id;
                    if ($table != "local_kopere_bi_cat") {
                        $DB->update_record($table, $record);
                    }
                    return $record->id;
                } else if (count($itens) > 1) {
                    $DB->delete_records($table, $conditions);
                }
            }
        }

        return $DB->insert_record($table, $record);
    }

}
