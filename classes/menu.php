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
 * Kopere Dashboard menu integration.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use context;
use local_kopere_dashboard\api\subplugin_manager;
use local_kopere_bi\block\util\string_util;
use local_kopere_bi\vo\local_kopere_bi_cat;
use moodle_url;

/**
 * Class menu
 */
class menu {
    /**
     * Return the menu definition used by Kopere Dashboard.
     *
     * @param context $context
     * @return array
     * @throws \coding_exception
     * @throws \moodle_exception
     * @throws \core\exception\moodle_exception
     * @throws \Exception
     */
    public static function get_definition(context $context): array {
        global $DB;

        $children = [];

        $koperebicats = $DB->get_records("local_kopere_bi_cat", null, "sortorder ASC");

        /** @var local_kopere_bi_cat $koperebicat */
        foreach ($koperebicats as $koperebicat) {
            $params = [
                "classname" => "dashboard",
                "method" => "start",
                "bicat" => $koperebicat->id,
            ];
            $children[] = [
                "title" => string_util::get_string($koperebicat->title),
                "url" => new moodle_url("/local/kopere_bi/index.php", $params),
                "activeurls" => [
                    new moodle_url("/local/kopere_bi/index.php", ["bicat" => $koperebicat->id]),
                ],
                "icon" => "bar_chart_4_bars",
            ];
        }

        return [
            "category" => subplugin_manager::CAT_PEDAGOGIC,
            "items" => [
                [
                    "title" => get_string("pluginname", "local_kopere_bi"),
                    "description" => get_string("menu_desc", "local_kopere_bi"),
                    "url" => new moodle_url("/local/kopere_bi/index.php", ["classname" => "dashboard", "method" => "start"]),
                    "activeurls" => [
                        new moodle_url("/local/kopere_bi/index.php"),
                    ],
                    "icon" => "area_chart",
                    "capability" => "local/kopere_bi:view",
                    "children" => $children,
                ],
            ],
        ];
    }
}
