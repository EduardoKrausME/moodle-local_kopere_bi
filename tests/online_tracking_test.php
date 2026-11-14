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
 * Tests for deferred, cached tracking enrichment.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use advanced_testcase;
use cache;
use local_kopere_bi\analysis\access;
use local_kopere_bi\analysis\device_detector_cache;
use local_kopere_bi\external\online_update;

/**
 * Regression tests for deferred browser detection and authenticated AJAX tracking.
 *
 * @covers \local_kopere_bi\analysis\access
 * @covers \local_kopere_bi\analysis\device_detector_cache
 * @covers \local_kopere_bi\external\online_update
 */
final class online_tracking_test extends advanced_testcase {
    /** Browser User-Agent shared by test cases. */
    private const TEST_UA = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) " .
        "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36";

    public function test_agent_result_is_cached_by_user_agent(): void {
        $this->resetAfterTest();
        $detected = access::agent(self::TEST_UA);
        $this->assertSame(self::TEST_UA, $detected->user_agent);
        $this->assertNotSame("unknown", $detected->client_name);

        $key = hash("sha256", \DeviceDetector\DeviceDetector::VERSION . "\0" . self::TEST_UA);
        $resultcache = cache::make("local_kopere_bi", "device_detector_agents");
        $resultcache->set($key, [
            "client_type" => "browser",
            "client_name" => "Cached Browser",
            "client_version" => "123",
            "os_name" => "Cached OS",
            "os_version" => "10",
        ]);
        try {
            $cached = access::agent(self::TEST_UA);
            $this->assertSame("Cached Browser", $cached->client_name);
            $this->assertSame("Cached OS", $cached->os_name);
        } finally {
            $resultcache->delete($key);
        }
    }

    public function test_parser_regexes_have_a_persistent_muc_adapter(): void {
        $this->resetAfterTest();
        require_once(__DIR__ . "/../classes/analysis/vendor/autoload.php");

        $adapter = new device_detector_cache();
        $key = "kopere-bi-phpunit-fixture";
        $this->assertTrue($adapter->save($key, [["regex" => "test"]]));
        $this->assertTrue($adapter->contains($key));
        $this->assertSame([["regex" => "test"]], $adapter->fetch($key));
        $this->assertTrue($adapter->delete($key));
        $this->assertFalse($adapter->fetch($key));
    }

    public function test_ajax_tracks_a_student_without_report_permissions(): void {
        global $DB;

        $this->resetAfterTest();
        set_config("online_tracking_enabled", 1, "local_kopere_bi");
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->setUser($user);
        $id = $this->create_online_record($user->id, $course->id);

        $result = online_update::api($id, $course->id . ">0", 20, false);
        $this->assertTrue($result["success"]);
        $online = $DB->get_record("local_kopere_bi_online", ["id" => $id], "*", MUST_EXIST);
        $this->assertSame(21, (int)$online->seconds);
        $this->assertEmpty($online->client_name);
    }

    public function test_ajax_cannot_update_another_users_record(): void {
        global $DB;

        $this->resetAfterTest();
        set_config("online_tracking_enabled", 1, "local_kopere_bi");
        $owner = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $id = $this->create_online_record($owner->id, $course->id);
        $this->setUser($other);

        online_update::api($id, $course->id . ">0", 90, false);
        $online = $DB->get_record("local_kopere_bi_online", ["id" => $id], "*", MUST_EXIST);
        $this->assertSame(1, (int)$online->seconds);
    }

    public function test_first_ajax_call_enriches_the_online_record(): void {
        global $DB;

        $this->resetAfterTest();
        set_config("online_tracking_enabled", 1, "local_kopere_bi");
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->setUser($user);
        $id = $this->create_online_record($user->id, $course->id);

        $previousua = $_SERVER["HTTP_USER_AGENT"] ?? null;
        $_SERVER["HTTP_USER_AGENT"] = self::TEST_UA;
        try {
            online_update::api($id, $course->id . ">0", 0, true);
        } finally {
            if ($previousua === null) {
                unset($_SERVER["HTTP_USER_AGENT"]);
            } else {
                $_SERVER["HTTP_USER_AGENT"] = $previousua;
            }
        }

        $online = $DB->get_record("local_kopere_bi_online", ["id" => $id], "*", MUST_EXIST);
        $this->assertNotEmpty($online->client_name);
        $this->assertNotEmpty($online->os_name);
        $this->assertSame(1, (int)$online->seconds);
    }

    /**
     * Insert the minimal online record used by tracking tests.
     *
     * @param int $userid Owner of the record.
     * @param int $courseid Course being tracked.
     * @return int Record identifier.
     */
    private function create_online_record(int $userid, int $courseid): int {
        global $DB;

        return $DB->insert_record("local_kopere_bi_online", (object)[
            "userid" => $userid,
            "courseid" => $courseid,
            "moduleid" => 0,
            "seconds" => 1,
            "currenttime" => time(),
            "lastip" => "",
        ]);
    }
}
