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

use Exception;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\util\message;
use local_kopere_dashboard\util\url_util;

/**
 * Class maps
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class maps implements i_type {

    /**
     * Function get_name
     *
     * @return mixed|string
     * @throws Exception
     */
    public static function get_name() {
        return get_string("maps_name", "local_kopere_bi");
    }

    /**
     * Function get_description
     *
     * @return mixed|string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("maps_desc", "local_kopere_bi");
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
     * @return mixed|string
     */
    public function title_extra($koperebielement) {
        return "";
    }

    /**
     * Function edit
     *
     * @param form $form
     * @param $koperebielement
     * @return mixed|void
     * @throws Exception
     * @throws Exception
     */
    public function edit(form $form, $koperebielement) {

        message::print_warning(get_string("maps_sql_warning", "local_kopere_bi"));

        code_util::input_commandsql($form, $koperebielement, false);
    }

    /**
     * Function is_edit_columns
     *
     * @return bool|mixed
     */
    public function is_edit_columns() {
        return false;
    }

    /**
     * Function edit_columns
     *
     * @param form $form
     * @param $koperebielement
     * @return mixed|void
     */
    public function edit_columns(form $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     * @return mixed|string
     */
    public function preview($koperebielement) {
        global $CFG;

        $id = uniqid();

        $param = ["item_id" => $koperebielement->id, "theme" => $koperebielement->theme];
        $url = urlencode(url_util::makeurl("bi-chart_data", "load_data", $param, "view-ajax"));

        $urlresource = urlencode("{$CFG->wwwroot}/local/kopere_bi/maps/resource/");

        return "
            <iframe id='maps-online-{$id}'
                    class='maps-online'
                    width='100%' height='525' frameborder='0' allowfullscreen
                    sandbox='allow-scripts allow-same-origin allow-popups'
                    allow=':encrypted-media; :picture-in-picture'
                    src='{$CFG->wwwroot}/local/kopere_bi/maps/?wwwroot={$url}&resource={$urlresource}'></iframe>
            <script>
                var maps = document.getElementById('maps-online-{$id}');
                var newHeight = 525 * maps.offsetWidth / 1000;
                if (newHeight > window.innerHeight)
                    newHeight = window.innerHeight;
                maps.height = newHeight + 'px';
            </script>";
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     * @return mixed|void
     * @throws Exception
     */
    public function get_chart_data($koperebielement) {
        global $CFG;

        require_once($CFG->dirroot . "/local/kopere_bi/lib.php");

        $comand = sql_util::prepare_sql($koperebielement->commandsql);
        try {
            $rows = (new database_util())->get_records_sql_block_array($comand->sql, $comand->params);
        } catch (Exception $e) {
            if (AJAX_SCRIPT) {
                echo json_encode([
                    "sql" => $comand->sql,
                    "error" => $e->getMessage(),
                    "trace" => $e->getTraceAsString(),
                ]);
                die;
            } else {
                message::print_danger($e->getMessage());
                return;
            }
        }

        $data = [];
        foreach ($rows as $row) {
            $dataip = local_kopere_bi_iplookup_find_location($row["lastip"]);
            $userinfo = [
                "nb_visits" => 1,
                "lastip" => $row[0],
                "city_name" => $dataip->city,
                "country_name" => $dataip->country,
                "country_code" => $dataip->country_code ? $dataip->country_code : $dataip->country,
                "latitude" => $dataip->latitude,
                "longitude" => $dataip->longitude,
            ];
            $data[] = $userinfo;
        }

        header("Content-Type: application/json");
        die(json_encode($data, JSON_NUMERIC_CHECK));
    }
}
