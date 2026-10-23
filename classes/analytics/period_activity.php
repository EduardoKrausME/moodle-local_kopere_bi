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
 * Persist course activity in site-timezone day and period buckets.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\analytics;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Records page views and tracked time without losing their time-of-day dimension.
 */
final class period_activity {
    /**
     * Record one page visit at the time the page is rendered.
     *
     * @param int $userid
     * @param int $courseid
     * @param int $timestamp
     */
    public static function record_visit(int $userid, int $courseid, int $timestamp): void {
        if ($userid <= 0 || $courseid <= 1) {
            return;
        }

        foreach (self::split_seconds($timestamp + 1, 1) as $bucket) {
            self::increment($userid, $courseid, $bucket, 1);
        }
    }

    /**
     * Record time sent by the existing focused-tab heartbeat.
     *
     * Each heartbeat normally contains 30 seconds. The server only knows the
     * cumulative focused time, not the exact focus timeline within an interval;
     * assign it to the most recent seconds, splitting crossing boundaries.
     *
     * @param int $userid
     * @param int $courseid
     * @param int $timestamp
     * @param int $seconds
     */
    public static function record_seconds(int $userid, int $courseid, int $timestamp, int $seconds): void {
        if ($userid <= 0 || $courseid <= 1 || $seconds <= 0) {
            return;
        }

        foreach (self::split_seconds($timestamp, $seconds) as $bucket) {
            self::increment($userid, $courseid, $bucket, 0);
        }
    }

    /**
     * Split tracked seconds into site-local reporting periods, including days.
     * This pure method is also used to exercise boundary and DST cases in tests.
     *
     * @param int $timestamp End timestamp (exclusive).
     * @param int $seconds Focused seconds, capped for malformed requests.
     * @param DateTimeZone|null $timezone Explicit timezone for tests.
     * @return array
     */
    public static function split_seconds(int $timestamp, int $seconds, ?DateTimeZone $timezone = null): array {
        global $CFG;

        $seconds = min(7200, max(0, $seconds));
        if ($timestamp <= 0 || $seconds === 0) {
            return [];
        }

        $timezone = $timezone ?? \core_date::get_user_timezone_object($CFG->timezone);
        $cursor = max(0, $timestamp - $seconds);
        $buckets = [];

        while ($cursor < $timestamp) {
            $local = (new DateTimeImmutable("@" . $cursor))->setTimezone($timezone);
            $hour = (int)$local->format("G");
            $weekday = (int)$local->format("N") - 1;
            $daystart = $local->setTime(0, 0)->getTimestamp();

            if ($hour < 6) {
                $slot = 1;
                $nexthour = 6;
            } else if ($hour < 9) {
                $slot = 2;
                $nexthour = 9;
            } else if ($hour < 12) {
                $slot = 3;
                $nexthour = 12;
            } else if ($hour < 15) {
                $slot = 4;
                $nexthour = 15;
            } else if ($hour < 18) {
                $slot = 5;
                $nexthour = 18;
            } else if ($hour < 21) {
                $slot = 6;
                $nexthour = 21;
            } else {
                $slot = 7;
                $nexthour = 24;
            }

            $next = $nexthour === 24 ? $local->modify("tomorrow")->setTime(0, 0) :
                $local->setTime($nexthour, 0);
            // Avoid stalling around ambiguous daylight-saving transitions.
            $stop = min($timestamp, max($cursor + 1, $next->getTimestamp()));
            $key = $daystart . ":" . $slot;
            if (!isset($buckets[$key])) {
                $buckets[$key] = [
                    "daystart" => $daystart,
                    "weekdaynum" => $weekday,
                    "slot" => $slot,
                    "visits" => 0,
                    "timespend" => 0,
                ];
            }
            $buckets[$key]["timespend"] += $stop - $cursor;
            $cursor = $stop;
        }

        return array_values($buckets);
    }

    /**
     * Atomically accumulate one bucket, avoiding lost updates during concurrent requests.
     *
     * @param int $userid
     * @param int $courseid
     * @param array $bucket
     * @param int $visits
     */
    private static function increment(int $userid, int $courseid, array $bucket, int $visits): void {
        global $DB;

        $params = [
            "userid" => $userid,
            "courseid" => $courseid,
            "daystart" => $bucket["daystart"],
            "weekdaynum" => $bucket["weekdaynum"],
            "slot" => $bucket["slot"],
            "visits" => $visits,
            "timespend" => $bucket["timespend"],
        ];
        $sql = "INSERT INTO {local_kopere_bi_period}
                    (userid, courseid, daystart, weekdaynum, slot, visits, timespend)
                VALUES (:userid, :courseid, :daystart, :weekdaynum, :slot, :visits, :timespend)";

        if ($DB->get_dbfamily() === "postgres") {
            $sql .= " ON CONFLICT (userid, courseid, daystart, slot) DO UPDATE
                        SET visits = visits + EXCLUDED.visits,
                            timespend = timespend + EXCLUDED.timespend";
        } else {
            $sql .= " ON DUPLICATE KEY UPDATE
                        visits = visits + VALUES(visits),
                        timespend = timespend + VALUES(timespend)";
        }
        $DB->execute($sql, $params);
    }
}
