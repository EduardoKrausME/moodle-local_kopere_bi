define(['core/ajax'], function(ajax) {
    return {
        init : function(online_id, key) {

            var tempoTotal = 0;

            var online_update_send = function() {

                // console.log("Enviar: " + tempoTotal);
                if (tempoTotal == 0) return;

                // Envia a notificação para o webservice sobre o tempo gasto a casa 1 minuto
                ajax.call([{
                    methodname : 'local_kopere_bi_online_update',
                    args       : {
                        online_id : online_id,
                        cache_key : key,
                        seconds  : Math.round(tempoTotal)
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
                    // console.log("Vamos enviar: " + tempoTotal);
                    online_update_send();
                }
                // console.log("tempoTotal: " + tempoTotal);
            }, 2 * 1000); // 2 seconds

            // Após 20 minutos, pausa o envio do minutos
            setTimeout(function() {
                // console.log("Fimmmm");
                online_update_send();
                clearInterval(intervalId);
                online_update_send = console.log;
            }, 2 * 60 * 1000); // 2 minutos
        }
    };
});
