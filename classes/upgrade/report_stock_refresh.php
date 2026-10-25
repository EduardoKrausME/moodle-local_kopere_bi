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
 * Restore only verified, untouched bundled report definitions.
 *
 * The checksums represent the stock SQL and columns shipped before the
 * four report improvement groups. An institution's changed SQL or column
 * settings always take precedence over packaged defaults.
 *
 * @package local_kopere_bi
 */
final class report_stock_refresh {
    /**
     * Page suffix | immutable element refkey | legacy SQL crc32b | legacy column crc32b.
     *
     * Hashes are used only as exact-match upgrade guards, not for security.
     */
    private const LEGACY = <<<'STOCK'
019|kbi_el_user_profile_information_learne|2a4dd9a8|3a9c6df1
023|kbi_el_user_status|9f0d1a9a|eb9d57a9
029-postgres|kbi_el_instructor_engagement_ratio_pg|31e98b70|23ef6e71
029|kbi_el_instructor_engagement_ratio|9c06a7b3|23ef6e71
035-postgres|kbi_el_course_access_pg|3cb5c59c|bcf70bc1
035|kbi_el_course_access|1c188c2f|bcf70bc1
036|kbi_el_course_access_details|2a99e3c3|4418f2c2
037-postgres|kbi_el_course_activity_overview_pg|36fcaba6|e881a69e
037|kbi_el_course_activity_overview|9edd097d|e881a69e
038|kbi_el_course_content_logs|40cfdb7c|4a13b84f
039|kbi_el_course_content_utilization|41ef0361|b68e904d
040|kbi_el_course_inactivity|b1afd7d1|178cd5c2
041|kbi_el_course_progress|6372489d|440f9b88
042|kbi_el_course_site_summary|ed8e41d8|e5639c7c
043|kbi_el_course_stats|dc948afb|d3f74b7c
044-postgres|kbi_el_course_stats_advanced_pg|80cfc63d|47d35887
044|kbi_el_course_stats_advanced|54fba367|47d35887
052-postgres|kbi_el_pending_grading_items_pg|06f962cb|e1dab4a3
052|kbi_el_pending_grading_items|a4ff0d86|e1dab4a3
053|kbi_el_course_gradebook_by_category|f49fc4bd|0975dcbe
056-postgres|kbi_el_activity_grades_with_dates_pg|98e6dd0b|19189359
056|kbi_el_activity_grades_with_dates|c899f1d9|19189359
058-postgres|kbi_el_activity_status_detail_pg|bc018987|0c0449f4
058|kbi_el_activity_status_detail|0d5cca96|0c0449f4
060-postgres|kbi_el_activity_submission_progress_c_pg|4552d0ea|dc922905
060|kbi_el_activity_submission_progress_c|abfd6ab5|dc922905
063-postgres|kbi_el_quiz_performance_statistics_pg|c4e37ed2|190cd114
063|kbi_el_quiz_performance_statistics|9f06bc60|190cd114
064|kbi_el_quiz_activity_detail_by_learner|6089066e|d2aa80b0
065|kbi_el_quiz_attempts_summary_by_course|1a81fa0f|91edcaba
066|kbi_el_quiz_attempts_summary_by_quiz|68d90aa0|2a4ed470
067-postgres|kbi_el_quiz_grade_detail_by_learner_pg|fcffa945|2a1c4652
067|kbi_el_quiz_grade_detail_by_learner|c1fb083e|2a1c4652
068|kbi_el_quiz_progress_by_cohort|cc41800e|01d31b0d
069|kbi_el_quiz_progress_by_course|db9a4d15|437f37f0
071|kbi_el_forum_activity_details|2089d033|4cebcf25
072|kbi_el_forum_post_ratio|a11de43e|ecd9cd4e
073|kbi_el_forum_post_replies|798a5071|431087fa
074-postgres|kbi_el_forum_posts_performance_pg|3e006567|11884c25
074|kbi_el_forum_posts_performance|c7688406|11884c25
075-postgres|kbi_el_forum_response_overview_pg|5cbfa830|5adc1a28
075|kbi_el_forum_response_overview|5cbfa830|5adc1a28
076|kbi_el_learner_forum_participation_summary|9890ab2d|3034ad37
077-postgres|kbi_el_scorm_activity_detail_pg|051db6c3|c31b5745
077|kbi_el_scorm_activity_detail|1c8ef3f8|c31b5745
078-postgres|kbi_el_scorm_activity_summary_by_course_pg|59fe0753|ce845092
078|kbi_el_scorm_activity_summary_by_course|e86dc09b|ce845092
079|kbi_el_scorm_occurrence_s_by_course|3cb2665f|32e86157
080|kbi_el_assignment_submission_status|fac99ac9|e9ff7b6d
083|kbi_el_feedback_overview_report|40a503ee|0e0f156c
087|kbi_el_learning_plan_progress_details|9c161912|ad961d7c
088-postgres|kbi_el_quiz_answer_details_pg|f3260c28|bf4170d5
088|kbi_el_quiz_answer_details|f3b66149|bf4170d5
089-postgres|kbi_el_quiz_score_details_pg|a3898665|2cbb1a83
089|kbi_el_quiz_score_details|f0235bf4|2cbb1a83
097-postgres|kbi_el_moodle_message_utilization_pg|a26b8a7b|4b64f36a
097|kbi_el_moodle_message_utilization|bc99e396|4b64f36a
112|kbi_el_section_engagement|4f755c01|f5abb0eb
114|kbi_el_time_by_activity_type|3298aa2c|698c3a38
115|kbi_el_daily_role_activity|6e26dd21|d22fdec0
116|kbi_el_active_courses_daily|3ba1a703|0e131412
118|kbi_el_category_completions_weekly|673341e8|f5af009e
119|kbi_el_learner_vs_class_activity|5e631c8f|8842ddb6
120|kbi_el_quiz_pre_post_comparison|e5ac0255|bf818533
123|kbi_el_question_tag_performance|9a60a308|9dc5a191
STOCK;

