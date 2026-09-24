<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_kopere_bi\vo;

/**
 * phpcs:disable
 * Class local_kopere_bi_online
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_kopere_bi_online extends \stdClass {
    /** @var int */
    public $id;
    /** @var int */
    public $userid;
    /** @var int */
    public $courseid;
    /** @var int */
    public $moduleid;
    /** @var int */
    public $seconds;
    /** @var int */
    public $currenttime;
    /** @var string */
    public $client_type;
    /** @var string */
    public $client_name;
    /** @var string */
    public $client_version;
    /** @var string */
    public $os_name;
    /** @var string */
    public $os_version;
    /** @var string */
    public $lastip;
    /** @var int|null */
    public $iplocationid;
}
