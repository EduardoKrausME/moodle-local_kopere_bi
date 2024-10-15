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
 * Class renderer_bi_mustache
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi\output;

use core\output\mustache_pix_helper;
use core\output\mustache_quote_helper;
use core\output\mustache_shorten_text_helper;
use core\output\mustache_string_helper;
use core\output\mustache_uniqid_helper;
use core\output\mustache_user_date_helper;
use local_kopere_bi\util\string_util;
use Mustache_Engine;


/**
 * Class renderer_bi_mustache
 *
 * @package local_kopere_bi\output
 */
class renderer_bi_mustache extends Mustache_Engine {

    /**
     * Function new_instance
     *
     * @return renderer_bi_mustache
     */
    public static function new_instance() {
        return new renderer_bi_mustache();
    }

    /**
     * renderer_bi_mustache constructor.
     */
    public function __construct() {
        global $PAGE, $OUTPUT;

        $themerev = theme_get_revision();
        parent::__construct([
            "cache" => make_localcache_directory("mustache/{$themerev}/{$PAGE->theme->name}"),
            "escape" => "s",
            "helpers" => [
                "str" => [
                    new mustache_string_helper(),
                    "str",
                ],
                "quote" => [
                    new mustache_quote_helper(),
                    "quote",
                ],
                "shortentext" => [
                    new mustache_shorten_text_helper(),
                    "shorten",
                ],
                "pix" => [
                    new mustache_pix_helper($OUTPUT),
                    "pix",
                ],
                "userdate" => [
                    new mustache_user_date_helper(),
                    "transform",
                ],
                "sqloneitem" => [
                    new mustache_sql_oneitem_helper(),
                    "execute",
                ],
                "uniqid", new mustache_uniqid_helper(),

            ],
            "pragmas" => [Mustache_Engine::PRAGMA_BLOCKS],
        ]);
    }

    /**
     * Var template
     *
     * @var string
     */
    public $template;

    /**
     * Function render_from_string
     *
     * @param $template
     * @param array $context
     * @param string $class
     *
     * @return string
     * @throws \coding_exception
     */
    public function render_from_string($template, $context = [], $class = null) {
        global $PAGE, $OUTPUT;

        if (!isset($template[3])) {
            return $template;
        }
        $template = string_util::get_string($template);

        if ($class) {
            $template = "<div class='{$class}'>{$template}</div>";
        }

        $this->template = $template;

        if (!is_array($context)) {
            $context = (array)$context;
        }

        $context["globals"] = [
            "config" => $PAGE->requires->get_config_for_javascript($PAGE, $OUTPUT),
        ];

        if (strpos($this->template, "{{#sql") === false) {
            $cache = \cache::make("local_kopere_bi", "mustache_nosql");
        } else {
            $cache = \cache::make("local_kopere_bi", "mustache_sql");
        }

        $cacheid = md5($this->template);
        if ($cache->has($cacheid)) {
            return $cache->get($cacheid);
        }

        $html = $this->render($this->template, $context);

        $cache->set($cacheid, $html);

        return $html;
    }

    /**
     * phpcs:disable
     * Function getTemplateClassName
     *
     * @param \Mustache_Source|string $source
     *
     * @return string
     */
    public function getTemplateClassName($source) {
        return "__Mustache_" . md5($this->template);
    }
}
