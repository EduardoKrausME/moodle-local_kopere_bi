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

use Exception;

/**
 * Class online_to_analytics
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class online_to_analytics extends \core\task\scheduled_task {
    /**
     * Nome da task
     *
     * @return string
     * @throws Exception
     */
    public function get_name() {
        return "Converts online data into access statistics";
    }

    /**
     * Executa a tarefa agendada
     *
     * @throws Exception
     */
    public function execute() {
        global $DB;

        $sql = "SELECT * FROM {local_kopere_bi_online} WHERE currenttime > :midnight ORDER BY userid ASC";
        $params = [
            "midnight" => strtotime("yesterday midnight"),
        ];
        $rows = $DB->get_recordset_sql($sql, $params);

        foreach ($rows as $row) {

            if ($this->lastuserid != $row->userid) {
                $this->save_statistic($row->userid);
            }

            $weekday = date("w", $row->currenttime); // Convert timestamp to the day of the week (e.g., 0 Sunday).
            $year = date("Y", $row->currenttime);    // Convert timestamp to the year (e.g., 2024).
            $month = date("m", $row->currenttime);   // Convert timestamp to the month (e.g., 09 for September).
            $day = date("d", $row->currenttime);     // Convert timestamp to the day (e.g., 24 for the 24th day).

            $key = "{$weekday}-{$year}-{$month}-{$day}";
            if (isset($this->statistics[$key])) {

                $this->statistics[$key]->seconds += $row->seconds;
            } else {

                $row->weekday = $weekday;
                $row->year = $year;
                $row->month = $month;
                $row->day = $day;

                $this->statistics[$key] = $row;
            }
        }
        $rows->close();

        $this->save_statistic($row->userid);
    }

    /**
     * Var lastuserid
     *
     * @var int
     */
    private $lastuserid = -1;
    /**
     * Var statistics
     *
     * @var array
     */
    private $statistics = [];

    /**
     * Function save_statistic
     *
     * @param $userid
     */
    private function save_statistic($userid) {
        global $DB;

        foreach ($this->statistics as $statistic) {
            try {
                $DB->insert_record("local_kopere_bi_statistic", $statistic);
            } catch (\dml_exception $e) { // phpcs:disable
            }
        }

        $this->statistics = [];
        $this->lastuserid = $userid;
    }
}
