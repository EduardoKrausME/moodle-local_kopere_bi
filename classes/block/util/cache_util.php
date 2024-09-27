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
 * Class cache_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_util {
    /**
     * Function delete
     *
     * @param $id
     */
    public static function delete($id) {
        $cache = \cache::make("local_kopere_bi", "block_chart_data_15m");
        $cache->delete($id);

        $cache = \cache::make("local_kopere_bi", "block_chart_data_30m");
        $cache->delete($id);

        $cache = \cache::make("local_kopere_bi", "block_chart_data_1h");
        $cache->delete($id);

        $cache = \cache::make("local_kopere_bi", "block_chart_data_6h");
        $cache->delete($id);

        $cache = \cache::make("local_kopere_bi", "block_chart_data_1d");
        $cache->delete($id);
    }

    /**
     * Function get_cache_make
     *
     * @param $cache
     *
     * @return mixed
     */
    public static function get_cache_make($cache) {
        return \cache::make("local_kopere_bi", "block_chart_data_{$cache}");
    }
}
