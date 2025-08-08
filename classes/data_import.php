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
use local_kopere_bi\install\reports;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_text;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\header;

/**
 * Class data_import
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_import {
    /**
     * Function cat_upload
     *
     * @throws Exception
     */
    public function cat_upload() {
        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(get_string("cat_upload", "local_kopere_bi"));
        dashboard_util::start_page();

        echo '<div class="element-box">';
        $form = new form("?classname=bi-data_import&method=import");

        $form->add_input(
            input_text::new_instance()
                ->set_type("file")
                ->set_title(get_string("cat_upload_filetitle", "local_kopere_bi"))
                ->set_name("cat_upload_file")
        );

        $form->create_submit_input(get_string("import"), "button");

        echo "</div>";
        dashboard_util::end_page();
    }

    /**
     * import
     *
     * @return void
     * @throws Exception
     */
    public function import() {
        if (form::check_post()) {
            if (isset($_FILES["cat_upload_file"]["tmp_name"])) {
                $json = file_get_contents($_FILES["cat_upload_file"]["tmp_name"]);
                $data = json_decode($json);

                if ($data) {
                    reports::from_json($data);

                    header::location("?classname=bi-dashboard&method=start");
                    die;
                }
            }
        }
        header::location("?classname=bi-data_import&method=cat_upload");
    }
}
