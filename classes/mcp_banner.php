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
 * Optional local_mcp promotion banner.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

/**
 * Visibility and dismissal state for the optional local_mcp banner.
 */
class mcp_banner {
    /** @var string User preference to suppress the banner. */
    public const PREFERENCE_HIDDEN = 'local_kopere_bi_mcp_banner_hidden';

    /**
     * Show only to site administrators when local_mcp is not installed and the notice was not dismissed.
     *
     * @return bool
     */
    public static function should_show(): bool {
        if (!is_siteadmin()) {
            return false;
        }
        if (get_config('local_mcp', 'version') !== false) {
            return false;
        }
        return !get_user_preferences(self::PREFERENCE_HIDDEN, false);
    }
}
