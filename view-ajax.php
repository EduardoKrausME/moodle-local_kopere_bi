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
 * Ajax Kopere BI dispatcher.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kopere_dashboard\util\json;

define("AJAX_SCRIPT", true);
define("NO_DEBUG_DISPLAY", true);

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/lib.php");

global $PAGE;

try {
    $context = context_system::instance();
    require_login();
    require_capability("local/kopere_bi:view", $context);

    $classname = required_param("classname", PARAM_ALPHANUMEXT);
    $method = required_param("method", PARAM_ALPHANUMEXT);

    $PAGE->set_url(new moodle_url("/local/kopere_bi/view-ajax.php", [
        "classname" => $classname,
        "method" => $method,
    ]));
    $PAGE->set_context($context);
    $PAGE->set_pagetype("local-kopere-bi-ajax");

    session_write_close();

    local_kopere_bi_dispatch($classname, $method, $context);
} catch (Throwable $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    json::error($e->getMessage());
}

exit;
