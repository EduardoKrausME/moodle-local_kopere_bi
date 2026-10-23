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
 * Regression tests for period boundaries.
 *
 * @package   bifilters_period
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace bifilters_period;

use DateTimeImmutable;
use DateTimeZone;

/**
 * @covers \bifilters_period\period_range
 */
final class period_range_test extends \advanced_testcase {
    /**
     * Calendar end dates must be fully included.
     */
    public function test_custom_end_is_exclusive(): void {
        $zone = new DateTimeZone("America/Sao_Paulo");
        $now = new DateTimeImmutable("2026-10-22 12:00:00", $zone);
        $range = period_range::calculate("custom", "2026-10-01", "2026-10-22", $now, $zone);
        $this->assertSame((new DateTimeImmutable("2026-10-01", $zone))->getTimestamp(), $range["periodfrom"]);
        $this->assertSame((new DateTimeImmutable("2026-10-23", $zone))->getTimestamp(), $range["periodto"]);
    }

    /**
     * Relative intervals are calendar days, not a fixed number of seconds.
     */
    public function test_seven_days_across_dst(): void {
        $zone = new DateTimeZone("America/New_York");
        $now = new DateTimeImmutable("2026-03-09 14:00:00", $zone);
        $range = period_range::calculate("7d", "", "", $now, $zone);
        $this->assertSame("2026-03-03", $range["fromdate"]);
        $this->assertSame("2026-03-09", $range["todate"]);
        $this->assertSame((new DateTimeImmutable("2026-03-10", $zone))->getTimestamp(), $range["periodto"]);
    }

    /**
     * Calendar years include leap days when applicable.
     */
    public function test_one_calendar_year(): void {
        $zone = new DateTimeZone("UTC");
        $range = period_range::calculate("1y", "", "", new DateTimeImmutable("2024-03-01", $zone), $zone);
        $this->assertSame("2023-03-02", $range["fromdate"]);
        $this->assertSame("2024-03-01", $range["todate"]);
    }

    /**
     * Invalid calendar days are rejected, not normalized.
     */
    public function test_reject_invalid_date(): void {
        $zone = new DateTimeZone("UTC");
        $this->expectException(\moodle_exception::class);
        period_range::calculate("custom", "2026-02-30", "2026-10-22",
            new DateTimeImmutable("2026-10-22", $zone), $zone);
    }
}
