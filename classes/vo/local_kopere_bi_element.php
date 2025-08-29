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

namespace local_kopere_bi\vo;

/**
 * phpcs:disable
 * Class local_kopere_bi_element
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_kopere_bi_element extends \stdClass {

    /**
     * Var id
     *
     * @var int
     */
    public $id;

    /**
     * Var title
     *
     * @var string
     */
    public $title;

    /**
     * Var block_id
     *
     * @var int
     */
    public $block_id;

    /**
     * Var block_num
     *
     * @var int
     */
    public $block_num;

    /**
     * Var type
     *
     * @var string
     */
    public $type;

    /**
     * Var theme
     *
     * @var string
     */
    public $theme;

    /**
     * Var css
     *
     * @var string
     */
    public $css;

    /**
     * Var html_before
     *
     * @var string
     */
    public $html_before;

    /**
     * Var html_after
     *
     * @var string
     */
    public $html_after;

    /**
     * Var commandsql
     *
     * @var string
     */
    public $commandsql;

    /**
     * Var cache
     *
     * @var string
     */
    public $cache;

    /**
     * Var reload
     *
     * @var string
     */
    public $reload;

    /**
     * Var info
     *
     * @var string
     */
    public $info;

    /**
     * Var time
     *
     * @var int
     */
    public $time;
}
