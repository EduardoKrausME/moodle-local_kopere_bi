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
 * Calendar-aware date range calculation and SQL binding.
 *
 * @package   bifilters_period
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace bifilters_period;

use DateTimeImmutable;
use DateTimeZone;
use moodle_exception;

/**
 * Resolves periods in the user's timezone, using an exclusive upper bound.
 */
final class period_range {
    /** @var array<string,string> Supported presets and translation identifiers. */
    private const PRESETS = [
        "7d" => "last7days",
        "15d" => "last15days",
        "30d" => "last30days",
        "60d" => "last60days",
        "90d" => "last90days",
        "180d" => "last180days",
        "1y" => "last1year",
        "2y" => "last2years",
        "5y" => "last5years",
        "custom" => "custom",
    ];

    /**
     * List the supported relative and manual period presets.
     *
     * @return array<string,string>
     */
    public static function presets(): array {
        return self::PRESETS;
    }

    /**
     * Return the selected period using the current user's timezone.
     *
     * @return array<string,int|string>
     */
    public static function current(): array {
        $timezone = \core_date::get_user_timezone_object();
        return self::calculate(
            optional_param("periodpreset", "30d", PARAM_ALPHANUMEXT),
            optional_param("periodfromdate", "", PARAM_RAW_TRIMMED),
            optional_param("periodtodate", "", PARAM_RAW_TRIMMED),
            new DateTimeImmutable("now", $timezone),
            $timezone
        );
    }

    /**
     * Relative periods include today, and the ending date is inclusive in the UI.
     *
     * @return array<string,int|string>
     */
    public static function calculate(
        string $preset,
        string $from,
        string $to,
        DateTimeImmutable $now,
        DateTimeZone $timezone
    ): array {
        if (!isset(self::PRESETS[$preset])) {
            $preset = "30d";
        }

        $today = $now->setTimezone($timezone)->setTime(0, 0, 0);
        $end = $today->modify("+1 day");
        if ($preset === "custom") {
            $start = self::parse_date($from, $timezone);
            $last = self::parse_date($to, $timezone);
            if ($start > $last) {
                throw new moodle_exception("invalidrange", "bifilters_period");
            }
            $end = $last->modify("+1 day");
        } else {
            $amount = (int)substr($preset, 0, -1);
            $unit = substr($preset, -1) === "y" ? "years" : "days";
            $start = $end->modify("-{$amount} {$unit}");
        }

        return [
            "preset" => $preset,
            "fromdate" => $start->format("Y-m-d"),
            "todate" => $end->modify("-1 day")->format("Y-m-d"),
            "periodfrom" => $start->getTimestamp(),
            "periodto" => $end->getTimestamp(),
        ];
    }

    /**
     * Reject impossible and incorrectly formatted ISO dates.
     */
    private static function parse_date(string $value, DateTimeZone $timezone): DateTimeImmutable {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
            throw new moodle_exception("invaliddate", "bifilters_period");
        }
        $date = DateTimeImmutable::createFromFormat("!Y-m-d", $value, $timezone);
        if (!$date || $date->format("Y-m-d") !== $value) {
            throw new moodle_exception("invaliddate", "bifilters_period");
        }
        return $date;
    }

    /**
     * Resolve only the placeholders actually present in the query.
     */
    public static function bind_sql(string &$sql, array &$params): void {
        if (!preg_match("/:period(from|to)\b/", $sql)) {
            return;
        }
        $range = self::current();
        foreach (["periodfrom", "periodto"] as $key) {
            $pattern = "/:{$key}\b/";
            $count = preg_match_all($pattern, $sql);
            if ($count === 1) {
                $params[$key] = $range[$key];
            } else if ($count > 1) {
                $index = 0;
                $sql = preg_replace_callback($pattern, function () use (&$index, $key) {
                    $index++;
                    return ":count_{$index}_{$key}";
                }, $sql);
                for ($index = 1; $index <= $count; $index++) {
                    $params["count_{$index}_{$key}"] = $range[$key];
                }
            }
        }
    }
}
