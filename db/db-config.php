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
 * Function reset_bi_reports
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @throws Exception
 */

use local_kopere_bi\vo\local_kopere_bi_cat;

/**
 * Function reset_bi_reports
 *
 * @throws dml_exception
 */
function reset_bi_reports() {
    global $DB, $CFG;

    set_config("theme_palette", "default", "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_pie_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_pie_default", $json, "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_column_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_column_default", $json, "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_area_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_area_default", $json, "local_kopere_bi");

    $json = file_get_contents(__DIR__ . "/files/chart_line_default.js");
    $json = str_replace("json = ", "", $json);
    set_config("chart_line_default", $json, "local_kopere_bi");

    try {
        $DB->delete_records("local_kopere_bi_cat");
        $DB->delete_records("local_kopere_bi_page");
        $DB->delete_records("local_kopere_bi_block");
        $DB->delete_records("local_kopere_bi_element");
    } catch (Exception $e) {
        return;
    }

    // Load default pages.
    $pagefiles = glob(__DIR__ . "/files/page-*.json");
    foreach ($pagefiles as $pagefile) {
        $jsonpage = file_get_contents($pagefile);

        $page = json_decode($jsonpage);

        $koperebipage = clone $page;
        unset($koperebipage->blocks);

        if (isset($koperebipage->pre_requisit)) {
            if ($koperebipage->pre_requisit == "mysql") {
                $ok = false;
                if ($CFG->dbtype == "mysqli") {
                    $ok = true;
                } else if ($CFG->dbtype =="mariadb") {
                    $ok = true;
                }
                if (!$ok) {
                    continue;
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
            $koperebiblock = clone $block;
            unset($koperebiblock->elements);

            if (isset($koperebiblock->pre_requisit)) {
                if ($koperebiblock->pre_requisit == "mysql") {
                    $ok = false;
                    if ($CFG->dbtype == "mysqli") {
                        $ok = true;
                    } else if ($CFG->dbtype =="mariadb") {
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
                $koperebielement = clone $element;

                if (isset($koperebielement->pre_requisit)) {
                    if ($koperebielement->pre_requisit == "mysql") {
                        $ok = false;
                        if ($CFG->dbtype == "mysqli") {
                            $ok = true;
                        } else if ($CFG->dbtype =="mariadb") {
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
