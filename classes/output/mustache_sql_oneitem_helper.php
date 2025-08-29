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
 * Class mustache_sql_oneitem_helper
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\output;

use Exception;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_dashboard\util\message;
use Mustache_LambdaHelper;

/**
 * Class mustache_sql_oneitem_helper
 */
class mustache_sql_oneitem_helper {
    /**
     * Function transform
     *
     * @param $sql
     * @param Mustache_LambdaHelper $helper
     * @return string
     * @throws Exception
     */
    public function transform($sql, Mustache_LambdaHelper $helper) {
        $comand = sql_util::prepare_sql($sql);

        try {
            $line = (new database_util())->get_record_sql_block($comand->sql, $comand->params);
        } catch (Exception $e) {
            if (AJAX_SCRIPT) {
                echo json_encode([
                    "sql" => $comand->sql,
                    "error" => $e->getMessage(),
                    "trace" => $e->getTraceAsString(),
                ]);
                die;
            } else {
                return message::danger(get_string("info_error_sql", "local_kopere_bi"));
            }
        }

        foreach ($line as $column) {
            if (is_float($column)) {
                $column = "{$column}";
            } else if (is_number($column)) {
                $column = number_format($column, 0, "", ".");
            }
            return $column;
        }

        return "";
    }
}
