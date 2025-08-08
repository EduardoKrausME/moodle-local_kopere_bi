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
use local_kopere_bi\block\i_type;
use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_dashboard\util\json;

/**
 * Class chart_data
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chart_data {

    /**
     * Function load_data
     *
     * @throws Exception
     */
    public function load_data() {
        global $DB;

        $elementid = optional_param("item_id", 0, PARAM_INT);
        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["id" => $elementid]);
        if (!$koperebielement) {
            json::error(get_string("block_not_found", "local_kopere_bi"));
        }

        $koperebielement->info_obj = @json_decode($koperebielement->info, true);

        $class = "\\local_kopere_bi\\block\\{$koperebielement->type}";
        if (class_exists($class)) {
            /** @var i_type $block */
            $block = new $class();
            $block->get_chart_data($koperebielement);
        } else {
            json::error(get_string("class_not_found", "local_kopere_bi"));
        }

        die();
    }
}
