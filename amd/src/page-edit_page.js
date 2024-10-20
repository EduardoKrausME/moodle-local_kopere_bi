define(["jquery", "jqueryui", "core/ajax", "core/notification"], function($, $ui, ajax, notification) {
    return {

        page_sortable : function(page_id) {
            $("#page-block-sort").sortable({
                placeholder : "ui-state-highlight",
                update      : function(event, ui) {
                    var itens = "";
                    $("#page-block-sort .line").each(function() {
                        itens += "," + $(this).attr("data-blockid")
                    });

                    ajax.call([{
                        methodname : "local_kopere_bi_block_sequence",
                        args       : {
                            itens : itens
                        }
                    }])[0].then(function(data) {
                        // console.log(data);
                    }).then(function(msg) {
                        console.error(msg);
                    }).catch(notification.exception);
                }
            });
        },

        page_blocks : function(page_id) {

            // Botão adicionar novo Bloco
            var $bt_add_block = $('<span class="btn btn-primary btn-add-block">' + M.util.get_string("block_add", "local_kopere_bi") + "</span>");
            $("#page-block-sort").append($bt_add_block);
            $bt_add_block.click(function() {
                $("#dialog-confirm-block-add").dialog({
                    resizable : false,
                    height    : "auto",
                    width     : "auto",
                    maxWidth  : 400,
                    modal     : true,
                    classes   : {
                        "ui-dialog" : "kopere-dashboard-modal"
                    },
                    show      : {
                        effect   : "blind",
                        duration : 200
                    },
                    hide      : {
                        duration : 300
                    },
                    buttons   : [
                        {
                            text  : M.util.get_string("close", "admin"),
                            click : function() {
                                $(this).dialog("close");
                            }
                        }
                    ]
                });
            });

            // Botão add Bloco dentro do
            $(".line-add").click(function() {

                $("#dialog-confirm-block-add").dialog("close");

                ajax.call([{
                    methodname : "local_kopere_bi_block_add",
                    args       : {
                        page_id : page_id,
                        type    : $(this).attr("data-type"),
                    }
                }])[0].then(function(data) {
                    if (data.status == "OK") {
                        $bt_add_block.before(data.html);
                    }
                }).then(function(msg) {
                    console.error(msg);
                }).catch(notification.exception);
            });

            // Botão delete
            $("#page-block-sort .line").each(function(id, element) {

                var $element = $(element);
                var $deleteButton = $('<span class="delete-block">' + M.util.get_string("delete", "moodle") + "</span>");
                $element.append($deleteButton);
                $deleteButton.click(function() {
                    var blockid = $element.attr("data-blockid");

                    $("#dialog-confirm-block-" + blockid).dialog({
                        resizable : false,
                        height    : "auto",
                        width     : "auto",
                        maxWidth  : 400,
                        modal     : true,
                        classes   : {
                            "ui-dialog" : "kopere-dashboard-modal"
                        },
                        show      : {
                            effect   : "blind",
                            duration : 200
                        },
                        hide      : {
                            duration : 300
                        },
                        buttons   : [
                            {
                                text  : M.util.get_string("delete", "moodle"),
                                click : function() {

                                    $(this).dialog("close");

                                    ajax.call([{
                                        methodname : "local_kopere_bi_block_delete",
                                        args       : {
                                            block_id : blockid
                                        }
                                    }])[0].then(function(data) {
                                        $("#blockid-" + blockid)
                                            .hide(200)
                                            .delay(200)
                                            .remove();
                                    }).then(function(msg) {
                                        console.error(msg);
                                    });
                                    //.catch(notification.exception);
                                }
                            }, {
                                text  : M.util.get_string("cancel", "moodle"),
                                click : function() {
                                    $(this).dialog("close");
                                }
                            }
                        ]
                    });
                });
            });
        }
    };
});
