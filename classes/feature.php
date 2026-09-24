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

/**
 * Centralises runtime feature switches for Kopere BI.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feature {
    /**
     * Returns whether online tracking is enabled.
     *
     * @return bool
     */
    public static function online_tracking_enabled(): bool {
        return self::config_enabled("online_tracking_enabled");
    }

    /**
     * Returns whether the report support table synchronisation is enabled.
     *
     * @return bool
     */
    public static function report_tables_sync_enabled(): bool {
        return self::config_enabled("report_tables_sync_enabled");
    }

    /**
     * Returns whether the learning analytics snapshot is enabled.
     *
     * @return bool
     */
    public static function analytics_snapshot_enabled(): bool {
        return self::config_enabled("analytics_snapshot_enabled");
    }

    /**
     * Checks whether a BI page may be shown with the current feature configuration.
     *
     * @param object $page
     * @return bool
     */
    public static function page_is_available($page): bool {
        if (!self::online_tracking_enabled() && isset($page->refkey) && $page->refkey === "online_students") {
            return false;
        }

        return true;
    }

    /**
     * Checks whether a BI category may be shown with the current feature configuration.
     *
     * @param object $category
     * @return bool
     */
    public static function category_is_available($category): bool {
        if (!self::online_tracking_enabled() && isset($category->refkey) && $category->refkey === "online") {
            return false;
        }

        return true;
    }

    /**
     * Reads a boolean plugin setting while keeping existing installations enabled by default.
     *
     * Missing settings are treated as enabled so an upgrade does not silently disable an
     * existing feature before the administrator saves the settings page for the first time.
     *
     * @param string $name
     * @return bool
     */
    private static function config_enabled($name): bool {
        $value = get_config("local_kopere_bi", $name);

        if ($value === false) {
            return true;
        }

        return (bool)$value;
    }
}
