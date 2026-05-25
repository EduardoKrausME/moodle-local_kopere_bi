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

namespace local_kopere_bi\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Dynamic Moodle form used by the BI editors.
 *
 * This class keeps the previous dynamic builder API used by the BI block providers,
 * but renders the final form using Moodle's moodleform/Formslib stack.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dynamic_moodleform extends moodleform {
    /**
     * No fixed fields here. Editors add fields dynamically through add_input().
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        if (!$mform->elementExists('sesskey')) {
            $mform->addElement('hidden', 'sesskey', sesskey());
            $mform->setType('sesskey', PARAM_RAW);
        }
    }

    /**
     * Check if a Moodle form was posted and the session key is valid.
     *
     * @return bool
     */
    public static function check_post() {
        return data_submitted() && optional_param('submitbutton', null, PARAM_RAW) !== null && confirm_sesskey();
    }

    /**
     * Add a field descriptor to the Moodle form.
     *
     * @param form_input $input
     * @return void
     */
    public function add_input(form_input $input) {
        $mform = $this->_form;
        $name = $input->get_name();
        $title = $input->get_title();
        $type = $input->get_type();
        $attributes = $this->get_attributes($input);

        if ($type === 'textarea') {
            $attributes += ['rows' => 5, 'cols' => 80];
            $mform->addElement('textarea', $name, $title, $attributes);
            $mform->setType($name, PARAM_RAW);
        } else if ($type === 'select') {
            $mform->addElement('select', $name, $title, $input->get_values(), $attributes);
            $mform->setType($name, PARAM_TEXT);
        } else if ($type === 'file') {
            $this->_form->updateAttributes(['enctype' => 'multipart/form-data']);
            $mform->addElement('file', $name, $title, $attributes);
        } else {
            $attributes['type'] = $type;
            $mform->addElement('text', $name, $title, $attributes);
            $mform->setType($name, PARAM_TEXT);
        }

        if ($input->is_required()) {
            $mform->addRule($name, get_string('required'), 'required', null, 'client');
        }

        if ($input->get_description() !== '') {
            $mform->addElement('static', $this->get_clean_id($name) . '_desc', '', $input->get_description());
        }

        if ($type !== 'file' && $input->get_value() !== null && $input->get_value() !== false) {
            $mform->setDefault($name, $input->get_value());
        }
    }


    /**
     * Add raw HTML inside the Moodle form.
     *
     * @param string $html
     * @return void
     */
    public function add_html($html) {
        $this->_form->addElement('html', $html);
    }

    /**
     * Add the submit button.
     *
     * @param string $label
     * @param string $class
     * @return void
     */
    public function create_submit_input($label) {
        $this->_form->addElement('submit', 'submitbutton', $label, ["button"]);
    }

    /**
     * Render the form and return the generated HTML.
     *
     * @return string
     */
    public function render() {
        ob_start();
        $this->display();
        return ob_get_clean();
    }

    /**
     * Compatibility method for the old builder.
     *
     * @return void
     */
    public function close() {
        echo $this->render();
    }

    /**
     * Build Moodle form element attributes.
     *
     * @param form_input $input
     * @return array
     */
    private function get_attributes(form_input $input) {
        $attributes = ['id' => $this->get_clean_id($input->get_name())];

        if ($input->get_style() !== '') {
            $attributes['style'] = $input->get_style();
        }

        foreach ($input->get_extras() as $extra) {
            $attributes = array_merge($attributes, $this->parse_extra_attributes($extra));
        }

        return $attributes;
    }

    /**
     * Create a predictable id from the element name.
     *
     * @param string $name
     * @return string
     */
    private function get_clean_id($name) {
        return preg_replace('/[^A-Za-z0-9_]/', '', $name);
    }

    /**
     * Parse a small raw attribute string from the old builder.
     *
     * @param string $extra
     * @return array
     */
    private function parse_extra_attributes($extra) {
        $attributes = [];
        if (preg_match_all('/([a-zA-Z0-9_:-]+)=("[^"]*"|\'[^\']*\')/', $extra, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes[$match[1]] = trim($match[2], '"\'');
            }
        }
        return $attributes;
    }
}
