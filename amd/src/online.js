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
 * online file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax'], function(ajax) {
    return {
        init : function(online_id, key) {

            var tempoTotal = 0;

            var online_update_send = function() {

                if (tempoTotal == 0) return;

                // Sends notification to the webservice about time spent at home 1 minute
                ajax.call([{
                    methodname : "local_kopere_bi_online_update",
                    args       : {
                        online_id : online_id,
                        cache_key : key,
                        seconds   : Math.round(tempoTotal)
                    }
                }]);

                tempoTotal = 0;
            };

            window.addEventListener("beforeunload", online_update_send);

            var intervalId = setInterval(function() {
                if (document.hasFocus()) {
                    tempoTotal += 2;
                }
                if (tempoTotal >= 30) {
                    online_update_send();
                }
            }, 2 * 1000); // 2 seconds

            // After 20 minutes, pause sending minutes.
            setTimeout(function() {
                online_update_send();
                clearInterval(intervalId);
                online_update_send = console.log;
            }, 2 * 60 * 1000); // 2 minutes
        }
    };
});
