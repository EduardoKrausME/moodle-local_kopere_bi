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

use Exception;
use local_kopere_bi\local\util\string_util;
use Mustache_Engine;


/**
 * Class renderer_bi_mustache
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
        global $PAGE;

        $themerev = theme_get_revision();
        parent::__construct([
            "cache" => make_localcache_directory("mustache/{$themerev}/{$PAGE->theme->name}"),
            "escape" => "s",
            "helpers" => [
                "str" => function ($text) {
                    $texts = explode(",", $text);
                    $identifier = trim(array_shift($texts));
                    $component = trim(array_shift($texts));

                    if (strpos($identifier, "{{") !== false) {
                        $identifier = $this->render_from_string($identifier, $this->data);
                    }
                    if (strpos($component, "{{") !== false) {
                        $component = $this->render_from_string($component, $this->data);
                    }

                    return get_string($identifier, $component);
                },
                "shortentext" => function ($args) {
                    list($length, $text) = explode(',', $args, 2);
                    $length = trim($length);
                    $text = trim($text);

                    return shorten_text($text, $length);
                },
                "pix" => function ($text) {
                    global $OUTPUT;
                    $key = strtok($text, ",");
                    $key = trim($key);
                    $component = strtok(",");
                    $component = trim($component);
                    if (!$component) {
                        $component = '';
                    }
                    $text = strtok("");
                    $text = htmlspecialchars_decode($text, ENT_COMPAT);

                    return trim($OUTPUT->renderer->pix_icon($key, $text, $component));
                },
                "userdate" => function ($args) {
                    list($timestamp, $format) = explode(',', $args, 2);
                    $timestamp = trim($timestamp);
                    $format = trim($format);

                    return userdate($timestamp, $format);
                },
                "sqloneitem" => [
                    new mustache_sql_oneitem_helper(),
                    "execute",
                ],

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
     * Var data
     *
     * @var array
     */
    public $data;

    /**
     * Function render_from_string
     *
     * @param $template
     * @param array $data
     * @param string $class
     * @return string
     * @throws Exception
     */
    public function render_from_string($template, $data = [], $class = null) {
        global $PAGE, $OUTPUT;

        if (!isset($template[3])) {
            return $template;
        }

        $template = string_util::get_string($template);
        if ($class) {
            $template = "<div class='{$class}'>{$template}</div>";
        }
        $cacheid = md5($template . json_encode($data));
        $this->template = $template;

        if (strpos($this->template, "{{#sql") === false) {
            $cache = \cache::make("local_kopere_bi", "mustache_nosql");
        } else {
            $cache = \cache::make("local_kopere_bi", "mustache_sql");
        }

        if ($cache->has($cacheid)) {
            return $cache->get($cacheid);
        }

        if (!is_array($data)) {
            $data = (array)$data;
        }
        $data["config"] = $PAGE->requires->get_config_for_javascript($PAGE, $OUTPUT);
        $data["uniqid"] = "uniqid_" . uniqid();
        $this->data = $data;

        $html = $this->render($this->template, $data);

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
        return "__StringMustache_00" . md5($this->template);
    }
}
