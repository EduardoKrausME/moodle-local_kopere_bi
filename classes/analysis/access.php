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

namespace local_kopere_bi\analysis;

use cache;
use DeviceDetector\DeviceDetector;

/**
 * Detects the current browser and operating system after page rendering.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access {
    /** Avoid parsing excessively long, client-controlled User-Agent headers. */
    private const MAX_USER_AGENT_LENGTH = 512;

    /**
     * Returns cached browser and OS information, parsing the User-Agent only on a cache miss.
     *
     * @param string|null $useragent Optional User-Agent (defaults to the request header).
     * @return object
     */
    public static function agent(?string $useragent = null): object {
        $useragent = substr(trim($useragent ?? ($_SERVER["HTTP_USER_AGENT"] ?? "")),
            0, self::MAX_USER_AGENT_LENGTH);
        $data = (object)[
            "user_agent" => $useragent,
            "client_type" => "unknown",
            "client_name" => "unknown",
            "client_version" => "unknown",
            "os_name" => "unknown",
            "os_version" => "unknown",
        ];

        if ($useragent === "" ||
                !is_file(__DIR__ . "/vendor/autoload.php") ||
                !is_file(__DIR__ . "/vendor/matomo/device-detector/DeviceDetector.php")) {
            return $data;
        }

        require_once(__DIR__ . "/vendor/autoload.php");

        // Include the library version to invalidate results when its regexes change.
        $key = hash("sha256", DeviceDetector::VERSION . "\0" . $useragent);
        $resultcache = cache::make("local_kopere_bi", "device_detector_agents");
        $cached = $resultcache->get($key);
        if (is_array($cached)) {
            foreach ($cached as $field => $value) {
                if (property_exists($data, $field)) {
                    $data->{$field} = $value;
                }
            }
            return $data;
        }

        $dd = new DeviceDetector($useragent);
        $dd->setCache(new device_detector_cache());
        $dd->parse();

        $client = $dd->getClient();
        $os = $dd->getOs();
        $data->client_type = \core_text::substr((string)($client["type"] ?? "unknown"), 0, 100);
        $data->client_name = \core_text::substr((string)($client["name"] ?? "unknown"), 0, 100);
        $data->client_version = \core_text::substr((string)($client["version"] ?? "unknown"), 0, 100);
        $data->os_name = \core_text::substr((string)($os["name"] ?? "unknown"), 0, 100);
        $data->os_version = \core_text::substr((string)($os["version"] ?? "unknown"), 0, 100);

        $resultcache->set($key, [
            "client_type" => $data->client_type,
            "client_name" => $data->client_name,
            "client_version" => $data->client_version,
            "os_name" => $data->os_name,
            "os_version" => $data->os_version,
        ]);

        return $data;
    }
}
