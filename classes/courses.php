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
 * couses file
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use cache;
use context_course;
use local_kopere_dashboard\util\json;

/**
 * Class courses
 *
 * @package local_kopere_bi
 */
class courses {
    /**
     * Function load_all_courses
     * @throws \Exception
     */
    public function load_all_courses() {
        global $DB;

        $mycourses = optional_param("mycourses", false, PARAM_INT);

        $cache = cache::make("local_kopere_bi", "block_chart_data_1h");
        $cachekey = "load_all_courses";
        if (!$mycourses && $cache->has($cachekey)) {
            $courses = $cache->get($cachekey);
        } else {
            $courses = $DB->get_records_sql("
                SELECT c.id, c.fullname, c.shortname, c.visible, COUNT(DISTINCT ue.id) AS enrolments
                  FROM {course}           c
                  JOIN {enrol}            e ON e.courseid = c.id
                  JOIN {user_enrolments} ue ON ue.enrolid = e.id
                 WHERE c.id > 1
              GROUP BY c.id, c.fullname, c.shortname, c.visible"
            );

            if ($mycourses) {
                $returncourses = [];
                foreach ($courses as $course) {
                    $coursecontext = context_course::instance($course->id);
                    if (has_capability("moodle/course:view", $coursecontext)) {
                        $returncourses[] = $course;
                    }
                }

                $courses = $returncourses;
            } else {
                $cache->set($cachekey, $courses);
            }
        }

        json::encode($courses);
    }
}
