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

/**
 * cache file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$definitions = [
    // Caches the location of IPs.
    "ip_user_location" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 10 * 24 * 60 * 60, // 10d.
        "staticacceleration" => true,
    ],

    // Caches searches.
    "block_chart_data_none" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 1 * 60, // 1 minute.
        "staticacceleration" => true,
    ],
    "block_chart_data_15m" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 15 * 60, // 15 minutes.
        "staticacceleration" => true,
    ],
    "block_chart_data_30m" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 30 * 60, // 15 minutes.
        "staticacceleration" => true,
    ],
    "block_chart_data_1h" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 1 * 60 * 60, // 1 hour.
        "staticacceleration" => true,
    ],
    "block_chart_data_6h" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 6 * 60 * 60, // 6 hours.
        "staticacceleration" => true,
    ],
    "block_chart_data_1d" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 24 * 60 * 60, // 24 hours.
        "staticacceleration" => true,
    ],

    "mustache_sql" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 1 * 60 * 60, // 1 hours.
        "staticacceleration" => true,
    ],
    "mustache_nosql" => [
        "mode" => cache_store::MODE_APPLICATION,
        "ttl" => 24 * 60 * 60, // 24 hours.
        "staticacceleration" => true,
    ],
];
