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

namespace local_kopere_bi\task;

/**
 * Class delete_3months
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class online_delete extends \core\task\scheduled_task {

    /**
     * Var month
     *
     * @var int
     */
    public $month = 12;

    /**
     * Nome da task
     *
     * @return string
     * @throws \Exception
     */
    public function get_name() {
        return "Delete the last {$this->month} months of online student data";
    }

    /**
     * Executa a tarefa agendada
     *
     * @throws \dml_exception
     */
    public function execute() {
        global $DB;

        if ($DB->get_dbfamily() == "mysql") {
            $where = 'currenttime < DATE_SUB(NOW(), INTERVAL :month MONTH)';
            $DB->delete_records_select("local_kopere_bi_online", $where, ["month" => $this->month]);
        } else if ($DB->get_dbfamily() == "postgres") {
            $where = "currenttime < :month";
            $time = strtotime("-{$this->month} months", time());
            $DB->delete_records_select("local_kopere_bi_online", $where, ["month" => $time]);
        } else {
            mtrace("only mysqli and pgsql");
            return;
        }

        mtrace("Completed cleaning of results from the last {$this->month} months.");
    }
}
