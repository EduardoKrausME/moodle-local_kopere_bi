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
 * filter_course file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "jqueryui", "core/modal_factory", "local_kopere_dashboard/dataTables_init"],
    function($, $ui, ModalFactory, dataTables_init) {
        return {
            init: function() {
                var data_course_block_select = false;

                var chartCourse = $("#chart-course-button-open");
                chartCourse.show().click(function() {
                    if (data_course_block_select) {
                        data_course_block_select.show();
                    } else {
                        ModalFactory.create({
                            type: ModalFactory.types.DEFAULT,
                            title: $("#chart-course-datatable-select").attr("title"),
                            body: $("#chart-course-datatable-select").html(),
                        }).then(function(modal) {
                            if (!modal.root) {
                                modal.root = modal._root;
                            }

                            data_course_block_select = modal;
                            data_course_block_select.show();
                            data_course_block_select.root.addClass("kopere-bi");
                            data_course_block_select.root.find(".modal-dialog")
                                .addClass("modal-xl").addClass("kopere-dashboard-modal");

                            var urlajax = $("#chart-course-datatable-select").attr("data-urlajax");
                            var urlclick = $("#chart-course-datatable-select").attr("data-urlclick");

                            $("#chart-course-datatable-select").remove();

                            dataTables_init.init("datatable_course_select", {
                                autoWidth: true,
                                columns: [
                                    {data: "id"},
                                    {data: "fullname"},
                                    {data: "shortname"},
                                    {data: "visible"},
                                    {data: "inscritos"},
                                ],
                                columnDefs: [
                                    {
                                        render: "numberRenderer",
                                        targets: 0,
                                    }, {
                                        render: "visibleRenderer",
                                        targets: 3,
                                    }, {
                                        render: "numberRenderer",
                                        targets: 4,
                                    }
                                ],
                                export_title: false,
                                ajax: {
                                    url: urlajax,
                                    type: "POST",
                                    dataType: "json",
                                }
                            });

                            dataTables_init.click("datatable_course_select", ["id"], urlclick);
                        });
                    }
                });
            }
        }
    });