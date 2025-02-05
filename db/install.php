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
 * install file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Function xmldb_local_kopere_bi_install
 *
 * @return bool
 * @throws Exception
 */
function xmldb_local_kopere_bi_install() {
    global $CFG;

    if ($CFG->dbtype == "mysqli" || $CFG->dbtype == "mariadb" || $CFG->dbtype == "pgsql") {
        new moodle_exception("Unfortunately, at the moment I only accept Moodle running on MySql and PostgreSQL!");
    }

    require_once("db-config.php");
    reset_bi_reports();

    return true;
}
