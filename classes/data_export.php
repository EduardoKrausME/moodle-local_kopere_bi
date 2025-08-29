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
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_cat;
use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_bi\vo\local_kopere_bi_page;
use local_kopere_dashboard\util\header;

/**
 * Class data_export
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_export {
    /**
     * Function json
     *
     * @throws Exception
     */
    public function json() {
        global $DB;

        $string = [];
        if (file_exists(__DIR__ . "/../../../lang/pt_br/moodle.php")) {
            require_once(__DIR__ . "/../../../lang/pt_br/moodle.php");
        }
        $stringmoodle = $string;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        /** @var local_kopere_bi_cat $koperebicat */
        $koperebicat = $DB->get_record("local_kopere_bi_cat", ["id" => $koperebipage->cat_id]);
        header::notfound_null($koperebicat, get_string("cat_not_found", "local_kopere_bi"));

        $data = (object)[
            "title" => $this->get_key_by_value($koperebipage->refkey, $koperebipage->title, "page_title"),
            "description" => $this->get_key_by_value($koperebipage->refkey, $koperebipage->description, "page_description"),
            "refkey" => $koperebipage->refkey,
            "category" => (object)[
                "title" => $this->get_key_by_value($koperebipage->refkey, $koperebicat->title, "category_title"),
                "refkey" => $koperebicat->refkey,
                "sortorder" => $koperebicat->sortorder,
                "description" => $this->get_key_by_value($koperebipage->refkey, $koperebicat->description, "category_description"),
            ],
            "blocks" => [],
        ];

        $koperebiblocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $koperebipage->id]);
        /** @var local_kopere_bi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            $elements = [];

            $koperebielements = $DB->get_records("local_kopere_bi_element", ["block_id" => $koperebiblock->id]);
            /** @var local_kopere_bi_element $koperebielement */
            foreach ($koperebielements as $koperebielement) {
                unset($koperebielement->id);
                unset($koperebielement->block_id);
                unset($koperebielement->time);

                $infos = json_decode($koperebielement->info, true);
                foreach ($infos["column"] as $key => $col) {
                    if ($col["type"] == "none") {
                        continue;
                    }

                    if ($col["key"] == "firstname") {
                        $col["title"] = "lang::u_fullname::local_kopere_bi";
                    } else if ($col["key"] != "name" && isset($stringmoodle[$col["key"]])) {
                        $col["title"] = "lang::{$col["key"]}::moodle";
                    } else {
                        $col["title"] = $this->get_key_by_value(
                            $koperebipage->refkey, $col["title"], "{$koperebielement->refkey}_{$col["key"]}");
                    }

                    $infos["column"][$key]["title"] = $col["title"];
                }

                $koperebielement->info = json_encode($infos, JSON_PRETTY_PRINT);
                $koperebielement->title = $this->get_key_by_value(
                    $koperebipage->refkey, $koperebielement->title, "{$koperebielement->refkey}_cat_title");

                $elements[] = $koperebielement;
            }

            unset($koperebiblock->id);
            unset($koperebiblock->page_id);
            unset($koperebiblock->time);

            $koperebiblock->elements = $elements;

            $data->blocks[] = $koperebiblock;
        }
        if ($this->missingstrings) {
            echo "<a href='?classname=bi-data_export&method=json&page_id={$pageid}&ignoremissingstrings=1'>Export anyway</a>";
            die;
        }

        ob_clean();
        header("Content-Type: application/json; charset: utf-8");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"page-{$koperebipage->refkey}.json\"");
        die(json_encode($data, JSON_NUMERIC_CHECK + JSON_PRETTY_PRINT) . "\n");
    }

    /**
     * Function to find the key based on a value in an array
     *
     * @param string $refkey
     * @param string $value
     * @param string $stringlast
     * @return string
     */
    private function get_key_by_value($refkey, $value, $stringlast = "") {

        if (strpos($value, "::") || $value == "#" || $value == "") {
            return $value;
        }

        global $CFG;
        $filelangs = glob("{$CFG->dirroot}/local/*/lang/pt_br/*.php");
        foreach ($filelangs as $filelang) {
            $component = preg_replace('/.*\/(local)\/(\w+)\/lang\/.*/', '$1_$2', $filelang);

            $string = [];
            require($filelang);
            if ($key = array_search($value, $string)) {
                return "lang::{$key}::{$component}";
            }
        }

        $ignoremissingstrings = optional_param("ignoremissingstrings", false, PARAM_INT);
        if (!$ignoremissingstrings) {
            $this->missingstrings = true;
            echo '<pre style="background: #FFEB3B55;padding: 7px;margin: 8px;">';
            echo "\$string['report_{$refkey}_{$stringlast}'] = '{$value}';";
            echo '</pre>';
        }

        // Return $value if the value is not found in the array.
        return $value;
    }

    /**
     * Var missingstrings1
     *
     * @var array
     */
    private $missingstrings = false;
}
