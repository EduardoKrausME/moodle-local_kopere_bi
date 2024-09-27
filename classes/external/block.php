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

namespace local_kopere_bi\external;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use local_kopere_bi\util\details_util;
use local_kopere_bi\util\sql_util;
use local_kopere_bi\vo\local_kopere_bi_block;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Class block
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block extends external_api {

    /**
     * Function sequence_parameters
     *
     * @return external_function_parameters
     */
    public static function sequence_parameters() {
        return new external_function_parameters([
            "itens" => new external_value(PARAM_TEXT, "New page sequence"),
        ]);
    }

    /**
     * Function sequence_is_allowed_from_ajax
     *
     * @return bool
     */
    public static function sequence_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Function sequence_returns
     *
     * @return external_single_structure
     */
    public static function sequence_returns() {
        return new external_single_structure([
            "status" => new external_value(PARAM_TEXT, "Status", VALUE_OPTIONAL),
        ]);
    }

    /**
     * Function sequence
     *
     * @param $elements
     *
     * @return array
     * @throws \Exception
     */
    public static function sequence($elements) {
        global $DB;

        $elements = explode(",", $elements);
        $sequence = 0;
        foreach ($elements as $element) {
            if ($element) {
                $koperebiblock = (object)[
                    "id" => $element,
                    "sequence" => ++$sequence,
                ];
                $DB->update_record("local_kopere_bi_block", $koperebiblock);
            }
        }

        return [
            "status" => "OK",
        ];
    }

    /**
     * Function delete_parameters
     *
     * @return external_function_parameters
     */
    public static function delete_parameters() {
        return new external_function_parameters([
            "block_id" => new external_value(PARAM_TEXT, "Block ID"),
        ]);
    }

    /**
     * Function delete_is_allowed_from_ajax
     *
     * @return bool
     */
    public static function delete_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Function delete_returns
     *
     * @return external_single_structure
     */
    public static function delete_returns() {
        return new external_single_structure([
            "status" => new external_value(PARAM_TEXT, "Delete a Block", VALUE_OPTIONAL),
        ]);
    }

    /**
     * Function delete
     *
     * @param $blockid
     *
     * @return array
     * @throws \Exception
     */
    public static function delete($blockid) {
        global $DB;

        /** @var local_kopere_bi_block $koperebiblock */
        $koperebiblock = $DB->get_record("local_kopere_bi_block", ["id" => $blockid]);
        if (!$koperebiblock) {
            return [
                "status" => "ERRO",
            ];
        }

        $DB->delete_records("local_kopere_bi_block", ["id" => $blockid]);
        $DB->delete_records("local_kopere_bi_element", ["block_id" => $blockid]);

        return [
            "status" => "OK",
        ];
    }

    /**
     * Function add_parameters
     *
     * @return external_function_parameters
     */
    public static function add_parameters() {
        return new external_function_parameters([
            "page_id" => new external_value(PARAM_TEXT, "ID of the page that will receive the new Block"),
            "type" => new external_value(PARAM_TEXT, "Block Type"),
        ]);
    }

    /**
     * Function add_is_allowed_from_ajax
     *
     * @return bool
     */
    public static function add_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Function add_returns
     *
     * @return external_single_structure
     */
    public static function add_returns() {
        return new external_single_structure([
            "status" => new external_value(PARAM_TEXT, "Status", VALUE_OPTIONAL),
            "html" => new external_value(PARAM_RAW, "HTML of the Block page", VALUE_OPTIONAL),
        ]);
    }

    /**
     * Function add
     *
     * @param $pageid
     * @param $type
     *
     * @return array
     * @throws \Exception
     */
    public static function add($pageid, $type) {
        global $DB;

        $koperebiblock = (object)[
            "page_id" => $pageid,
            "type" => $type,
            "sequence" => time(),
            "time" => time(),
        ];
        $koperebiblock->id = $DB->insert_record("local_kopere_bi_block", $koperebiblock);

        return [
            "status" => "OK",
            "html" => (new details_util())->html_details_block($koperebiblock),
        ];
    }
}
