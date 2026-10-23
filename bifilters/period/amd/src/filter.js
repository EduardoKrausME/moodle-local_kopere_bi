// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.

/**
 * Date range UI; timestamp conversion stays in PHP.
 *
 * @module     bifilters_period/filter
 * @package    bifilters_period
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    return {
        init: function(formid) {
            const form = document.getElementById(formid);
            if (!form) {
                return;
            }
            const preset = form.querySelector("[name=periodpreset]");
            const from = form.querySelector("[name=periodfromdate]");
            const to = form.querySelector("[name=periodtodate]");
            const apply = form.querySelector("button[type=submit]");
            const update = function() {
                const custom = preset.value === "custom";
                const option = preset.options[preset.selectedIndex];
                if (!custom) {
                    from.value = option.dataset.from;
                    to.value = option.dataset.to;
                }
                from.disabled = !custom;
                to.disabled = !custom;
                apply.disabled = custom && (!from.value || !to.value || from.value > to.value);
            };
            preset.addEventListener("change", update);
            from.addEventListener("change", update);
            to.addEventListener("change", update);
            update();
        }
    };
});
