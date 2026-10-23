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
 * Test cohort placeholder substitution in BI report SQL.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use local_kopere_bi\block\util\sql_util;

/**
 * Cohort parameter substitution in BI report SQL.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local_kopere_bi\block\util\sql_util
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

    /**
     * Validate a real cohort before binding it to report SQL.
     *
     * @covers \local_kopere_bi\block\util\sql_util::prepare_sql
     * @covers \local_kopere_bi\cohorts::can_view_cohort
     */
    public function test_prepare_sql_with_accessible_cohort(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $cohort = $this->getDataGenerator()->create_cohort(["name" => "BI cohort"]);
        $USER->filter_cohortid = (int)$cohort->id;

        $command = sql_util::prepare_sql("SELECT :cohortid AS cohortid");
        $this->assertSame(["cohortid" => (int)$cohort->id], $command->params);
        $this->assertSame("SELECT :cohortid AS cohortid", $command->sql);
    }

    /**
     * An invalid cohort ID must not reach a report query.
     *
     * @covers \local_kopere_bi\block\util\sql_util::prepare_sql
     * @covers \local_kopere_bi\cohorts::can_view_cohort
     */
    public function test_prepare_sql_rejects_missing_cohort(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $maxid = (int)$DB->get_field_sql("SELECT MAX(id) FROM {cohort}");
        $USER->filter_cohortid = $maxid + 1;

        $this->expectException(\moodle_exception::class);
        sql_util::prepare_sql("SELECT :cohortid AS cohortid");
    }

    /**
     * Cohorts hidden from an ordinary user cannot be selected.
     *
     * @covers \local_kopere_bi\cohorts::can_view_cohort
     */
    public function test_hidden_cohort_cannot_be_selected(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cohort = $this->getDataGenerator()->create_cohort([
            "name" => "Private BI cohort",
            "visible" => 0,
        ]);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->assertFalse(cohorts::can_view_cohort((int)$cohort->id));
        $this->assertFalse(cohorts::can_view_cohort(0));
    }
}
