// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * online file
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax'], function(ajax) {
    return {
        init : function(online_id, key) {

            var tempoTotal = 0;

            var online_update_send = function(resolveIp) {
                if (tempoTotal === 0 && !resolveIp) {
                    return;
                }

                ajax.call([{
                    methodname : "local_kopere_bi_online_update",
                    args       : {
                        online_id  : online_id,
                        cache_key  : key,
                        seconds    : Math.round(tempoTotal),
                        resolve_ip : Boolean(resolveIp)
                    }
                }]);

                tempoTotal = 0;
            };

            // First asynchronous request resolves/links the IP after the page has rendered.
            online_update_send(true);

            window.addEventListener("beforeunload", function() {
                online_update_send(false);
            });

            var intervalId = setInterval(function() {
                if (document.hasFocus()) {
                    tempoTotal += 2;
                }
                if (tempoTotal >= 30) {
                    online_update_send(false);
                }
            }, 2 * 1000); // 2 seconds

            // After 20 minutes, pause sending minutes.
            setTimeout(function() {
                online_update_send(false);
                clearInterval(intervalId);
                online_update_send = console.log;
            }, 2 * 60 * 1000); // 2 minutes.

            console.log("\n %c Eduardo Kraus %c https://eduardokraus.com \n",
                "color: #FFFFFF; background: #2196F3; padding:8px 0;border-radius:5px;", "padding:8px 0;");
        }
    };
});
