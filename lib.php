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
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kopere_bi\block\util\preview_util;
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
 * @throws \core\exception\coding_exception
 */
function local_kopere_bi_iplookup_find_location($ip) {
    $cache = cache::make("local_kopere_bi", "ip_user_location");

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

    return (object) $dataip;
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

        local_kopere_dashboard\datatable\datatable_init::lang();

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
    global $DB;

    $text = load_kopere_bi_assets();

    $text .= "<div class='kopere_bi_div'>";
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
            $text .= (new preview_util())->details_block($koperebiblock);
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
    $text = load_kopere_bi_assets();
    $text .= "<div class='kopere_bi_div-ajax'
                   id='kopere_bi_div-coursemodule_{$coursemoduleid}'
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
            "classname" => "dashboard",
            "method" => "preview",
            "page_id" => $koperebipage->id,
            "courseid" => $course->id,
        ];

        $url = new moodle_url("/local/kopere_bi/index.php", $params);
        $name = $pluginname . " - " . string_util::get_string($koperebipage->title);
        $settingsnode = navigation_node::create($name, $url, navigation_node::TYPE_SETTING);
        if (isset($settingsnode)) {
            $reportnode->add_node($settingsnode);
        }
    }
}

/**
 * Resolves the class name received from the URL into a local_kopere_bi class.
 *
 * @param string $rawclassname
 * @return array
 * @throws moodle_exception
 */
function local_kopere_bi_resolve_classname($rawclassname) {
    $classname = str_replace("-", "_", trim($rawclassname));

    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $classname)) {
        throw new moodle_exception("invalidrequest", "error", "", null, "Invalid class name");
    }

    $fqcn = "\\local_kopere_bi\\{$classname}";
    if (!class_exists($fqcn)) {
        throw new moodle_exception("class_not_found", "local_kopere_bi");
    }

    return [$classname, $fqcn];
}

/**
 * Validates the method name received from the URL.
 *
 * @param string $method
 * @return void
 * @throws moodle_exception
 */
function local_kopere_bi_validate_method($method) {
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $method) || strpos($method, "__") === 0) {
        throw new moodle_exception("invalidrequest", "error", "", null, "Invalid method name");
    }
}

/**
 * Requires the correct capability for the requested route.
 *
 * @param string $classname
 * @param string $method
 * @param context_system $context
 * @return void
 * @throws required_capability_exception
 */
function local_kopere_bi_require_route_capability($classname, $method, context_system $context) {
    $viewroutes = [
        "dashboard" => ["start", "preview", "type_block_preview"],
        "chart_data" => ["load_data"],
    ];

    if (isset($viewroutes[$classname]) && in_array($method, $viewroutes[$classname], true)) {
        require_capability("local/kopere_bi:view", $context);
        return;
    }

    require_capability("local/kopere_bi:manage", $context);
}

/**
 * Dispatches the current request.
 *
 * @param string $rawclassname
 * @param string $method
 * @param context_system $context
 * @return string
 * @throws moodle_exception
 * @throws \required_capability_exception
 */
function local_kopere_bi_dispatch($rawclassname, $method, context_system $context) {
    [$classname, $fqcn] = local_kopere_bi_resolve_classname($rawclassname);
    local_kopere_bi_validate_method($method);
    local_kopere_bi_require_route_capability($classname, $method, $context);

    $instance = new $fqcn();
    if (!is_callable([$instance, $method])) {
        throw new moodle_exception("invalidrequest", "error", "", null, "Method not found");
    }

    return $instance->{$method}();
}

if (!function_exists('str_ends_with')) {
    /**
     * Function str_ends_with
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function str_ends_with($haystack, $needle) {
        if ('' === $needle || $needle === $haystack) {
            return true;
        }

        if ('' === $haystack) {
            return false;
        }

        $needlelength = strlen($needle);

        return $needlelength <= strlen($haystack) && 0 === substr_compare($haystack, $needle, -$needlelength);
    }
}
