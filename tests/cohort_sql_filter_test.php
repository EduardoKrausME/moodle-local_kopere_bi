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

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\block\util\sql_util;

/**
 * Cohort parameter substitution in BI report SQL.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local_kopere_bi\block\util\sql_util::params
 */
final class cohort_sql_filter_test extends advanced_testcase {
    /**
     * A single cohort parameter retains the original name.
     */
    public function test_single_cohort_parameter(): void {
        global $USER;

        $this->resetAfterTest();
        $USER->filter_cohortid = 37;
        [$sql, $params] = sql_util::params("SELECT :cohortid AS cohortid", false, false, true);

        $this->assertSame("SELECT :cohortid AS cohortid", $sql);
        $this->assertSame(["cohortid" => 37], $params);
    }

    /**
     * Repeated cohort parameters receive unique names.
     */
    public function test_repeated_cohort_parameter(): void {
        global $USER;

        $this->resetAfterTest();
        $USER->filter_cohortid = 37;
        [$sql, $params] = sql_util::params(
            "SELECT :cohortid AS firstcohort, :cohortid AS secondcohort",
            false,
            false,
            true
        );

        $this->assertSame("SELECT :count_1_cohortid AS firstcohort, :count_2_cohortid AS secondcohort", $sql);
        $this->assertSame(["count_1_cohortid" => 37, "count_2_cohortid" => 37], $params);
    }

    /**
     * Unselected cohorts bind zero without altering the SQL.
     */
    public function test_unselected_cohort_parameter(): void {
        global $USER;

        $this->resetAfterTest();
        $USER->filter_cohortid = 0;
        [$sql, $params] = sql_util::params("SELECT :cohortid AS cohortid", false, false, true);

        $this->assertSame("SELECT :cohortid AS cohortid", $sql);
        $this->assertSame(["cohortid" => 0], $params);
    }
}
