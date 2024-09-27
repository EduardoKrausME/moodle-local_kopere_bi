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

namespace local_kopere_bi\block;

use local_kopere_dashboard\html\form;

/**
 * Interface i_type
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface i_type {

    /**
     * Function get_name
     *
     * @return mixed
     */
    public static function get_name();

    /**
     * Function get_description
     *
     * @return mixed
     */
    public static function get_description();

    /**
     * Function title_extra
     *
     * @param $koperebielement
     *
     * @return mixed
     */
    public function title_extra($koperebielement);

    /**
     * Function edit
     *
     * @param form $form
     * @param $koperebielement
     *
     * @return mixed
     */
    public function edit(form $form, $koperebielement);

    /**
     * Function is_edit_columns
     *
     * @return mixed
     */
    public function is_edit_columns();

    /**
     * Function edit_columns
     *
     * @param form $form
     * @param $koperebielement
     *
     * @return mixed
     */
    public function edit_columns(form $form, $koperebielement);

    /**
     * Function preview
     *
     * @param $koperebielement
     *
     * @return mixed
     */
    public function preview($koperebielement);

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     *
     * @return mixed
     */
    public function get_chart_data($koperebielement);
}
