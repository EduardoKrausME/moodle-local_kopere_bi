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
 * Scheduled IP location refresh.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\task;

use local_kopere_bi\ip_location;

/**
 * Resolves IPs that did not get a location during the browser AJAX flow.
 */
class ip_location_update extends \core\task\scheduled_task {

    /** Maximum missing online IPs created in one daily run. */
    private const MAX_NEW_IPS = 100;

    /** Maximum failed/pending locations retried in one daily run. */
    private const MAX_RETRIES = 100;

    /**
     * Task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'local_kopere_bi') . ' - IP location update';
    }

    /**
     * Executes the task.
     *
     * @return void
     */
    public function execute() {
        global $DB;

        $sql = "SELECT DISTINCT lastip
                  FROM {local_kopere_bi_online}
                 WHERE iplocationid IS NULL
                   AND lastip IS NOT NULL
                   AND lastip <> ''";

        $missing = $DB->get_records_sql($sql, [], 0, self::MAX_NEW_IPS);
        foreach ($missing as $record) {
            ip_location::resolve($record->lastip, true);
        }

        $retrybefore = time() - DAYSECS;
        $select = '(status = :pending OR status = :retry) AND lastattempt < :retrybefore';
        $params = [
            'pending' => ip_location::STATUS_PENDING,
            'retry' => ip_location::STATUS_RETRY,
            'retrybefore' => $retrybefore,
        ];

        $locations = $DB->get_records_select(
            'local_kopere_bi_iplocation',
            $select,
            $params,
            'lastattempt ASC, id ASC',
            '*',
            0,
            self::MAX_RETRIES
        );

        foreach ($locations as $location) {
            ip_location::resolve($location->ip, true);
        }

        mtrace('Kopere BI IP locations: ' . count($missing) . ' new IPs and ' . count($locations) . ' retries processed.');
    }
}
