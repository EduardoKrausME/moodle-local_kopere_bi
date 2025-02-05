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
 * choose-block-type file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
 * setting file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery"], function($) {
    return {
        theme_palette : function() {

            var select_theme_palette = $("#id_s_local_kopere_bi_theme_palette");

            select_theme_palette.change(function() {
                var colors = ["", "#008FFB", "#00E396", "#FEB019", "#FF4560", "#775DD0"];
                switch (select_theme_palette.val()) {
                    case "palette1":
                        colors = ["", "#008FFB", "#00E396", "#FEB019", "#FF4560", "#775DD0"];
                        break;
                    case "palette2":
                        colors = ["", "#3f51b5", "#03a9f4", "#4caf50", "#f9ce1d", "#FF9800"];
                        break;
                    case "palette3":
                        colors = ["", "#33b2df", "#546E7A", "#d4526e", "#13d8aa", "#A5978B"];
                        break;
                    case "palette4":
                        colors = ["", "#4ecdc4", "#c7f464", "#81D4FA", "#fd6a6a", "#546E7A"];
                        break;
                    case "palette5":
                        colors = ["", "#2b908f", "#f9a3a4", "#90ee7e", "#fa4443", "#69d2e7"];
                        break;
                    case "palette6":
                        colors = ["", "#449DD1", "#F86624", "#EA3546", "#662E9B", "#C5D86D"];
                        break;
                    case "palette7":
                        colors = ["", "#D7263D", "#1B998B", "#2E294E", "#F46036", "#E2C044"];
                        break;
                    case "palette8":
                        colors = ["", "#662E9B", "#F86624", "#F9C80E", "#EA3546", "#43BCCD"];
                        break;
                    case "palette9":
                        colors = ["", "#5C4742", "#A5978B", "#8D5B4C", "#5A2A27", "#C4BBAF"];
                        break;
                    case "palette10":
                        colors = ["", "#A300D6", "#7D02EB", "#5653FE", "#2983FF", "#00B1F2"];
                        break;
                }

                cor_1.css({background : colors[1]});
                cor_2.css({background : colors[2]});
                cor_3.css({background : colors[3]});
                cor_4.css({background : colors[4]});
                cor_5.css({background : colors[5]});
            });

            var cor_1 = addAndStyle();
            var cor_2 = addAndStyle();
            var cor_3 = addAndStyle();
            var cor_4 = addAndStyle();
            var cor_5 = addAndStyle();

            function addAndStyle() {
                var element = $("<span></span>");
                element.css({
                    height  : "15px",
                    width   : "15px",
                    display : "inline-block",
                    margin  : "0 10px 0 0",
                });
                $("#id_s_local_kopere_bi_theme_palette_html").append(element);

                return element;
            }

            select_theme_palette.change();
        },

        chart_default : function() {
            $("#id_s_local_kopere_bi_chart_pie_default").css({
                "white-space" : "nowrap",
                "font-family" : "monospace",
            });
            $("#id_s_local_kopere_bi_chart_column_default").css({
                "white-space" : "nowrap",
                "font-family" : "monospace",
            });
            $("#id_s_local_kopere_bi_chart_area_default").css({
                "white-space" : "nowrap",
                "font-family" : "monospace",
            });
            $("#id_s_local_kopere_bi_chart_line_default").css({
                "white-space" : "nowrap",
                "font-family" : "monospace",
            });
        }
    };
});
