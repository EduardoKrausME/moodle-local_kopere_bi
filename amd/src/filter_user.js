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
 * filter_user file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
