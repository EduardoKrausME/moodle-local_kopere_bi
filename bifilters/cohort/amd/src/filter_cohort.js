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
 * Cohort filter AMD module.
 *
 * @module     bifilters_cohort/filter_cohort
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "local_kopere_bi/filter"], function($, Filter) {
    return {
        init: function() {
            var button = $('[data-urlajax*="load_all_cohorts"]').first();

            if (!button.length) {
                return;
            }

            var buttonid = button.attr("id");
            var suffix = "-btn-open";

            if (!buttonid || buttonid.slice(-suffix.length) !== suffix) {
                return;
            }

            var popupid = buttonid.slice(0, -suffix.length);

            Filter.init(popupid, [
                {data: "id"},
                {data: "name"}
            ]);
        }
    };
});
