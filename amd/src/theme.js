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
 * theme file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery"], function($) {
    return {

        collapse_style : function() {
            $("#campo_chart_estilo-fieldset legend a").click(function(event) {
                event && event.preventDefault();
                $('#campo_chart_estilo-fieldset').toggleClass("collapsed");
            });
        },

        collapse_options : function() {
            $("#campo_chart_options-fieldset legend a").click(function(event) {
                event && event.preventDefault();
                $('#campo_chart_options-fieldset').toggleClass("collapsed");
            });
        },

        changue : function(texto) {
            function changeContent(content) {
                $("#editor_css").remove();
                $("#editor_css_area").html("<div id='editor_css' style='width:100%;height:200px;'>" + content + "</div>");

                var editor = ace.edit("editor_css");
                editor.setTheme("ace/theme/textmate");
                editor.getSession().setMode("ace/mode/scss");

                editor.session.on("change", function(delta) {
                    $("#css").val(editor.getValue());
                });
            }

            document.addEventListener("ace_load", function() {
                var css = $("#css")
                    .hide()
                    .after('<div id="editor_css_area"></div>')
                    .val();
                changeContent(css);
            });

            var infotheme = $("#infotheme");
            infotheme
                .change(function() {
                    changueTheme(infotheme.val())
                })
                .parent().css({"position" : "relative"});

            changueTheme(infotheme.val());

            function changueTheme(newTheme) {
                infotheme.parent().find(".chart-box").remove();
                infotheme.after(
                    "<div class='chart-box select-theme'>" +
                    "    <div class='test-theme theme-" + newTheme + "'>" + texto + "</div>" +
                    "</div>")
            }
        }
    };
});
