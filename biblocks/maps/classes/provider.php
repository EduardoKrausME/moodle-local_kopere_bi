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

namespace biblocks_maps;

use Exception;
use local_kopere_bi\block\i_block_provider;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\database_util;
use local_kopere_bi\block\util\sql_util;
use local_kopere_bi\form\dynamic_moodleform;
use local_kopere_bi\ip_location;
use local_kopere_dashboard\util\message;

/**
 * Class maps
 *
 * @package   biblocks_maps
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements i_block_provider {

    /**
     * Function get_name
     *
     * @return string
     * @throws Exception
     */
    public static function get_name() {
        return get_string("pluginname", "biblocks_maps");
    }

    /**
     * Function get_description
     *
     * @return string
     * @throws Exception
     */
    public static function get_description() {
        return get_string("pluginname_desc", "biblocks_maps");
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
     * @return string
     */
    public function title_extra($koperebielement) {
        return "";
    }

    /**
     * Function edit
     *
     * @param dynamic_moodleform $form
     * @param $koperebielement
     * @return void
     * @throws Exception
     * @throws Exception
     */
    public function edit(dynamic_moodleform $form, $koperebielement) {

        $html = message::warning(get_string("maps_sql_warning", "biblocks_maps"));
        $form->add_html($html);

        code_util::input_commandsql($form, $koperebielement, false);
    }

    /**
     * Function is_edit_columns
     *
     * @return bool
     */
    public function is_edit_columns() {
        return false;
    }

    /**
     * Function edit_columns
     *
     * @param dynamic_moodleform $form
     * @param $koperebielement
     * @return void
     */
    public function edit_columns(dynamic_moodleform $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     * @return string
     */
    public function preview($koperebielement) {
        global $CFG;

        $id = uniqid();

        $param = ["item_id" => $koperebielement->id, "theme" => $koperebielement->theme];
        $url = urlencode("../../../view-ajax.php?classname=chart_data&method=load_data&" . http_build_query($param, "", "&"));

        $urlresource = urlencode("{$CFG->wwwroot}/local/kopere_bi/biblocks/maps/assets/resource/");

        return "
            <iframe id='maps-online-{$id}'
                    class='maps-online'
                    width='100%' height='525' frameborder='0' allowfullscreen
                    sandbox='allow-scripts allow-same-origin allow-popups'
                    allow=':encrypted-media; :picture-in-picture'
                    src='{$CFG->wwwroot}/local/kopere_bi/biblocks/maps/assets/?wwwroot={$url}&resource={$urlresource}'></iframe>
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
     * @return void
     * @throws Exception
     */
    public function get_chart_data($koperebielement) {
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
            $location = null;

            // New native reports already join the numeric location relation. For custom/old map SQL that still
            // returns only lastip, keep compatibility with a local DB lookup without ever calling the external API.
            if ((!isset($row["latitude"]) || !isset($row["longitude"])) && !empty($row["lastip"])) {
                $location = ip_location::find($row["lastip"]);
            }

            $latitude = $row["latitude"] ?? ($location->latitude ?? null);
            $longitude = $row["longitude"] ?? ($location->longitude ?? null);
            if ($latitude === null || $longitude === null || $latitude === '' || $longitude === '') {
                continue;
            }

            $data[] = [
                "nb_visits" => isset($row["nb_visits"]) ? (int)$row["nb_visits"] : 1,
                "lastip" => $row["lastip"] ?? ($location->ip ?? ''),
                "city_name" => $row["city_name"] ?? ($location->city_name ?? ''),
                "country_name" => $row["country_name"] ?? ($location->country_name ?? ''),
                "country_code" => $row["country_code"] ?? ($location->country_code ?? ''),
                "latitude" => $latitude,
                "longitude" => $longitude,
            ];
        }

        header("Content-Type: application/json");
        die(json_encode($data, JSON_NUMERIC_CHECK));
    }
}
