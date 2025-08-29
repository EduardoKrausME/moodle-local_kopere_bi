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

use Exception;
use ScssPhp\ScssPhp\Compiler;

/**
 * Class scss_util
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scss_util {

    /**
     * Function build_css
     *
     * @param $koperebielement
     * @return string
     * @throws Exception
     */
    public static function build_css($koperebielement) {
        global $CFG;

        if (isset($koperebielement->css) && strlen($koperebielement->css) > 5) {

            $scss = "
                #chart-box-{$koperebielement->id} {
                    {$koperebielement->css}
                }";
            $cacheoptions = [
                "cacheDir" => "{$CFG->localcachedir}/scsscache/",
                "prefix" => "scss_util_",
                "forceRefresh" => false,
            ];

            $compiler = new Compiler($cacheoptions);
            $css = $compiler->compileString($scss);

            if (isset($css[18])) {
                $css = preg_replace('/\s+/', " ", $css);
                return "<style>{$css}</style>";
            }
        }

        return "";
    }
}
