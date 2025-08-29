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

use core_component;
use Exception;
use moodleform;
use tool_customlang_utils;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . "/lib/formslib.php");

/**
 * Class filter
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extra_langs_changue_component extends moodleform {
    /**
     * Function definition
     *
     * @throws Exception
     */
    public function definition() {
        global $CFG;

        require_once("{$CFG->dirroot}/admin/tool/customlang/locallib.php");
        require_once("{$CFG->dirroot}/lib/classes/component.php");

        $mform = $this->_form;

        // Component.
        $options = [];
        foreach (tool_customlang_utils::list_components() as $component => $normalized) {
            list($type, $plugin) = core_component::normalize_component($normalized);
            $options[$type][$normalized] = "{$component}.php";
        }
        $mform->addElement("selectgroups", "component", get_string("extra_langs_filter_component", "local_kopere_bi"), $options);

        // Show submit button.
        $mform->addElement("submit", "submit", get_string("filtershowstrings", "tool_customlang"));
    }
}
