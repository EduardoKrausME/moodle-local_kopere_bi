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

use local_kopere_bi\local\vo\local_kopere_bi_block;
use local_kopere_bi\local\vo\local_kopere_bi_cat;
use local_kopere_bi\local\vo\local_kopere_bi_element;
use local_kopere_bi\local\vo\local_kopere_bi_page;
use local_kopere_dashboard\util\header;

/**
 * Class data_export
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_export {
    /**
     * Function json
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function json() {
        global $DB;

        $string = [];
        if (file_exists(__DIR__ . "/../lang/pt_br/local_kopere_bi.php")) {
            require_once(__DIR__ . "/../lang/pt_br/local_kopere_bi.php");
        } else {
            require_once(__DIR__ . "/../lang/en/local_kopere_bi.php");
        }
        $stringbi = $string;

        $string = [];
        if (file_exists(__DIR__ . "/../../../lang/pt_br/moodle.php")) {
            require_once(__DIR__ . "/../../../lang/pt_br/moodle.php");
        }
        $stringmoodle = $string;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($koperebipage, get_string("page_not_found"));

        /** @var local_kopere_bi_cat $koperebicat */
        $koperebicat = $DB->get_record("local_kopere_bi_cat", ["id" => $koperebipage->cat_id]);
        header::notfound_null($koperebicat, get_string("cat_not_found"));

        $data = (object)[
            "title" => $this->get_key_by_value($stringbi, $koperebipage->title),
            "description" => $this->get_key_by_value($stringbi, $koperebipage->description),
            "user_id" => $koperebipage->user_id,
            "category" => (object)[
                "title" => $this->get_key_by_value($stringbi, $koperebicat->title),
                "description" => $this->get_key_by_value($stringbi, $koperebicat->description),
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

                $infos = json_decode($koperebielement->info);
                if (isset($infos->column->name)) {
                    $infos->column->name = (array)$infos->column->name;
                    $infos->column->type = (array)$infos->column->type;

                    foreach ($infos->column->name as $key => $string) {

                        if ($infos->column->type[$key] == "none") {
                            continue;
                        }

                        if ($key == "firstname") {
                            $string = "lang::u_fullname::local_kopere_bi";
                        } else if (isset($stringmoodle[$key])) {
                            $string = "lang::{$key}::moodle";
                        } else {
                            $string = $this->get_key_by_value($stringbi, $string, $key);
                        }

                        $infos->column->name[$key] = $string;
                    }

                    $koperebielement->info = json_encode($infos, JSON_PRETTY_PRINT);
                }

                $koperebielement->title = $this->get_key_by_value($stringbi, $koperebielement->title);

                $elements[] = $koperebielement;
            }

            unset($koperebiblock->id);
            unset($koperebiblock->page_id);
            unset($koperebiblock->time);

            $koperebiblock->elements = $elements;

            $data->blocks[] = $koperebiblock;
        }

        if ($this->missingstrings1) {
            $data->missingstrings1 = get_string("missingstrings1", "local_kopere_bi", implode("\n", $this->missingstrings1));
        }
        if ($this->missingstrings2) {
            $data->missingstrings2 = get_string("missingstrings2", "local_kopere_bi", implode("\n", $this->missingstrings2));
        }

        ob_clean();
        header("Content-Type: application/json; charset: utf-8");
        header("Content-Description: File Transfer");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header("Content-Disposition: attachment; filename=\"page-{$pageid}.json\"");
        die(json_encode($data, JSON_NUMERIC_CHECK + JSON_PRETTY_PRINT) . "\n");
    }

    /**
     * Function to find the key based on a value in an array
     *
     * @param array $string
     * @param string $value
     * @param string $stringkey
     *
     * @return string
     * @throws \coding_exception
     */
    private function get_key_by_value($string, $value, $stringkey = "") {
        $pageid = optional_param("page_id", 0, PARAM_INT);

        if (strpos($value, "::") || $value == "#" || $value == "") {
            return $value;
        }

        if ($key = array_search($value, $string)) {
            // Return the key if the value is found.
            return "lang::{$key}::local_kopere_bi";
        } else {
            if (!$stringkey) {
                $this->missingstrings1[] = "\$string['report_{$pageid}_'] = '{$value}';";
            } else {
                $this->missingstrings2[] = "\$string['{$value}'] = '';";
            }

            // Return $value if the value is not found in the array.
            return $value;
        }
    }

    /**
     * Var missingstrings1
     *
     * @var array
     */
    private $missingstrings1 = [];
    /**
     * Var missingstrings2
     *
     * @var array
     */
    private $missingstrings2 = [];
}
