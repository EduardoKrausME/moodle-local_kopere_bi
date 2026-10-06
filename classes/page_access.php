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

use context;
use context_system;
use local_kopere_bi\vo\local_kopere_bi_page;
use moodle_exception;

/**
 * Centralises access checks for individual BI pages.
 *
 * A page always requires local/kopere_bi:view. When the page has a custom
 * capability configured, the user must hold that capability as well.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_access {

    /**
     * Returns the custom capability configured for a page.
     *
     * @param local_kopere_bi_page|object $page
     * @return string
     */
    public static function get_capability($page): string {
        return trim((string)($page->capability ?? ""));
    }

    /**
     * Checks whether the current user can view a page.
     *
     * @param local_kopere_bi_page|object $page
     * @param context|null $context
     * @return bool
     */
    public static function can_view($page, ?context $context = null): bool {
        $context = $context ?? context_system::instance();

        if (!has_capability("local/kopere_bi:view", $context)) {
            return false;
        }

        $capability = self::get_capability($page);
        return $capability === "" || has_capability($capability, $context);
    }

    /**
     * Requires access to a page.
     *
     * @param local_kopere_bi_page|object $page
     * @param context|null $context
     * @return void
     */
    public static function require_view($page, ?context $context = null): void {
        $context = $context ?? context_system::instance();

        require_capability("local/kopere_bi:view", $context);

        $capability = self::get_capability($page);
        if ($capability !== "") {
            require_capability($capability, $context);
        }
    }

    /**
     * Validates and normalises a capability before storing it on a page.
     *
     * Empty means that the page only requires local/kopere_bi:view.
     *
     * @param string $capability
     * @return string
     * @throws moodle_exception
     */
    public static function validate_capability(string $capability): string {
        global $DB;

        $capability = trim($capability);
        if ($capability === "") {
            return "";
        }

        if (!preg_match('/^[a-z][a-z0-9_]*\/[a-z][a-z0-9_]*:[a-z][a-z0-9_]*$/', $capability) ||
                !$DB->record_exists("capabilities", ["name" => $capability])) {
            throw new moodle_exception("page_capability_invalid", "local_kopere_bi", "", $capability);
        }

        return $capability;
    }
}
