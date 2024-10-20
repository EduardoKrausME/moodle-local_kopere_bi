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
