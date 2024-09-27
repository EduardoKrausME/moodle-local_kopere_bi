<?php
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

namespace local_kopere_bi\block\util;

/**
 * Class reload_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reload_util {

    /**
     * Function convert
     *
     * @param $reload
     *
     * @return bool|float|int
     */
    public static function convert($reload) {

        switch ($reload) {
            case "1m":
                return 1 * 60 * 1000;
            case "5m":
                return 5 * 60 * 1000;
            case "10m":
                return 10 * 60 * 1000;
            case "20m":
                return 20 * 60 * 1000;
            case "30m":
                return 30 * 60 * 1000;
            case "40m":
                return 40 * 60 * 1000;
            case "50m":
                return 50 * 60 * 1000;
            case "1h":
                return 60 * 60 * 1000;
            case "2h":
                return 2 * 60 * 60 * 1000;
        }

        return false;
    }
}
