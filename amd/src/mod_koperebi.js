define(["jquery", 'core/ajax', 'core/notification'], function($, ajax, notification) {
    var mod_koperebi = {
        init : function() {
            $(".kopere_dashboard_div-ajax").each(function(id, element) {
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
