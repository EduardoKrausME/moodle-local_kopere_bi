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

namespace local_kopere_bi\util;

use coding_exception;

/**
 * Class string_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_util {
    /**
     * Function trunc
     *
     * @param $string
     * @param $maxlength
     *
     * @return string
     * @throws \coding_exception
     */
    public static function trunc($string, $maxlength) {
        $string = self::get_string($string);
        $string = strip_tags($string);
        $stringarray = explode(" ", $string);

        $stringreturn = "";
        foreach ($stringarray as $palavra) {
            $stringreturn .= " {$palavra}";

            if (strlen($stringreturn) >= $maxlength) {
                return trim($stringreturn) . "...";
            }
        }

        return $string;
    }

    /**
     * Function get_string
     *
     * @param $string
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_string($string) {
        if (strpos($string, "lang::") === false || $string == "#" || $string == "") {
            return $string;
        }

        $strings = explode("::", $string);
        if (isset($strings[2])) {
            $identifier = $strings[1];

            if (clean_param($identifier, PARAM_STRINGID) === "") {
                throw new coding_exception("Invalid string identifier '{$identifier}' in '{$string}'.", DEBUG_DEVELOPER);
            }

            return get_string($identifier, $strings[2]);
        }
        return $string;
    }
}
