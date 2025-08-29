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
 * page-edit_page file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

            // Edit
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

            // Add
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

            // Delete
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
