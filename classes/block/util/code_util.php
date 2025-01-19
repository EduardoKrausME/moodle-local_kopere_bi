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

namespace local_kopere_bi\block\util;

use local_kopere_bi\util\sql_util;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_select;
use local_kopere_dashboard\html\inputs\input_textarea;

/**
 * Class code_util
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class code_util {

    /**
     * Function input_commandsql
     *
     * @param form $form
     * @param $koperebielement
     * @param bool $iscache
     * @param bool $isreload
     *
     * @throws \Exception
     */
    public static function input_commandsql(form $form, $koperebielement, $iscache = true, $isreload = true) {
        global $PAGE;

        $commandsql = $koperebielement->commandsql;
        $commandsql = str_replace("\t", "    ", $commandsql);

        $form->add_input(
            input_textarea::new_instance()
                ->set_title("SQL")
                ->set_name("commandsql")
                ->set_value($commandsql)
                ->set_description(sql_util::chaves_replace()));
        $PAGE->requires->js_call_amd("local_kopere_bi/ace", "load", ["commandsql", "sql"]);

        if ($iscache) {
            $form->add_input(
                input_select::new_instance()
                    ->set_title(get_string("cache_time", "local_kopere_bi"))
                    ->set_name("cache")
                    ->set_value(@$koperebielement->cache)
                    ->set_description(get_string("cache_time_desc", "local_kopere_bi"))
                    ->set_values([
                        ["key" => "none", "value" => get_string("cache_time_no", "local_kopere_bi")],
                        ["key" => "15m", "value" => get_string("cache_time_15min", "local_kopere_bi")],
                        ["key" => "30m", "value" => get_string("cache_time_30min", "local_kopere_bi")],
                        ["key" => "1h", "value" => get_string("cache_time_1h", "local_kopere_bi")],
                        ["key" => "6h", "value" => get_string("cache_time_6h", "local_kopere_bi")],
                        ["key" => "1d", "value" => get_string("cache_time_1d", "local_kopere_bi")],
                    ]));
        }

        if ($isreload) {
            $form->add_input(
                input_select::new_instance()
                    ->set_title(get_string("reload_time", "local_kopere_bi"))
                    ->set_name("reload")
                    ->set_value(@$koperebielement->reload)
                    ->set_description(get_string("reload_time_desc", "local_kopere_bi"))
                    ->set_values([
                        ["key" => "none", "value" => get_string("reload_time_none", "local_kopere_bi")],
                        ["key" => "1m", "value" => get_string("reload_time_1m", "local_kopere_bi")],
                        ["key" => "5m", "value" => get_string("reload_time_5m", "local_kopere_bi")],
                        ["key" => "10m", "value" => get_string("reload_time_10m", "local_kopere_bi")],
                        ["key" => "20m", "value" => get_string("reload_time_20m", "local_kopere_bi")],
                        ["key" => "30m", "value" => get_string("reload_time_30m", "local_kopere_bi")],
                        ["key" => "40m", "value" => get_string("reload_time_40m", "local_kopere_bi")],
                        ["key" => "50m", "value" => get_string("reload_time_50m", "local_kopere_bi")],
                        ["key" => "1h", "value" => get_string("reload_time_1h", "local_kopere_bi")],
                        ["key" => "2h", "value" => get_string("reload_time_2h", "local_kopere_bi")],
                    ]));
        }
    }

    /**
     * Function estilo
     *
     * @param form $form
     * @param $koperebielement
     *
     * @throws \Exception
     */
    public static function estilo(form $form, $koperebielement) {
        global $PAGE;

        $collapsed = "collapsed";
        if (isset($koperebielement->css[2]) ||
            isset($koperebielement->html_before[2]) ||
            isset($koperebielement->html_after[2])) {
            $collapsed = "";
        }

        echo "<div class=mform>
                  <fieldset id='campo_chart_estilo-fieldset' class='clearfix collapsible {$collapsed}'>
                      <legend>
                          <a href='#' class='btn-icon'>
                              <i class='icon fa fa-chevron-right fa-fw'></i>
                              " . get_string("extra_options", "local_kopere_bi") . "
                          </a>
                      </legend>
                      <div class='fcontainer clearfix'>";
        $PAGE->requires->js_call_amd("local_kopere_bi/theme", "collapse_style");

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("css_extra", "local_kopere_bi"))
                ->set_style("width:100%;height:60px;font-family:monospace;white-space:nowrap;")
                ->set_name("css")
                ->set_value(@$koperebielement->css)
                ->set_description(get_string("css_extra_desc", "local_kopere_bi")));
        $PAGE->requires->js_call_amd("local_kopere_bi/ace", "load", ["css", "css"]);

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("html_before", "local_kopere_bi"))
                ->set_name("html_before")
                ->set_value(@$koperebielement->html_before)
                ->set_style("height:70px"));

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("html_after", "local_kopere_bi"))
                ->set_name("html_after")
                ->set_value(@$koperebielement->html_after)
                ->set_style("height:70px"));

        echo "</div></div></fieldset>";

        $PAGE->requires->js_call_amd("local_kopere_bi/ace", "load", ["html_before", "html"]);
        $PAGE->requires->js_call_amd("local_kopere_bi/ace", "load", ["html_after", "html"]);
        $PAGE->requires->js_call_amd("local_kopere_bi/theme", "changue", ["Amostra"]);
    }

    /**
     * Function options
     *
     * @param form $form
     * @param $value
     *
     * @throws \Exception
     */
    public static function options(form $form, $value) {
        global $PAGE;
        echo "
              <div class=mform>
                  <fieldset id='campo_chart_options-fieldset' class='clearfix collapsible collapsed'>
                      <legend>
                          <a href='#' class='btn-icon'>
                              <i class='icon fa fa-chevron-right fa-fw'></i>
                              " . get_string("block_extra", "local_kopere_bi") . "
                          </a>
                      </legend>
                      <div class='fcontainer clearfix'>";

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("setting_apex", "local_kopere_bi"))
                ->set_style("width:100%;font-family:monospace;white-space:nowrap;")
                ->set_name("info[chart_options]")
                ->set_value($value)
                ->set_description(get_string("setting_apex_desc", "local_kopere_bi")));

        echo "</div></div></fieldset>";

        $PAGE->requires->js_call_amd("local_kopere_bi/ace", "load", ["infochart_options", "json5"]);
        $PAGE->requires->js_call_amd("local_kopere_bi/theme", "collapse_options");
    }

    /**
     *
     */
    public static function add_js_apexcharts() {
        static $code = false;
        if ($code) {
            return;
        }
        $code = true;

        echo "
            <script src='https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/eligrey-classlist-js-polyfill@1.2.20171210/classList.min.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/findindex_polyfill_mdn'></script>
            <script src='https://cdn.jsdelivr.net/npm/apexcharts'></script>";
    }

    /**
     * Function get_js_theme
     *
     * @param $koperebielement
     *
     * @return string
     * @throws \Exception
     */
    public static function get_js_theme($koperebielement) {
        $extra = "";

        $themepaleta = get_config("local_kopere_bi", "theme_palette");
        if ($themepaleta && $themepaleta != "default") {
            $extra .= "
                var optionsNew1 = {
                    theme : {
                        palette : '{$themepaleta}'
                    }
                };
                options = {...optionsNew1, ...options};";
        }

        if (@$koperebielement->theme != "light") {
            $extra .= "
                var optionsNew2 = {
                    theme   : {
                        mode : 'dark'
                    },
                    chart   : {
                        foreColor : '#CCCCCC'
                    },
                    tooltip : {
                        theme : 'dark'
                    },
                    grid    : {
                        borderColor : '#535A6C'
                    }
                };
                options = {...optionsNew2, ...options};";
        }

        return $extra;
    }

    /**
     * Function get_js_options
     *
     * @param $chartoptions
     *
     * @return string
     */
    public static function get_js_options($chartoptions) {

        $chartoptions = preg_replace('/\s+/', " ", $chartoptions);
        $chartoptions = str_replace('"', "'", $chartoptions);

        if (isset($chartoptions[5])) {
            return "
                try {
                    eval(\"options_custom = {$chartoptions}\");
                    options = {...options_custom, ...options};
                }
                catch (e) {
                    console.error(e)
                }";
        }

        return "";
    }
}
