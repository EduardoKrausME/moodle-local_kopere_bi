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
 * Report column preferences inside Kopere BI (not site administration).
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use context_system;
use html_writer;
use moodle_url;

/**
 * Global report column settings controller.
 */
final class report_column_settings {
    /**
     * Render and save the administrative screen.
     *
     * @return string
     */
    public function index(): string {
        global $PAGE, $OUTPUT;

        require_capability('local/kopere_bi:manage', context_system::instance());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_sesskey();
            $user = optional_param_array('usercolumns', [], PARAM_ALPHANUMEXT);
            $course = optional_param_array('coursecolumns', [], PARAM_ALPHANUMEXT);
            report_columns::save($user, $course);
            redirect(new moodle_url('/local/kopere_bi/index.php', [
                'classname' => 'report_column_settings',
                'method' => 'index',
                'saved' => 1,
            ]));
        }

        $title = get_string('reportcols_heading', 'local_kopere_bi');
        $PAGE->set_title($title);
        $PAGE->navbar->add($title);

        $html = '';
        if (optional_param('saved', 0, PARAM_BOOL)) {
            $html .= $OUTPUT->notification(
                get_string('reportcols_saved', 'local_kopere_bi'), 'success'
            );
        }
        $html .= html_writer::tag('h2', s($title));
        $html .= html_writer::tag('p', s(get_string('reportcols_description', 'local_kopere_bi')));
        $url = new moodle_url('/local/kopere_bi/index.php', [
            'classname' => 'report_column_settings',
            'method' => 'index',
        ]);
        $html .= html_writer::start_tag('form', [
            'method' => 'post',
            'action' => $url->out(false),
            'class' => 'element-box',
        ]);
        $html .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => 'sesskey',
            'value' => sesskey(),
        ]);

        foreach (['user', 'course'] as $entity) {
            $selected = report_columns::selected($entity);
            $mandatory = $entity === 'user' ? 'name' : 'fullname';
            $html .= html_writer::start_tag('fieldset', [
                'class' => 'mb-4',
            ]);
            $html .= html_writer::tag('legend', s(get_string(
                'reportcols_' . $entity . '_legend', 'local_kopere_bi'
            )), ['class' => 'h4']);
            foreach (report_columns::available($entity) as $key => $label) {
                $name = ($entity === 'user' ? 'usercolumns' : 'coursecolumns') . '[]';
                $required = $key === $mandatory;
                $attributes = [
                    'type' => 'checkbox',
                    'name' => $name,
                    'value' => $key,
                    'class' => 'form-check-input',
                ];
                if ($required || in_array($key, $selected, true)) {
                    $attributes['checked'] = 'checked';
                }
                if ($required) {
                    $attributes['disabled'] = 'disabled';
                }
                $check = html_writer::empty_tag('input', $attributes);
                $description = $required ? ' (' . get_string(
                    'reportcols_mandatory', 'local_kopere_bi'
                ) . ')' : '';
                $html .= html_writer::tag('label',
                    $check . ' ' . s($label . $description), ['class' => 'form-check d-block mb-2']);
            }
            $html .= html_writer::end_tag('fieldset');
        }
        $html .= html_writer::tag('p', s(get_string(
            'reportcols_scope_note', 'local_kopere_bi'
        )), ['class' => 'text-muted']);
        $html .= html_writer::tag('button', s(get_string('savechanges')), [
            'type' => 'submit',
            'class' => 'btn btn-primary',
        ]);
        $html .= html_writer::end_tag('form');
        return $html;
    }
}
