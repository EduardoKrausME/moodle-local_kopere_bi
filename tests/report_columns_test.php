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
 * Regression tests for global Kopere BI report columns.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;

/**
 * @covers \local_kopere_bi\report_columns
 */
final class report_columns_test extends advanced_testcase {
    /**
     * An installation that has not saved preferences retains its SQL-shaped output.
     */
    public function test_legacy_rows_are_untouched_until_configuration_is_saved(): void {
        $this->resetAfterTest();
        $row = (object)['id' => 42, 'firstname' => 'Ana', 'lastname' => 'Silva',
            'username' => 'ana123', 'email' => 'ana@example.org'];
        $columns = ['username' => ['title' => 'Username', 'type' => 'string']];
        [$output, $headers] = report_columns::prepare([$row], $columns,
            'SELECT u.id, u.firstname, u.lastname, u.username, u.email FROM {user} u');

        $this->assertEquals($row, $output[0]);
        $this->assertSame($columns, $headers);
    }

    /**
     * Disabling a standard field removes it from both headers and JSON rows.
     */
    public function test_disabling_username_does_not_break_identity_lookup(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user([
            'username' => 'student0123',
            'email' => 'student0123@example.org',
            'firstname' => 'Ana',
            'lastname' => 'Silva',
        ]);
        set_config('reportcols_user', json_encode(['name', 'email', 'idnumber']),
            'local_kopere_bi');

        $row = (object)['username' => $user->username, 'firstname' => $user->firstname,
            'lastname' => $user->lastname];
        $columns = [
            'username' => ['title' => 'Username', 'type' => 'string'],
            'firstname' => ['title' => 'Name', 'type' => 'userfullname'],
            'lastname' => ['title' => 'Surname', 'type' => 'none'],
        ];
        [$output, $headers] = report_columns::prepare([$row], $columns,
            'SELECT u.username, u.firstname, u.lastname FROM {user} u');

        $this->assertArrayNotHasKey('username', $headers);
        $this->assertFalse(property_exists($output[0], 'username'));
        $this->assertSame($user->email, $output[0]->user_email);
        $this->assertArrayHasKey('user_email', $headers);
    }

    /**
     * User custom fields can be added without editing the saved report SQL.
     */
    public function test_custom_profile_field_respects_private_visibility(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user(['username' => 'cpfstudent']);
        $categoryid = $DB->insert_record('user_info_category', (object)[
            'name' => 'Institution', 'sortorder' => 1,
        ]);
        $fieldid = $DB->insert_record('user_info_field', (object)[
            'shortname' => 'cpf',
            'name' => 'CPF',
            'datatype' => 'text',
            'description' => '',
            'descriptionformat' => FORMAT_HTML,
            'categoryid' => $categoryid,
            'sortorder' => 1,
            'required' => 0,
            'locked' => 0,
            'visible' => 0,
            'forceunique' => 0,
            'signup' => 0,
            'defaultdata' => '',
            'defaultdataformat' => FORMAT_HTML,
            'param1' => '',
            'param2' => '',
            'param3' => '',
            'param4' => '',
            'param5' => '',
        ]);
        $DB->insert_record('user_info_data', (object)[
            'userid' => $user->id,
            'fieldid' => $fieldid,
            'data' => '123.456.789-00',
            'dataformat' => FORMAT_PLAIN,
        ]);
        set_config('reportcols_user', json_encode(['name', 'custom_cpf']),
            'local_kopere_bi');

        $rows = [(object)['userid' => $user->id]];
        [$adminrows, $adminheaders] = report_columns::prepare($rows, ['userid' => [
            'title' => 'User ID', 'type' => 'number']], 'SELECT u.id AS userid FROM {user} u');
        $this->assertSame('123.456.789-00', $adminrows[0]->user_custom_cpf);
        $this->assertArrayHasKey('user_custom_cpf', $adminheaders);

        $viewer = $this->getDataGenerator()->create_user();
        [$viewerrows] = report_columns::prepare($rows, ['userid' => [
            'title' => 'User ID', 'type' => 'number']], 'SELECT u.id AS userid FROM {user} u',
            [], (int)$viewer->id);
        $this->assertSame('', $viewerrows[0]->user_custom_cpf);
    }

    /**
     * Avoid silently treating an unrelated id (e.g. attempt or question id) as a person.
     */
    public function test_unrelated_id_is_not_treated_as_user_id(): void {
        $columns = ['id' => 1, 'firstname' => 'Test', 'lastname' => 'User'];
        $this->assertNull(report_columns::identity_column($columns,
            'SELECT a.id, a.firstname, a.lastname FROM {quiz_attempts} a', [], 'user'));
    }

    /**
     * Course columns use course identifiers, not the primary key of a report row.
     */
    public function test_course_column_enrichment(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'History of Moodle', 'shortname' => 'HISTORY53',
        ]);
        set_config('reportcols_course', json_encode(['fullname', 'shortname']),
            'local_kopere_bi');

        [$rows, $headers] = report_columns::prepare(
            [(object)['id' => 9, 'courseid' => $course->id]],
            ['id' => ['title' => 'Record ID', 'type' => 'number'],
                'courseid' => ['title' => 'Course', 'type' => 'number']],
            'SELECT x.id, x.courseid FROM {course_modules} x'
        );

        $this->assertSame('History of Moodle', $rows[0]->course_fullname);
        $this->assertSame('HISTORY53', $rows[0]->course_shortname);
        $this->assertArrayHasKey('course_fullname', $headers);
        $this->assertSame(9, $rows[0]->id);
    }
}
