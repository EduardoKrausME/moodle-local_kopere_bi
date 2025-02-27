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
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
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
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025011001) {
        $table = new xmldb_table("local_kopere_bi_cat");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table("local_kopere_bi_page");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table("local_kopere_bi_block");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table("local_kopere_bi_element");
        $field = new xmldb_field("refkey", XMLDB_TYPE_CHAR, 50, null, null, null, null, "id");
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        require_once("db-config.php");
        reset_bi_reports();

        upgrade_plugin_savepoint(true, 2025011001, "local", "kopere_bi");
    }

    return true;
}
