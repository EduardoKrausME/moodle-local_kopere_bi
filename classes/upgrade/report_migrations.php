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

    /**
     * Apply fixes to untouched built-in SQL definitions, preserving local changes.
     *
     * Changing bundled JSON alone does not update reports already saved in the
     * database. Checksums restrict this migration to the exact old definitions.
     *
     * @return void
     */
    public static function fix_broken_builtin_reports(): void {
        global $DB;

        $originals = [
            "report_8_teacher_access" => [7, "b9e00f36"],
            "kbi_el_inactive_users_in_course_s" => [11, "f397b33f"],
            "kbi_el_learner_success_and_progress" => [13, "bc56e0a0"],
            "kbi_el_user_role_modification_learner_roles" => [20, "42d205be"],
            "kbi_el_user_site_summary_detail" => [21, "edec1305"],
            "kbi_el_user_site_use_summary" => [22, "301054e5"],
            "kbi_el_assignments_graded_by_instructor" => [25, "4472b14d"],
            "kbi_el_instructor_assignment_engagement" => [28, "6d8615d3"],
            "kbi_el_instructor_forum_engagement" => [31, "94d08f4e"],
            "kbi_el_instructor_load_summary" => [32, "e94faa3b"],
            "kbi_el_instructor_site_use_summary" => [33, "ed781a23"],
            "kbi_el_course_access" => [35, "9f32d19e"],
            "kbi_el_course_activity_overview" => [37, "ecf0840f"],
            "kbi_el_course_inactivity" => [40, "e65b04f4"],
            "kbi_el_course_progress" => [41, "201982b0"],
            "kbi_el_course_site_summary" => [42, "0dd07f6d"],
            "kbi_el_course_stats_advanced" => [44, "70e62852"],
            "kbi_el_enrollment_method_details" => [50, "f9fcb4e3"],
            "kbi_el_course_gradebook_by_category" => [53, "27f587c7"],
            "kbi_el_activity_status_detail_simple" => [59, "df326d78"],
            "kbi_el_activity_submission_progress_c" => [60, "0b4315f5"],
            "kbi_el_quiz_activity_detail_by_learner" => [64, "3048fe22"],
            "kbi_el_scorm_activity_detail" => [77, "e02a2c3d"],
            "kbi_el_scorm_activity_summary_by_course" => [78, "8ba4c063"],
            "kbi_el_assignment_submission_status" => [80, "a4fa66dc"],
            // Preserve compatibility with the original and the intermediate REGEXP-based definition.
            "kbi_el_feedback_overview_report" => [83, ["dac9c2ab", "bd67f41b"]],
        ];

        foreach ($originals as $refkey => [$pageid, $checksum]) {
            $path = dirname(__DIR__, 2) . "/db/files/page-" . sprintf("%03d", $pageid) . ".json";
            $definition = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $fixedsql = null;

            foreach ($definition["blocks"] as $block) {
                foreach ($block["elements"] as $element) {
                    if (($element["refkey"] ?? "") === $refkey) {
                        $fixedsql = (string)$element["commandsql"];
                        break 2;
                    }
                }
            }

            if ($fixedsql === null) {
                throw new \coding_exception("Missing built-in Kopere BI SQL: " . $refkey);
            }

            $records = $DB->get_records("local_kopere_bi_element", ["refkey" => $refkey], "", "id, commandsql");
            foreach ($records as $record) {
                $previoussql = (string)$record->commandsql;
                if ($previoussql === $fixedsql) {
                    continue;
                }

                // Never replace a locally customised report definition.
                if (!in_array(hash("crc32b", $previoussql), (array)$checksum, true)) {
                    debugging("Preserving modified Kopere BI SQL for " . $refkey, DEBUG_DEVELOPER);
                    continue;
                }

                $DB->set_field(
                    "local_kopere_bi_element",
                    "commandsql",
                    $fixedsql,
                    ["id" => (int)$record->id]
                );
            }
        }
    }
}
