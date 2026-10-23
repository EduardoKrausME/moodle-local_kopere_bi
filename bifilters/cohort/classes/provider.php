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
 * Cohort selector provider for Kopere BI.
 *
 * @package   bifilters_cohort
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace bifilters_cohort;

use local_kopere_bi\filters\i_filter_provider;

/**
 * Filter reports by a Moodle cohort.
 *
 * @package   bifilters_cohort
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements i_filter_provider {
    /**
     * Render a cohort picker for reports containing :cohortid.
     *
     * @param array $paramsurl URL parameters for the report page.
     * @param object $comand Prepared SQL and its parameters.
     * @return string
     */
    public static function filter(&$paramsurl, $comand) {
        global $DB, $OUTPUT, $PAGE;

        $cohortid = $comand->params["cohortid"] ?? $comand->params["count_1_cohortid"] ?? null;
        if ($cohortid === null) {
            return "";
        }

        $cohort = (int)$cohortid > 0 ? $DB->get_record("cohort", ["id" => (int)$cohortid]) : false;
        $name = $cohort ? format_string($cohort->name) : get_string("selectcohort", "bifilters_cohort");

        $urlparams = $paramsurl;
        $urlparams["cohortid"] = "{id}";
        foreach (["courseid", "userid"] as $key) {
            $value = $comand->params[$key] ?? $comand->params["count_1_" . $key] ?? null;
            if ($value !== null) {
                $urlparams[$key] = (int)$value;
            }
        }

        // Carry the course scope into the AJAX request for category-level cohorts.
        $courseid = $comand->params["courseid"] ?? $comand->params["count_1_courseid"]
            ?? optional_param("courseid", 0, PARAM_INT);
        $courseid = (int)$courseid;
        if ($courseid > SITEID) {
            $urlparams["courseid"] = $courseid;
        }

        $classname = optional_param("classname", "", PARAM_ALPHANUMEXT);
        $method = optional_param("method", "", PARAM_ALPHANUMEXT);

        $data = [
            "popupid" => uniqid("kopere-bi-cohort-"),
            "btn-title" => get_string("pluginname", "bifilters_cohort"),
            "btn-subtitle" => $name,
            "cols" => [
                ["id" => "id", "name" => "#", "style" => "width:60px"],
                ["id" => "name", "name" => get_string("cohortname", "bifilters_cohort")],
            ],
            "columns" => [
                (object)["data" => "id"],
                (object)["data" => "name"],
            ],
            "table-title" => get_string("selectcohort", "bifilters_cohort"),
            "url-ajax" => "view-ajax.php?classname=cohorts&method=load_all_cohorts"
                . ($courseid > SITEID ? "&courseid={$courseid}" : ""),
            "url-click" => "?classname=" . rawurlencode($classname) . "&method=" . rawurlencode($method)
                . "&" . http_build_query($urlparams, "", "&"),
        ];

        $PAGE->requires->js_call_amd("local_kopere_bi/filter", "init", [
            $data["popupid"], $data["columns"],
        ]);
        return $OUTPUT->render_from_template("local_kopere_bi/filter", $data);
    }

    /**
     * Parameter key advertised in the report SQL editor.
     *
     * @return string
     */
    public static function get_key() {
        return "cohortid";
    }
}
