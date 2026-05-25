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
 * mod_koperebi.js
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", 'core/ajax', 'core/notification'], function($, ajax, notification) {
    var mod_koperebi = {
        init : function() {
            $(".kopere_bi_div-ajax").each(function(id, element) {
                mod_koperebi.load_html(element);
            });
        },

        load_html(element) {
            var page_id = $(element).attr("data-koperebi");

            ajax.call([{
                methodname : "local_kopere_bi_page_html",
                args       : {
                    page_id : page_id
                }
            }])[0].then(function(data) {
                $(element).html(data.html);

            }).then(function(msg) {
                console.error(msg);
            }).catch(notification.exception);
        }
    };

    return mod_koperebi;
});
