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
 * Period filter provider for Kopere BI reports.
 *
 * @package   bifilters_period
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace bifilters_period;

use local_kopere_bi\filters\i_filter_provider;

/**
 * Build the period form and resolve the corresponding SQL parameters.
 */
class provider implements i_filter_provider {
    /**
     * @param array $paramsurl Navigation parameters.
     * @param object $comand Prepared SQL and bound values.
     * @return string HTML rendered by Moodle Mustache.
     */
    public static function filter(&$paramsurl, $comand) {
        $found = false;
        foreach (array_keys($comand->params) as $key) {
            if (preg_match("/^(?:count_\d+_)?period(?:from|to)$/", $key)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            return "";
        }

        global $PAGE, $OUTPUT;
        $timezone = \core_date::get_user_timezone_object();
        $now = new \DateTimeImmutable("now", $timezone);
        $selected = period_range::current();
        $options = [];
        foreach (period_range::presets() as $value => $key) {
            $range = $value === "custom" ? $selected :
                period_range::calculate($value, "", "", $now, $timezone);
            $options[] = [
                "value" => $value,
                "label" => get_string($key, "bifilters_period"),
                "selected" => $selected["preset"] === $value,
                "fromdate" => $range["fromdate"],
                "todate" => $range["todate"],
            ];
        }

        unset($paramsurl["periodpreset"], $paramsurl["periodfromdate"], $paramsurl["periodtodate"]);
        $paramsurl["classname"] = optional_param("classname", "dashboard", PARAM_ALPHANUMEXT);
        $paramsurl["method"] = optional_param("method", "preview", PARAM_ALPHANUMEXT);
        $hidden = [];
        foreach ($paramsurl as $name => $value) {
            if ($value !== false && $value !== null && $value !== "") {
                $hidden[] = ["name" => $name, "value" => $value];
            }
        }

        $id = uniqid("bifilters-period-");
        $PAGE->requires->js_call_amd("bifilters_period/filter", "init", [$id]);
        return $OUTPUT->render_from_template("bifilters_period/filter", [
            "id" => $id,
            "action" => (new \moodle_url("/local/kopere_bi/index.php"))->out(false),
            "options" => $options,
            "fromdate" => $selected["fromdate"],
            "todate" => $selected["todate"],
            "custom" => $selected["preset"] === "custom",
            "hiddenparams" => $hidden,
            "labelpreset" => get_string("preset", "bifilters_period"),
            "labelfrom" => get_string("datefrom", "bifilters_period"),
            "labelto" => get_string("dateto", "bifilters_period"),
            "labelapply" => get_string("apply", "bifilters_period"),
        ]);
    }

    /**
     * Backwards-compatible primary placeholder.
     */
    public static function get_key() {
        return "periodfrom";
    }

    /**
     * @return string[]
     */
    public static function get_keys(): array {
        return ["periodfrom", "periodto"];
    }

    /**
     * Additional SQL resolver hook called by the parent plugin.
     */
    public static function bind_sql(string &$sql, array &$params): void {
        period_range::bind_sql($sql, $params);
    }
}
