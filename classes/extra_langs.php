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

namespace local_kopere_bi;

use local_kopere_bi\form\extra_langs_changue_component;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\header;
use tool_customlang_utils;

/**
 * Class extra langs
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extra_langs {
    /**
     * Var existinginlang
     *
     * @var array
     */
    private $existinginlang = [
        // Student-related keys.
        'u_id',                 // Unique ID of the student.
        'u_fullname',           // Full name of the student.
        'u_idnumber',           // Student's identification number.
        'num_user',             // Number of students.
        'country_name',         // Name of the country.

        // Course completion details.
        'timecompleted',        // Time when enrollment was completed.
        'course_completed',     // Percentage of the course completed.
        'grade',                // Grade or score received.

        // Module-related keys.
        'cm_cmid',              // Course Module ID.
        'l_objecttable',        // Name of the module or object.
        'l_origin',             // Origin of the log or event.
        'l_ip',                 // IP address related to the log or event.
        'l_timecreated',        // Timestamp when the event was created.

        // Enrollment-related keys.
        'cc_id',                // Completed course ID.
        'cc_timecompleted',     // Time when the course was completed.
        'ue_id',                // Enrollment ID.
        'ue_timecreated',       // Timestamp when enrollment was created.
        'ue_timeend',           // Timestamp when enrollment ends.
        'ue_status',            // Enrollment status.
        'ul_timeaccess',        // Last access time of the user.
        'e_enrol',              // Type of enrollment.
        'g_finalgrade',         // Final grade received.

        // Course-related keys.
        'ctx_instanceid',       // Courses the user is enrolled in.
        'ca_completed_activities', // Number of completed activities.
        'c_id',                 // Course ID.
        'c_shortname',          // Short name of the course.
        'c_format',             // Format of the course (e.g., weekly, topics).
        'c_fullname',           // Full name of the course.
        'c_timemodified',       // Timestamp when the course was last modified.
        'c_enablecompletion',   // Whether course completion is enabled.
        'c_tempo',              // Time spent in the course.

        // User location details.
        'firstname',            // First name of the student.
        'city_name',            // City of the student.
        'country_name',         // Country of the student.

        // Client and operating system details.
        'client_name',          // Name of the browser used by the student.
        'client_version',       // Version of the browser.
        'os_name',              // Operating system used by the student.
    ];

    /**
     * Function index
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function index() {
        global $OUTPUT, $PAGE, $USER, $DB;

        dashboard_util::add_breadcrumb(get_string('title', 'local_kopere_bi'), "?classname=bi-dashboard&method=start");
        dashboard_util::start_page();

        $component = optional_param("component", "local_kopere_bi", PARAM_TEXT);

        $url = new \moodle_url($PAGE->url, [
            "classname" => required_param("classname", PARAM_TEXT),
            "method" => required_param("method", PARAM_TEXT),
        ]);
        $filter = new extra_langs_changue_component($url, null, "get");
        $filter->set_data([
            "component" => $component,
        ]);
        $filter->display();

        $existinginlang = [];
        if ($component == "local_kopere_bi") {
            foreach ($this->existinginlang as $identifier) {
                $existinginlang[] = [
                    "identifier" => $identifier,
                    "string" => get_string($identifier, "local_kopere_bi"),
                ];
            }
        } else {
            tool_customlang_utils::checkout($_SESSION['SESSION']->lang);

            $sql = "SELECT tl.stringid, tl.original
                      FROM {tool_customlang} tl
                      JOIN {tool_customlang_components} tlc ON tlc.id = tl.componentid
                     WHERE tlc.name = :component
                       AND tl.lang  = :lang";
            $langs = $DB->get_records_sql($sql, [
                "component" => $component,
                "lang" => $USER->lang,
            ]);

            foreach ($langs as $lang) {
                if (isset($lang->original[40])) {
                    continue;
                }
                $existinginlang[] = [
                    "identifier" => $lang->stringid,
                    "string" => get_string($lang->stringid, $component),
                ];
            }

        }

        echo $OUTPUT->render_from_template("local_kopere_bi/extra_langs_existinginlang_index", [
            "existinginlang" => $existinginlang,
            "component" => $component,
        ]);

        if ($component == "local_kopere_bi") {

            if (form::check_post()) {
                $toolcustomlangcomponent = $DB->get_record('tool_customlang_components', ["name" => "local_kopere_bi"]);

                for ($i = 0; $i < 80; $i++) {
                    $identifier = "word_extra_" . substr("0{$i}", -2);
                    $toolcustomlang = $DB->get_record("tool_customlang", [
                        "lang" => $_SESSION['SESSION']->lang,
                        "componentid" => $toolcustomlangcomponent->id,
                        "stringid" => $identifier,
                    ]);
                    $toolcustomlang->local = optional_param($identifier, "", PARAM_TEXT);
                    $toolcustomlang->timecustomized = time();
                    $toolcustomlang->modified = 1;

                    $DB->update_record("tool_customlang", $toolcustomlang);
                }
                tool_customlang_utils::checkin($_SESSION['SESSION']->lang);

                header::location("?{$_SERVER["QUERY_STRING"]}");
            }
            tool_customlang_utils::checkout($_SESSION['SESSION']->lang);

            $customs = [];
            $countempty = 0;
            for ($i = 0; $i < 80; $i++) {
                $identifier = "word_extra_" . substr("0{$i}", -2);
                $string = get_string($identifier, "local_kopere_bi");

                $customs[] = [
                    "identifier" => $identifier,
                    "string" => $string,
                ];

                if (!isset($string[1])) {
                    $countempty++;
                }

                if ($countempty > 5) {
                    break;
                }
            }

            $form = new form("?{$_SERVER["QUERY_STRING"]}");
            echo $OUTPUT->render_from_template("local_kopere_bi/extra_langs_custom_index", [
                "customs" => $customs,
                "component" => $component,
            ]);

            $form->create_submit_input(get_string('save', 'local_kopere_bi'));

            $form->close();
        }

        dashboard_util::end_page();
    }
}
