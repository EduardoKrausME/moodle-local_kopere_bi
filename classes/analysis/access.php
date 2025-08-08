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

use DeviceDetector\DeviceDetector;

/**
 * Class access
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access {
    /**
     * Function agent
     *
     * @return object
     */
    public static function agent() {
        require_once(__DIR__ . '/vendor/autoload.php');

        $dd = new DeviceDetector($_SERVER["HTTP_USER_AGENT"]);
        $dd->parse();

        $data = ["user_agent" => $_SERVER["HTTP_USER_AGENT"]];

        $client = $dd->getClient();
        $data["client_type"] = isset($client["type"]) ? $client["type"] : "unknown";
        $data["client_name"] = isset($client["name"]) ? $client["name"] : "unknown";
        $data["client_version"] = isset($client["version"]) ? $client["version"] : "unknown";

        $os = $dd->getOs();
        $data["os_name"] = isset($os["name"]) ? $os["name"] : "unknown";
        $data["os_version"] = isset($os["version"]) ? $os["version"] : "unknown";

        return (object)$data;
    }
}
