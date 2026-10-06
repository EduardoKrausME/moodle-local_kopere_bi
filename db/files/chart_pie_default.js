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
 * chart_pie_default.js
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

json = {
    chart       : {
        type       : "donut",
        height     : 400,
        width      : "100%",
        zoom       : {
            enabled : false
        },
        dropShadow : {
            enabled : false,
            top     : 3,
            left    : 2,
            blur    : 4,
            opacity : 1
        }
    },
    stroke      : {
        curve  : "smooth",
        width  : 2,
        show   : true,
        colors : ["transparent"]
    },
    dataLabels  : {
        enabled : false
    },
    tooltip     : {
        followCursor : true
    },
    legend      : {
        position : "left",
        offsetY  : 80
    },
    plotOptions : {
        pie    : {
            customScale : 1,
            donut       : {
                size : 0,
            },
            offsetY     : 20,
        },
        stroke : {
            colors : undefined
        }
    }
}
