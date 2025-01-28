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

namespace local_kopere_bi\local\vo;

/**
 * phpcs:disable
 * Class local_kopere_bi_online
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_kopere_bi_online extends \stdClass {

    /**
     * Var id
     *
     * @var int
     */
    public $id;

    /**
     * Var userid
     *
     * @var int
     */
    public $userid;

    /**
     * Var courseid
     *
     * @var int
     */
    public $courseid;

    /**
     * Var moduleid
     *
     * @var int
     */
    public $moduleid;

    /**
     * Var seconds
     *
     * @var int
     */
    public $seconds;

    /**
     * Var currenttime
     *
     * @var int
     */
    public $currenttime;

    /**
     * Var client_type
     *
     * @var string
     */
    public $client_type;

    /**
     * Var client_name
     *
     * @var string
     */
    public $client_name;

    /**
     * Var client_version
     *
     * @var string
     */
    public $client_version;

    /**
     * Var os_name
     *
     * @var string
     */
    public $os_name;

    /**
     * Var os_version
     *
     * @var string
     */
    public $os_version;

    /**
     * Var lastip
     *
     * @var string
     */
    public $lastip;

    /**
     * Var city_name
     *
     * @var string
     */
    public $city_name;

    /**
     * Var country_name
     *
     * @var string
     */
    public $country_name;

    /**
     * Var country_code
     *
     * @var string
     */
    public $country_code;

    /**
     * Var latitude
     *
     * @var string
     */
    public $latitude;

    /**
     * Var longitude
     *
     * @var string
     */
    public $longitude;
}
