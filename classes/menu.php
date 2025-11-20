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

use Exception;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\menu_util;

/**
 * Class menu
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menu {
    /**
     * Function show_menu
     *
     * @return string
     * @throws Exception
     */
    public function show_menu() {
        return dashboard_util::add_menu(
            (new menu_util())
                ->set_classname("bi-dashboard")
                ->set_methodname("start")
                ->set_icon("dashboard")
                ->set_name(get_string("title", "local_kopere_bi")));
    }
}
