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
 * Tests for local-time access period aggregation.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use DateTimeImmutable;
use DateTimeZone;
use local_kopere_bi\analytics\period_activity;

/**
 * Check weekday, midnight and time-slot boundaries.
 *
 * @covers \local_kopere_bi\analytics\period_activity
 */
final class period_activity_test extends advanced_testcase {
    /**
     * A heartbeat across 09:00 must not be charged wholly to one period.
     */
    public function test_split_seconds_across_two_slots(): void {
        $timezone = new DateTimeZone("America/Sao_Paulo");
        $end = new DateTimeImmutable("2026-10-22 09:00:15", $timezone);
        $buckets = period_activity::split_seconds($end->getTimestamp(), 30, $timezone);

        $this->assertCount(2, $buckets);
        $this->assertSame([2, 3], array_column($buckets, "slot"));
        $this->assertSame([15, 15], array_column($buckets, "timespend"));
        $this->assertSame([3, 3], array_column($buckets, "weekdaynum"));
    }

    /**
     * The end of Sunday and beginning of Monday belong to different days.
     */
    public function test_split_seconds_across_midnight(): void {
        $timezone = new DateTimeZone("UTC");
        $end = new DateTimeImmutable("2026-10-26 00:00:12", $timezone);
        $buckets = period_activity::split_seconds($end->getTimestamp(), 20, $timezone);

        $this->assertCount(2, $buckets);
        $this->assertSame([7, 1], array_column($buckets, "slot"));
        $this->assertSame([6, 0], array_column($buckets, "weekdaynum"));
        $this->assertSame([8, 12], array_column($buckets, "timespend"));
        $this->assertNotEquals($buckets[0]["daystart"], $buckets[1]["daystart"]);
    }

    /**
     * Invalid values are discarded and oversized client values are bounded.
     */
    public function test_rejects_nonpositive_and_caps_oversized_time(): void {
        $timezone = new DateTimeZone("UTC");
        $this->assertSame([], period_activity::split_seconds(100000, 0, $timezone));
        $this->assertSame([], period_activity::split_seconds(100000, -30, $timezone));

        $buckets = period_activity::split_seconds(100000, 10000000, $timezone);
        $this->assertSame(7200, array_sum(array_column($buckets, "timespend")));
    }
}
