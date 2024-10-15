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

namespace local_kopere_bi\external;

use context_system;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use local_kopere_bi\util\filter;
use local_kopere_bi\util\string_util;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_page;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Class page_html
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_html extends external_api {
    /**
     * Parâmetros recebidos pelo webservice
     *
     * @return external_function_parameters
     */
    public static function api_parameters() {
        return new external_function_parameters([
            "page_id" => new external_value(PARAM_INT, 'The online id'),
        ]);
    }

    /**
     * Identificador do retorno do webservice
     *
     * @return external_single_structure
     */
    public static function api_returns() {
        return new external_single_structure([
            "html" => new external_value(PARAM_RAW, "HTML of Kopere BI", VALUE_REQUIRED),
        ]);
    }

    /**
     * Define se o endpoint é chamado via AJAX
     *
     * @return bool
     */
    public static function api_is_allowed_from_ajax() {
        return true;
    }

    /**
     * API para contabilizar o tempo gasto na plataforma pelos usuários
     *
     * @param $onlineid
     *
     * @return array
     * @throws \Exception
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    public static function api($pageid) {
        global $DB, $CFG, $OUTPUT, $PAGE;

        require_once("{$CFG->dirroot}/local/kopere_dashboard/autoload.php");
        require_once("{$CFG->dirroot}/local/kopere_bi/lib.php");

        $text = "";

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
        $text .= "FIMMMMMMMMMMMMMMMMMMMMMM";

        $PAGE->set_pagelayout("print");
        $PAGE->set_context(context_system::instance());

        $return = "";
        $return .= $OUTPUT->header();
        $return .= $text;
        $return .= $OUTPUT->footer();

        $parte1 = "M\.util\.js_pending\('local_kopere_(dashboard|bi)\/.*?'\);";
        $parte2 = "require\(\['local_kopere_(dashboard|bi)\/.*?js_complete.*?\}\);";

        preg_match_all("/({$parte1}|{$parte2})/s", $return, $returnitens);

        $return = preg_replace('/.*(<div class=\'kopere_dashboard_div\')/s', '$1', $return);
        $return = preg_replace('/FIMMMMMMMMMMMMMMMMMMMMMM.*/s', "", $return);

        $js = "\n";
        foreach ($returnitens[0] as $returnitem) {
            $js .= "{$returnitem}\n";
        }

        $return .= load_kopere_bi_assets();
        $return .= "\n\n<script>{$js}</script>";

        return ["html" => $return];
    }
}
