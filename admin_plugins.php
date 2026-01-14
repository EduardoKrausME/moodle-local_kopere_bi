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
 * Print the table of all installed local Kopere Bi plugins
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");
require_once("{$CFG->libdir}/adminlib.php");
require_once("{$CFG->libdir}/tablelib.php");

$subtype = required_param("subtype", PARAM_PLUGIN);
$action = optional_param("action", null, PARAM_PLUGIN);
$plugin = optional_param("plugin", null, PARAM_PLUGIN);

$params = ["subtype" => $subtype, "action" => $action, "plugin" => $plugin];
$PAGE->set_url(new moodle_url("/local/kopere_bi/admin_plugins.php", $params));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string("subplugintype_{$subtype}_title", "local_kopere_bi"));

require_admin();

admin_externalpage_setup("managelocalplugins");

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("subplugintype_{$subtype}_title", "local_kopere_bi"));

$table = new flexible_table("localplugins_administration_table");
$table->define_columns(["name", "version", "uninstall"]);
$table->define_headers([get_string("plugin"), get_string("version"), get_string("uninstallplugin", "core_admin")]);
$table->define_baseurl($PAGE->url);
$table->set_attribute("id", "localplugins");
$table->set_attribute("class", "admintable generaltable");
$table->setup();

$plugins = [];
foreach (core_component::get_plugin_list($subtype) as $plugin => $plugindir) {
    if (get_string_manager()->string_exists("pluginname", "{$subtype}_{$plugin}")) {
        $strpluginname = get_string("pluginname", "{$subtype}_{$plugin}");
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
core_collator::asort($plugins);

foreach ($plugins as $plugin => $name) {
    $uninstall = "";
    if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url("{$subtype}_{$plugin}", "manage")) {
        $uninstall = html_writer::link($uninstallurl, get_string("uninstallplugin", "core_admin"));
    }

    $version = get_config("{$subtype}_{$plugin}");
    if (!empty($version->version)) {
        $version = $version->version;
    } else {
        $version = "?";
    }

    $table->add_data([$name, $version, $uninstall]);
}

$table->finish_html();

echo $OUTPUT->footer();
