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
 * lib file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kopere_bi\block\util\string_util;
use local_kopere_bi\core_hook_output;
use local_kopere_bi\filters\filter;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_page;

/**
 * Function local_kopere_bi_before_footer
 *
 * @throws Exception
 */
function local_kopere_bi_before_footer() {
    core_hook_output::before_footer_html_generation();
}

/**
 * Function getremoteaddr
 *
 * @return string
 */
function local_kopere_bi_getremoteaddr() {
    if (isset($_SERVER["HTTP_X_REAL_IP"])) {
        return $_SERVER["HTTP_X_REAL_IP"];
    } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }

    return getremoteaddr();
}

/**
 * Function local_kopere_bi_iplookup_find_location
 *
 * @param $ip
 * @return object
 */
function local_kopere_bi_iplookup_find_location($ip) {
    $cache = \cache::make("local_kopere_bi", "ip_user_location");

    if ($cache->has($ip)) {
        $dataip = $cache->get($ip);
    } else {
        $url = "http://ip-api.com/json/{$ip}";
        $context = stream_context_create(['http' => ['timeout' => 2]]);
        $dataip = json_decode(file_get_contents($url, false, $context));

        if (isset($dataip->query)) {
            $dataip->country_code = $dataip->countryCode;
            $dataip->latitude = $dataip->lat;
            $dataip->longitude = $dataip->lon;
        }
        $cache->set($ip, $dataip);
    }

    return (object)$dataip;
}

/**
 * Function load_kopere_bi_assets
 *
 * @return string
 */
function load_kopere_bi_assets() {
    static $koperebiloaded = false;

    if (!$koperebiloaded) {
        $koperebiloaded = true;

        local_kopere_dashboard_lang();

        return "";
    }

    return "";
}

/**
 * load_kopere_bi
 *
 * @param $pageid
 * @return string
 * @throws Exception
 */
function load_kopere_bi($pageid) {
    global $DB, $CFG;

    require_once("{$CFG->dirroot}/local/kopere_dashboard/autoload.php");

    $text = load_kopere_bi_assets();

    $text .= "<div class='kopere_dashboard_div'>";
    $text .= "<div class='content-w'>";
    $text .= "<div class='content-i'>";
    $text .= "<div class='content-box'>";

    /** @var local_kopere_bi_page $koperebipage */
    $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
    if ($koperebipage) {
        if ($koperebipage->description) {
            $text .= "<h2>" . string_util::get_string($koperebipage->description) . "</h2>";
        }

        $koperebiblocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $koperebipage->id], "sequence ASC");

        $text .= filter::create_filter($koperebipage);

        /** @var local_kopere_bi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            $text .= (new \local_kopere_bi\block\util\preview_util())->details_block($koperebiblock);
        }
    }

    $text .= "</div>";
    $text .= "</div>";
    $text .= "</div>";
    $text .= "</div>";

    return $text;
}

/**
 * load_kopere_bi_ajax
 *
 * @return string
 * @throws Exception
 */
function load_kopere_bi_ajax($coursemoduleid, $pageid) {
    global $CFG;

    require_once("{$CFG->dirroot}/local/kopere_dashboard/autoload.php");

    $text = load_kopere_bi_assets();
    $text .= "<div class='kopere_dashboard_div-ajax'
                   id='kopere_dashboard_div-coursemodule_{$coursemoduleid}'
                   data-koperebi='{$pageid}'>" . get_string("loading", "local_kopere_bi") . "</div>";

    return $text;
}

/**
 * Function local_kopere_bi_extend_navigation_course
 *
 * @param $navigation
 * @param $course
 * @param $context
 * @throws Exception
 */
function local_kopere_bi_extend_navigation_course($navigation, $course, $context) {
    if (!has_capability("local/kopere_bi:view", $context)) {
        return;
    }

    $reportnode = $navigation->get('coursereports');
    if (empty($reportnode)) {
        return;
    }

    global $DB;

    $pluginname = get_string("pluginname", "local_kopere_bi");
    $koperebipages = $DB->get_records("local_kopere_bi_page", [], "sortorder ASC");
    /** @var local_kopere_bi_page $koperebipage */
    foreach ($koperebipages as $koperebipage) {

        $params = [
            "classname" => "bi-dashboard",
            "method" => "preview",
            "page_id" => $koperebipage->id,
            "courseid" => $course->id,
        ];

        $url = new moodle_url("/local/kopere_dashboard/view.php", $params);
        $name = $pluginname . " - " . string_util::get_string($koperebipage->title);
        $settingsnode = navigation_node::create($name, $url, navigation_node::TYPE_SETTING);
        if (isset($settingsnode)) {
            $reportnode->add_node($settingsnode);
        }
    }
}
