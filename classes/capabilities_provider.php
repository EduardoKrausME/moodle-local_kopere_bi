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
 * Kopere Dashboard capabilities integration.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

/**
 * Class capabilities_provider
 */
class capabilities_provider {
    /**
     * Return the capabilities managed by Kopere Dashboard.
     *
     * @return array[]
     * @throws \coding_exception
     */
    public static function get_capabilities(): array {
        return [
            "local/kopere_bi:view" => [
                "name" => get_string("kopere_bi:view", "local_kopere_bi"),
                "description" => get_string("cap_view_desc", "local_kopere_bi"),
            ],
            "local/kopere_bi:manage" => [
                "name" => get_string("kopere_bi:manage", "local_kopere_bi"),
                "description" => get_string("cap_manage_desc", "local_kopere_bi"),
            ],
        ];
    }
}
