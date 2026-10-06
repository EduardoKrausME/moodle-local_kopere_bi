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
 * chart_area_default.js
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

json = {
    chart       : {
        type       : "area",
        height     : 350,
        zoom       : {
            enabled : true
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
    grid        : {
        show    : true,
        padding : {
            bottom : 0
        }
    },
    legend      : {
        position        : "top",
        horizontalAlign : "right",
        offsetY         : -20
    },
    markers     : {
        size        : 4,
        strokeWidth : 0,
        hover       : {
            size : 6
        }
    },
    plotOptions : {
        bar : {
            horizontal  : false,
            columnWidth : "75%",
            endingShape : "rounded"
        }
    },
    fill        : {
        opacity : 1
    },
    xaxis       : {
        tooltip : {
            enabled : false
        }
    }
}
