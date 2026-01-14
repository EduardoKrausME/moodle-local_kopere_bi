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
 * maps index file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");

require_login();

$wwwroot = optional_param('wwwroot', '', PARAM_TEXT);
$resource = optional_param('resource', '', PARAM_TEXT);
$tema = optional_param('tema', '', PARAM_TEXT);

$css = file_get_contents(__DIR__ . "/css/style.css");
if ($tema != 'dark') {
    $css .= "
        <style>
            body, html {
                background : #AAD3E1;
            }
            #mapa-online .terra {
                fill   : #F2EFE7;
                stroke : #E9DEDF;
            }
            #mapa-online .terra-outros {
                fill   : #3636366e;
                stroke : #00000038;
            }
        </style>";
}
$icone = file_get_contents(__DIR__ . '/image/loading.svg');
$all = file_get_contents(__DIR__ . "/js/all.min.js");

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>" . get_string('maps_name', 'biblocks_maps') . "</title>
    <style>{$css}</style>
</head>
<body>
<input type='hidden' id='maps_1_city' value='" . get_string('maps_1_city', 'biblocks_maps') . "' />
<input type='hidden' id='maps_many_city' value='" . get_string('maps_many_city', 'biblocks_maps') . "' />
<input type='hidden' id='maps_onlines' value='" . get_string('maps_onlines', 'biblocks_maps') . "' />
<input type='hidden' id='maps_online' value='" . get_string('maps_online', 'biblocks_maps') . "' />

<div id='mapa-online'></div>

<div id='loading'>{$icone}</div>

<script>
    urlMapsData = '{$wwwroot}';
    urlResource = '{$resource}';
    {$all}
</script>

</body>
</html>";
