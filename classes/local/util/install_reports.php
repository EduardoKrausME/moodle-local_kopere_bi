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
 * install_reports file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\local\util;

use Exception;
use local_kopere_bi\local\vo\local_kopere_bi_block;
use local_kopere_bi\local\vo\local_kopere_bi_cat;
use local_kopere_bi\local\vo\local_kopere_bi_element;

/**
 * Class install_reports
 *
 * @package local_kopere_bi
 */
class install_reports {
    /**
     * Function from_file
     *
     * @param string $pagefile
     * @throws Exception
     */
    public static function from_file($pagefile) {
        $jsonpage = file_get_contents($pagefile);

        $page = json_decode($jsonpage);
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

        if (isset($koperebipage->pre_requisit)) {
            if ($koperebipage->pre_requisit == "mysql") {
                $ok = false;
                if ($DB->get_dbfamily() == "mysql") {
                    $ok = true;
                }
                if (!$ok) {
                    return;
                }
            }
        }

        /** @var local_kopere_bi_cat $category */
        $category = $DB->get_record("local_kopere_bi_cat", ["refkey" => $page->category->refkey]);
        if (!$category) {
            $category = $DB->get_record("local_kopere_bi_cat", ["title" => $page->category->title]);
            if (!$category) {
                $category = $page->category;
                $category->id = $DB->insert_record("local_kopere_bi_cat", $category);
            }
        }

        $koperebipage->time = time();
        $koperebipage->cat_id = $category->id;
        $page->id = $DB->insert_record("local_kopere_bi_page", $koperebipage);

        foreach ($page->blocks as $block) {
            /** @var local_kopere_bi_block $koperebiblock */
            $koperebiblock = clone $block;
            unset($koperebiblock->elements);

            if (isset($koperebiblock->pre_requisit)) {
                if ($koperebiblock->pre_requisit == "mysql") {
                    $ok = false;
                    if ($DB->get_dbfamily() == "mysql") {
                        $ok = true;
                    }
                    if (!$ok) {
                        continue;
                    }
                }
            }

            $koperebiblock->page_id = $page->id;
            $koperebiblock->time = time();
            $block->id = $DB->insert_record("local_kopere_bi_block", $koperebiblock);

            foreach ($block->elements as $element) {
                /** @var local_kopere_bi_element $koperebielement */
                $koperebielement = clone $element;

                if (isset($koperebielement->pre_requisit)) {
                    if ($koperebielement->pre_requisit == "mysql") {
                        $ok = false;
                        if ($DB->get_dbfamily() == "mysql") {
                            $ok = true;
                        }
                        if (!$ok) {
                            continue;
                        }
                    }
                }

                $koperebielement->block_id = $block->id;
                $koperebielement->time = time();
                $element->id = $DB->insert_record("local_kopere_bi_element", $koperebielement);
            }
        }
    }
}
