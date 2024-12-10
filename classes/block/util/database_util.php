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
 * Class database_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_util {

    /**
     * Function ready_only
     *
     * @param $sql
     *
     * @return string
     *
     * @throws \Exception
     */
    private function ready_only($sql) {
        global $DB, $CFG;

        // Prevents SQL from deleting, altering, or inserting data.
        if ($CFG->dbtype == "mysqli" || $CFG->dbtype = "mariadb") {
            try {
                $DB->execute("SET @@SESSION.transaction_read_only = ON");
            } catch (\Exception $e) {
                // phpcs:Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            }
            try {
                $DB->execute("SET SESSION transaction_read_only = ON");
            } catch (\Exception $e) {
                // phpcs:Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            }

            try {
                $DB->execute("SET SESSION TRANSACTION READ ONLY");
            } catch (\Exception $e) {
                // phpcs:Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            }
        } else if ($CFG->dbtype == "pgsql") {
            try {
                $DB->execute("SET default_transaction_read_only = ON");
            } catch (\Exception $e) {
                // phpcs:Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            }
            try {
                $DB->execute("SET TRANSACTION READ ONLY");
            } catch (\Exception $e) {
                // phpcs:Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            }
        } else {
            throw new \Exception("only mysqli and pgsql");
        }

        $sql = preg_replace('/;(\s+)?$/s', "", $sql);

        return $sql;
    }

    /**
     * Function get_record_sql_block
     *
     * @param $sql
     * @param array|null $params
     *
     * @return null|object
     *
     * @throws \Exception
     */
    public function get_record_sql_block($sql, $params = null) {
        return (object)$this->get_records_sql_block($sql, $params, true);
    }

    /**
     * Function get_records_sql_block
     *
     * @param $sql
     * @param array|null $params
     * @param bool $onerow
     *
     * @return array
     *
     * @throws \dml_exception
     * @throws \Exception
     */
    public function get_records_sql_block($sql, $params = null, $onerow = false) {
        global $DB;

        $sql = $this->ready_only($sql);
        $result = $DB->get_recordset_sql($sql, $params);

        $return = [];
        foreach ($result as $row) {
            if ($onerow) {
                $result->close();
                return $row;
            }

            $return[] = (object)$row;
        }

        $result->close();
        return $return;
    }

    /**
     * Function get_records_sql_block_array
     *
     * @param $sql
     * @param array|null $params
     *
     * @return array
     * @throws \Exception
     */
    public function get_records_sql_block_array($sql, $params = null) {
        global $DB;

        $sql = $this->ready_only($sql);
        $result = $DB->get_recordset_sql($sql);

        $return = [];
        foreach ($result as $row) {
            $row = (array)$row;
            $return[] = $row;
        }
        $result->close();

        return $return;
    }
}
