define(["jquery", "jqueryui", "local_kopere_dashboard/dataTables_init"], function($, $ui, dataTablesInit) {
    return {
        init : function() {
            var data_course_block_select = false;

            var chartCourse = $("#chart-course-button-open");
            chartCourse.show().click(function() {
                if (data_course_block_select) {
                    $("#chart-course-datatable-select").dialog("open");
                } else {
                    data_course_block_select = $("#chart-course-datatable-select").dialog({
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
                                text  : "",
                                click : function() {
                                    $(this).dialog("close");
                                }
                            }
                        ]
                    });

                    dataTablesInit.init("datatable_course_select", {
                        autoWidth    : false,
                        columns      : [
                            {data : "id"},
                            {data : "fullname"},
                            {data : "shortname"},
                            {data : "visible"},
                            {data : "inscritos"},
                        ],
                        columnDefs   : [
                            {
                                render  : "numberRenderer",
                                targets : 0,
                            },
                            {
                                render  : "visibleRenderer",
                                targets : 3,
                            },
                            {
                                render  : "numberRenderer",
                                targets : 4,
                            }
                        ],
                        export_title : false,
                        ajax         : {
                            url      : chartCourse.attr("data-urlajax"),
                            type     : "POST",
                            dataType : "json",
                        }
                    });

                    dataTablesInit.click("datatable_course_select", ["id"], chartCourse.attr("data-urlclick"));
                }
            });
        }
    }
});