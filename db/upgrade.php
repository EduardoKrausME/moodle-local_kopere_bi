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
 * upgrade file
 *
 * @package   local_kopere_bi
 * @copyright 2020 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function xmldb_local_kopere_bi_upgrade
 *
 * @param $oldversion
 *
 * @return bool
 * @throws downgrade_exception
 * @throws upgrade_exception
 * @throws dml_exception
 */
function xmldb_local_kopere_bi_upgrade($oldversion) {

    if ($oldversion < 2023101600) {
        require_once("db-config.php");
        reset_bi_reports();

        upgrade_plugin_savepoint(true, 2023101600, "local", "kopere_bi");
    }

    return true;
}
