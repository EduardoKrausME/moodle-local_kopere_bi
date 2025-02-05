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

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Class categorie
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categorie extends external_api {

    /**
     * Function sortorder_parameters
     *
     * @return external_function_parameters
     */
    public static function sortorder_parameters() {
        return new external_function_parameters([
            "itens" => new external_value(PARAM_TEXT, "New page sortorder"),
        ]);
    }

    /**
     * Function sortorder_is_allowed_from_ajax
     *
     * @return bool
     */
    public static function sortorder_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Function sortorder_returns
     *
     * @return external_single_structure
     */
    public static function sortorder_returns() {
        return new external_single_structure([
            "status" => new external_value(PARAM_TEXT, "Status", VALUE_OPTIONAL),
        ]);
    }

    /**
     * Function sortorder
     *
     * @param $elements
     *
     * @return array
     * @throws \Exception
     */
    public static function sortorder($elements) {
        global $DB;

        $params = self::validate_parameters(self::sortorder_parameters(), [
            "itens" => $elements,
        ]);

        $context = \context_system::instance();
        require_capability("local/kopere_bi:manage", $context);
        self::validate_context($context);

        $elements = explode(",", $params["itens"]);
        $sortorder = 0;
        foreach ($elements as $element) {
            if ($element) {
                $koperebicat = (object)[
                    "id" => $element,
                    "sortorder" => ++$sortorder,
                ];
                $DB->update_record("local_kopere_bi_cat", $koperebicat);
            }
        }

        return [
            "status" => "OK",
        ];
    }
}
