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
 * Tests for the optional local_mcp promotion banner.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use coding_exception;

/**
 * Banner appears only to administrators without local_mcp until it is dismissed.
 *
 * @covers \local_kopere_bi\mcp_banner
 */
final class mcp_banner_test extends advanced_testcase {
    /**
     * test_hidden_for_non_administrators
     *
     * @return void
     */
    public function test_hidden_for_non_administrators(): void {
        $this->resetAfterTest();
        unset_config('version', 'local_mcp');
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertFalse(mcp_banner::should_show());
    }

    /**
     * test_visible_for_admin_when_not_installed
     *
     * @return void
     * @throws coding_exception
     */
    public function test_visible_for_admin_when_not_installed(): void {
        $this->resetAfterTest();
        unset_config('version', 'local_mcp');
        $this->setAdminUser();
        set_user_preference(mcp_banner::PREFERENCE_HIDDEN, '0');
        $this->assertTrue(mcp_banner::should_show());
    }

    /**
     * test_hidden_once_dismissed
     *
     * @return void
     * @throws coding_exception
     */
    public function test_hidden_once_dismissed(): void {
        $this->resetAfterTest();
        unset_config('version', 'local_mcp');
        $this->setAdminUser();
        set_user_preference(mcp_banner::PREFERENCE_HIDDEN, '1');
        $this->assertFalse(mcp_banner::should_show());
    }

    /**
     * test_hidden_if_mcp_is_installed
     *
     * @return void
     * @throws coding_exception
     */
    public function test_hidden_if_mcp_is_installed(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('version', 2026101200, 'local_mcp');
        set_user_preference(mcp_banner::PREFERENCE_HIDDEN, '0');
        $this->assertFalse(mcp_banner::should_show());
    }
}
