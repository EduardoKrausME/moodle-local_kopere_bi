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
 * List cohorts available to the Kopere BI cohort filter.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use context_course;
use context_system;
use local_kopere_dashboard\util\json;
use stdClass;

/**
 * Cohort discovery and access checks for BI report filters.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohorts {
    /**
     * Check if the current user may select a cohort from a report.
     *
     * @param int $cohortid Cohort ID.
     * @return bool
     */
    public static function can_view_cohort(int $cohortid): bool {
        global $DB;

        if ($cohortid <= 0) {
            return false;
        }

        $cohort = $DB->get_record("cohort", ["id" => $cohortid]);
        return $cohort && self::can_view($cohort);
    }

    /**
     * Resolve a course scope only when the user can actually access that course.
     *
     * @return \context
     */
    private static function filter_context(): \context {
        global $DB, $USER;

        $courseid = optional_param("courseid", (int)($USER->filter_courseid ?? SITEID), PARAM_INT);
        if ($courseid <= SITEID || !$DB->record_exists("course", ["id" => $courseid])) {
            return context_system::instance();
        }

        $coursecontext = context_course::instance($courseid, IGNORE_MISSING);
        if (!$coursecontext) {
            return context_system::instance();
        }

        if (has_capability("moodle/course:view", $coursecontext) ||
                is_enrolled($coursecontext, $USER->id)) {
            return $coursecontext;
        }

        return context_system::instance();
    }

    /**
     * Respect Moodle's cohort visibility and context rules.
     *
     * BI managers may use all cohorts in the reports they manage.
     *
     * @param stdClass $cohort Moodle cohort record.
     * @return bool
     */
    private static function can_view(stdClass $cohort): bool {
        global $CFG;

        if (has_capability("local/kopere_bi:manage", context_system::instance())) {
            return true;
        }

        require_once($CFG->dirroot . "/cohort/lib.php");
        return cohort_can_view_cohort($cohort, self::filter_context());
    }

    /**
     * Build the visible cohort options without exposing hidden cohort names.
     *
     * The zero-ID option clears the selection. Reports decide how to handle
     * zero in their SQL (for example, optional versus required cohort filters).
     *
     * @return array
     */
    public static function get_selectable_cohorts(): array {
        global $DB;

        $data = [
            ["id" => 0, "name" => get_string("clearcohort", "bifilters_cohort")],
        ];

        $cohorts = $DB->get_records("cohort", [], "name ASC, id ASC", "id, name, contextid, visible");
        foreach ($cohorts as $cohort) {
            if (!self::can_view($cohort)) {
                continue;
            }

            $data[] = [
                "id" => (int)$cohort->id,
                // The shared DataTables picker treats this value as HTML.
                "name" => s($cohort->name),
            ];
        }

        return $data;
    }

    /**
     * Return the permitted cohorts in DataTables' expected JSON format.
     *
     * @return void
     */
    public function load_all_cohorts(): void {
        require_capability("local/kopere_bi:view", context_system::instance());
        json::encode(self::get_selectable_cohorts());
    }
}
