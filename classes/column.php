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
use local_kopere_dashboard\util\json;

/**
 * Class column
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column extends bi_all {

    /**
     * Function by_sql
     *
     * @throws Exception
     */
    public function by_sql() {
        global $DB;

        $sql = optional_param("sql", "", PARAM_TEXT);
        $firstline = $DB->get_record_sql($sql);

        if (!$firstline) {
            json::error(get_string("data_not_found", "local_kopere_bi"));
        }

        $campos = [];
        foreach ($firstline as $id => $value) {
            $type = "string";
            if (is_float($value)) {
                $type = "number-float";
            } else if (is_numeric($value)) {
                $type = "number";
            } else if (strpos($id, "time") !== false) {
                $type = "datetime";
            }

            $label = optional_param("label-{$id}", "", PARAM_TEXT);
            $type = optional_param("type-{$id}", $type, PARAM_TEXT);

            $campos[] = [
                "id" => $id,
                "label" => $label,
                "type" => $type,
            ];
        }

        json::encode($campos);
    }
}