    /**
     * Return stock signatures keyed by report refkey.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function stock_signatures(): array {
        $signatures = [];
        foreach (explode("\n", trim(self::LEGACY)) as $row) {
            [$suffix, $refkey, $sqlhash, $columnhash] = explode("|", trim($row));
            $signatures[$refkey] = [$suffix, $sqlhash, $columnhash];
        }
        return $signatures;
    }

    /**
     * Refresh previous bundled definitions without touching any local customisation.
     *
     * @param bool $dryrun Compute results without database writes.
     * @return array<string, int>
     */
    public static function refresh(bool $dryrun = false): array {
        global $DB;

        $family = $DB->get_dbfamily();
        if (!in_array($family, ["mysql", "postgres"], true)) {
            throw new \coding_exception("Unsupported database family for report refresh.");
        }

        $counts = ["updated" => 0, "current" => 0, "preserved" => 0, "missing" => 0];
        foreach (self::stock_signatures() as $refkey => [$suffix, $oldsqlhash, $oldcolumnhash]) {
            if (str_ends_with($suffix, "-postgres") && $family !== "postgres") {
                continue;
            }

            $filename = __DIR__ . "/../../db/files/page-{$suffix}.json";
            $page = json_decode(file_get_contents($filename), true, 512, JSON_THROW_ON_ERROR);
            if (($page["pre_requisit"] ?? "") === "mysql" && $family !== "mysql") {
                continue;
            }
            if (($page["pre_requisit"] ?? "") === "postgres" && $family !== "postgres") {
                continue;
            }

            $element = null;
            foreach ($page["blocks"] as $block) {
                foreach ($block["elements"] as $candidate) {
                    if ($candidate["refkey"] === $refkey) {
                        $element = $candidate;
                        break 2;
                    }
                }
            }
            if ($element === null) {
                throw new \coding_exception("Report definition missing: {$refkey}");
            }

            $newsql = $element["commandsql"];
            $newinfo = $element["info"];
            $newcolumnhash = self::columns_hash($newinfo["column"]);
            foreach ($DB->get_records("local_kopere_bi_element", ["refkey" => $refkey],
                    "", "id, commandsql, info") as $record) {
                $oldsql = (string)$record->commandsql;
                $oldinfo = json_decode((string)$record->info, true);
                if (!is_array($oldinfo) || !isset($oldinfo["column"]) ||
                        !is_array($oldinfo["column"])) {
                    $counts["preserved"]++;
                    continue;
                }
                $columnhash = self::columns_hash($oldinfo["column"]);
                $islegacysql = hash("crc32b", $oldsql) === $oldsqlhash;
                $iscurrent = $oldsql === $newsql;
                $isdefaultcolumns = $columnhash === $oldcolumnhash ||
                    $columnhash === $newcolumnhash;

                // Any difference from recognised shipped SQL or column layouts
                // is treated as a deliberate institutional customisation.
                if ((!$islegacysql && !$iscurrent) || !$isdefaultcolumns) {
                    $counts["preserved"]++;
                    continue;
                }
                if ($iscurrent && $columnhash === $newcolumnhash) {
                    $counts["current"]++;
                    continue;
                }

                // Keep non-column metadata supplied by the institution.
                $oldinfo["column"] = $newinfo["column"];
                if (!$dryrun) {
                    $DB->update_record("local_kopere_bi_element", (object)[
                        "id" => $record->id,
                        "commandsql" => $newsql,
                        "info" => json_encode($oldinfo, JSON_UNESCAPED_UNICODE |
                            JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                    ]);
                }
                $counts["updated"]++;
            }
        }
        return $counts;
    }

    /**
     * Normalise decoded column settings before checking the shipped signature.
     *
     * @param array $columns Parsed report column definitions.
     * @return string
     */
    private static function columns_hash(array $columns): string {
        return hash("crc32b", json_encode($columns, JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }
}
