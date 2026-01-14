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
 * Plugininfo for bifilters_* subplugins.
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\plugininfo;

use admin_setting_configcheckbox;
use admin_settingpage;
use context_system;
use core\plugininfo\base;
use core_component;
use Exception;
use moodle_url;
use part_of_admin_tree;
use progress_trace;

/**
 * Manages admin UI + enable/disable + uninstall for bifilters_* subplugins.
 *
 * Enable/disable model:
 *   component = "bifilters_<name>"  |  key = "enabled" (1/0, default 1)
 */
class bifilters extends base {
    /**
     * Return enabled status for all bifilters subplugins.
     * Keys are raw plugin names (e.g. "html", "course"); values are booleans.
     *
     * @return array<string,bool>
     * @throws Exception
     */
    public static function get_enabled_plugins() {
        $plugins = core_component::get_plugin_list("bifilters");
        return $plugins;
    }

    /**
     * Whether the subplugin can be uninstalled via admin UI.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        $protected = ["bifilters_user", "bifilters_course"];

        if (in_array($this->component, $protected)) {
            return false;
        }
        return true;
    }

    /**
     * Return settings section name used in the admin tree (optional).
     *
     * @return string
     */
    public function get_settings_section_name() {
        return "bifilters_" . $this->name . "_settings";
    }

    /**
     * Add a simple Enable/Disable checkbox for this subplugin in the admin tree.
     *
     * @param part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig
     * @return void
     * @throws Exception
     */
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        if (!$hassiteconfig) {
            return;
        }

        $section = $this->get_settings_section_name();
        $page = new admin_settingpage($section, $this->displayname, "moodle/site:config");

        $component = "bifilters_" . $this->name;
        $page->add(
            new admin_setting_configcheckbox(
                "{$component}/enabled", get_string("pluginname", $component),     // Subplugin"s own display name.
                get_string("enableplugin", "admin"),      // Core admin string.
                1
            )
        );

        $adminroot->add($parentnodename, $page);
    }

    /**
     * Uninstall hook: remove items that belong to this subplugin and clear its config.
     * Does not drop core tables; only cleans items and files tied to this subplugin.
     *
     * @param progress_trace $progress
     * @return bool
     * @throws Exception
     */
    public function uninstall(progress_trace $progress) {
        global $DB;

        $component = "bifilters_" . $this->name;

        // Remove Indicadores items that reference this subplugin.
        $items = $DB->get_records("local_kopere_bi_item", ["component" => $component], "id ASC", "id");
        if ($items) {
            $sysctx = context_system::instance();
            $fs = get_file_storage();

            // Purge files used by the HTML editor area for these items.
            foreach ($items as $it) {
                $fs->delete_area_files($sysctx->id, "local_kopere_bi", "itemhtml", $it->id);
            }

            [$insql, $inparams] = $DB->get_in_or_equal(array_keys($items), SQL_PARAMS_QM);
            $DB->delete_records_select("local_kopere_bi_item", "id " . $insql, $inparams);
            $progress->output("Removed " . count($items) . " Indicadores items for component " . $component, 1);
        }

        // Clear all component config (including "enabled").
        unset_all_config_for_plugin($component);
        $progress->output("Cleared config for " . $component, 1);

        return true;
    }

    /**
     * Return URL used for management of plugins of this type.
     *
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url("/local/kopere_bi/admin_plugins.php?subtype=bifilters");
    }
}
