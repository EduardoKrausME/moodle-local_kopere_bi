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
 * Targeted migrations for saved Kopere BI report definitions.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\upgrade;

/**
 * Repair built-in reports without resetting locally customised data or metadata.
 */
final class report_migrations {
    /**
     * Fix the question status reference in the saved quiz structure report.
     *
     * The status column belongs to question_versions, not question. Only the
     * invalid reference is changed, leaving all other SQL and settings intact.
     *
     * @return void
     */
    public static function fix_quiz_question_status(): void {
        global $DB;

        $elements = $DB->get_records(
            "local_kopere_bi_element",
            ["refkey" => "kbi_el_quiz_question_structure"],
            "",
            "id, commandsql"
        );
        foreach ($elements as $element) {
            $oldsql = (string)$element->commandsql;
            $newsql = preg_replace(
                "/\\bkbi_q\\.status(?=\\s+AS\\s+question_status\\b)/i",
                "kbi_qv.status",
                $oldsql
            );
            if ($newsql !== null && $newsql !== $oldsql) {
                $DB->set_field(
                    "local_kopere_bi_element",
                    "commandsql",
                    $newsql,
                    ["id" => (int)$element->id]
                );
            }
        }
    }
}
