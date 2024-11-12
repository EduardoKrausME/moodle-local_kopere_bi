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
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kopere_bi\analise\access_analyze;
use local_kopere_bi\util\filter;
use local_kopere_bi\util\string_util;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_page;

/**
 * Function local_kopere_bi_before_footer
 *
 * @throws coding_exception
 */
function local_kopere_bi_before_footer() {
    global $USER, $DB, $COURSE, $PAGE;

    if (!isloggedin()) {
        return;
    }

    if (isguestuser()) {
        return;
    }

    $moduleid = 0;
    if ($PAGE->cm && $PAGE->cm->id) {
        $moduleid = $PAGE->cm->id;
    }

    $key = $COURSE->id . ">" . $moduleid;

    if (isset($USER->koperebionline_id)) {
        $USER->koperebionline_id = [];
    }

    if (isset($USER->koperebionline_id[$key])) {
        if ($USER->koperebionline_time[$key] - time() > 100) {
            unset($USER->koperebionline_id);
            unset($USER->koperebionline_time);
        }
    }

    if (!isset($USER->koperebionline_id[$key])) {
        $lastip = local_kopere_bi_getremoteaddr();

        $dataagent = access_analyze::agent();
        $dataip = local_kopere_bi_iplookup_find_location($lastip);

        $koperebionline = (object)[
            "userid" => $USER->id,
            "courseid" => $COURSE->id,
            "moduleid" => $moduleid,
            "seconds" => 0,
            "currenttime" => time(),

            "client_type" => $dataagent->client_type,
            "client_name" => $dataagent->client_name,
            "client_version" => $dataagent->client_version,
            "os_name" => $dataagent->os_name,
            "os_version" => $dataagent->os_version,

            "lastip" => $lastip,
            "city_name" => $dataip->city,
            "country_name" => $dataip->country,
            "country_code" => isset($dataip->country_code) ? $dataip->country_code : $dataip->country,
            "latitude" => $dataip->latitude,
            "longitude" => $dataip->longitude,
        ];
        try {
            $koperebionlineid = $DB->insert_record("local_kopere_bi_online", $koperebionline);
            $USER->koperebionline_id[$key] = $koperebionlineid;
            $USER->koperebionline_time[$key] = time();
        } catch (dml_exception $e) {
            $e->getMessage();
        }
    }

    if (isset($USER->koperebionline_id[$key])) {
        $PAGE->requires->js_call_amd("local_kopere_bi/online", "init", [$USER->koperebionline_id[$key], $key]);
    }
    $PAGE->requires->js_call_amd("local_kopere_bi/mod_koperebi", "init");
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
 *
 * @return object
 */
function local_kopere_bi_iplookup_find_location($ip) {
    global $CFG;

    require_once("{$CFG->dirroot}/iplookup/lib.php");

    $cache = \cache::make("local_kopere_bi", "ip_user_location");

    if ($cache->has($ip)) {
        $dataip = $cache->get($ip);
    } else {
        $dataip = (object)iplookup_find_location($ip);
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
        global $CFG;
        $koperebiloaded = true;

        get_kopere_lang();

        require_once("{$CFG->dirroot}/local/kopere_dashboard/autoload-lang-js.php");

        return "";
    }

    return "";
}

/**
 *
 * @param $pageid
 *
 * @return string
 * @throws \ScssPhp\ScssPhp\Exception\SassException
 * @throws coding_exception
 * @throws dml_exception
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
            $text .= (new \local_kopere_bi\util\preview_util())->details_block($koperebiblock);
        }
    }

    $text .= "</div>";
    $text .= "</div>";
    $text .= "</div>";
    $text .= "</div>";

    return $text;
}

/**
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
