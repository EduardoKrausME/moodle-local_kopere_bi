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
 * Refresh shipped analytics SQL without overwriting locally customised definitions.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\upgrade;

use local_kopere_bi\install\reports;

/**
 * Repairs the six existing report concepts without changing their reference keys.
 */
final class analytics_report_refresh {
    /** @var array<string, array{int, string}> Previous shipped SQL checksums. */
    private const PREVIOUS_SQL = [
        "kbi_el_grade_change_history" => [121, "4c41f53a"],
        "kbi_el_common_incorrect_answers" => [122, "a6fd51b5"],
        "kbi_el_grading_criteria_performance" => [124, "752aec12"],
        "kbi_el_feedback_answer_distribution" => [125, "b2d16681"],
        "kbi_el_forum_posting_times" => [126, "8eec130b"],
        "kbi_el_pending_grading_items" => [52, "dbc18d99"],
        "kbi_el_pending_grading_items_pg" => [52, "22329994"],
    ];

    /**
     * Update only stock SQL, merge additional columns and import missing bundled pages.
     *
     * This explicit command can be used between version bumps; later releases can
     * invoke it from the standard plugin upgrade. Admin edits remain untouched.
     *
     * @param bool $dryrun Whether to inspect only.
     * @return array<string, int> Number of changed, already-current and preserved elements.
     */
    public static function refresh(bool $dryrun = false): array {
        global $DB;

        $family = $DB->get_dbfamily();
        if (!in_array($family, ["mysql", "postgres"], true)) {
            throw new \coding_exception("Unsupported database family for built-in analytics reports.");
        }

        $summary = ["updated" => 0, "current" => 0, "preserved" => 0, "missing" => 0];
        foreach (self::PREVIOUS_SQL as $refkey => [$number, $checksum]) {
            if ($refkey === "kbi_el_pending_grading_items" && $family !== "mysql") {
                continue;
            }
            if ($refkey === "kbi_el_pending_grading_items_pg" && $family !== "postgres") {
                continue;
            }

            $suffix = $refkey === "kbi_el_pending_grading_items_pg" ? "-postgres" : "";
            $path = dirname(__DIR__, 2) . "/db/files/page-" . sprintf("%03d", $number) . $suffix . ".json";
            $page = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $definition = null;
            foreach ($page["blocks"] as $block) {
                foreach ($block["elements"] as $element) {
                    if ($element["refkey"] === $refkey) {
                        $definition = $element;
                        break 2;
                    }
                }
            }
            if ($definition === null) {
                throw new \coding_exception("Missing report definition: " . $refkey);
            }

            // Install pages missing on older sites; do not reset an existing report.
            if (!$dryrun) {
                reports::from_file($path, true);
            }

            $installed = $DB->get_records("local_kopere_bi_element",
                ["refkey" => $refkey], "", "id, commandsql, info");
            if (!$installed) {
                $summary["missing"]++;
                continue;
            }

            foreach ($installed as $record) {
                if ((string)$record->commandsql === $definition["commandsql"]) {
                    $summary["current"]++;
                    continue;
                }
                if (hash("crc32b", (string)$record->commandsql) !== $checksum) {
                    $summary["preserved"]++;
                    continue;
                }

                $columns = json_decode((string)$record->info, true);
                if (!is_array($columns)) {
                    $columns = [];
                }
                // Preserve customised column titles, formatting and layout.
                foreach ($definition["info"]["column"] as $key => $column) {
                    if (!isset($columns["column"][$key])) {
                        $columns["column"][$key] = $column;
                    }
                }
                if (!$dryrun) {
                    $DB->update_record("local_kopere_bi_element", (object)[
                        "id" => $record->id,
                        "commandsql" => $definition["commandsql"],
                        "info" => json_encode($columns, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                    ]);
                }
                $summary["updated"]++;
            }
        }
        return $summary;
    }
}
