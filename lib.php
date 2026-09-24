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
 * Backwards-compatible IP lookup helper.
 *
 * This compatibility helper is database-only. External resolution is performed asynchronously by the online AJAX
 * flow or by the scheduled task, so calling this helper can never delay page rendering.
 *
 * @param string $ip
 * @return object
 */
function local_kopere_bi_iplookup_find_location($ip) {
    $location = \local_kopere_bi\ip_location::find((string)$ip);
    if (!$location) {
        return (object)[];
    }

    return (object)[
        "query" => $location->ip ?? $ip,
        "city" => $location->city_name ?? null,
        "country" => $location->country_name ?? null,
        "country_code" => $location->country_code ?? null,
        "latitude" => $location->latitude ?? null,
        "longitude" => $location->longitude ?? null,
    ];
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
