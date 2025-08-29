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
 * filter file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "jqueryui", "core/modal_factory", "local_kopere_dashboard/dataTables_init"],
    function ($, $ui, ModalFactory, dataTables_init) {
    return {
        init: function (popupid, columns, columnDefs) {
            var data_block_select = false;

            var chart = $(`#${popupid}-btn-open`);
            chart.show().click(function () {
                if (data_block_select) {
                    data_block_select.show();
                } else {

                    ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: $(`#${popupid}-btn-open`).attr("title"),
                        body: $(`#${popupid}-datatable-select`).html(),
                    }).then(function (modal) {
                        if (!modal.root) {
                            modal.root = modal._root;
                        }

                        data_block_select = modal;
                        data_block_select.show();
                        data_block_select.root.addClass("kopere-bi");
                        data_block_select.root.find(".modal-dialog")
                            .addClass("modal-xl").addClass("kopere-dashboard-modal");

                        var urlajax = $(`#${popupid}-btn-open`).attr("data-urlajax");
                        var urlclick = $(`#${popupid}-btn-open`).attr("data-urlclick");

                        console.log(urlajax);
                        console.log(urlclick);

                        $(`#${popupid}-datatable-select`).remove();

                        var defaultColumnDefs = [
                            {
                                render: "numberRenderer",
                                targets: 0,
                            }
                        ];
                        if (columnDefs !== undefined) {
                            defaultColumnDefs = columnDefs;
                        }

                        dataTables_init.init(`${popupid}-table`, {
                            autoWidth: false,
                            columns: columns,
                            columnDefs: defaultColumnDefs,
                            export_title: false,
                            order: [[1, "asc"]],
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: urlajax,
                                type: "POST",
                                dataType: "json",
                            }
                        });

                        dataTables_init.click(`${popupid}-table`, ["id"], urlclick);
                    });
                }
            });
        }
    };
});
