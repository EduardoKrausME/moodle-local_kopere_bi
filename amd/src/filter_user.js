define(["jquery", "jqueryui", "local_kopere_dashboard/dataTables_init"], function($, $ui, dataTables_init) {
    return {
        init : function() {
            var data_user_block_select = false;

            var chartUser = $("#chart-user-button-open");
            chartUser.show().click(function() {
                if (data_user_block_select) {
                    $("#chart-user-datatable-select").dialog("open");
                } else {
                    data_user_block_select = $("#chart-user-datatable-select").dialog({
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
                                text  : chartUser.attr("data-strclose"),
                                click : function() {
                                    $(this).dialog("close");
                                }
                            }
                        ]
                    });

                    dataTables_init.init("datatable_user_select", {
                        autoWidth    : false,
                        columns      : [
                            {data : "id"},
                            {data : "fullname"},
                            {data : "username"},
                            {data : "email"},
                        ],
                        columnDefs   : [
                            {
                                render  : "numberRenderer",
                                targets : 0,
                            }
                        ],
                        export_title : false,
                        order        : [[1, "asc"]],
                        processing   : true,
                        serverSide   : true,
                        ajax         : {
                            url      : chartUser.attr("data-urlajax"),
                            type     : "POST",
                            dataType : "json",
                        }
                    });

                    dataTables_init.click("datatable_user_select", ["id"], chartUser.attr("data-urlclick"));
                }
            });
        }
    };
});
