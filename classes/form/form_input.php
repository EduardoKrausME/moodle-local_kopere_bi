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

namespace local_kopere_bi\form;

/**
 * Small input descriptor used by dynamic_moodleform.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class form_input {
    /** @var string */
    protected $type = 'text';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $name = '';

    /** @var mixed */
    protected $value = '';

    /** @var bool */
    protected $required = false;

    /** @var array */
    protected $values = [];

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $style = '';

    /** @var array */
    protected $extras = [];

    /**
     * Create a new input descriptor.
     *
     * @return static
     */
    public static function new_instance() {
        return new static();
    }

    /**
     * Set the HTML/input type.
     *
     * @param string $type
     * @return $this
     */
    public function set_type($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the field title.
     *
     * @param string $title
     * @return $this
     */
    public function set_title($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the field name.
     *
     * @param string $name
     * @return $this
     */
    public function set_name($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the default field value.
     *
     * @param mixed $value
     * @return $this
     */
    public function set_value($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Mark the field as required.
     *
     * @return $this
     */
    public function set_required() {
        $this->required = true;
        return $this;
    }

    /**
     * Set select options.
     *
     * @param array $values
     * @param string $keyfield
     * @param string $valuefield
     * @return $this
     */
    public function set_values($values, $keyfield = 'key', $valuefield = 'value') {
        $options = [];
        foreach ($values as $key => $item) {
            if (is_object($item)) {
                $optionkey = isset($item->{$keyfield}) ? $item->{$keyfield} : $key;
                $optionvalue = isset($item->{$valuefield}) ? $item->{$valuefield} : $optionkey;
            } else if (is_array($item)) {
                $optionkey = array_key_exists($keyfield, $item) ? $item[$keyfield] : $key;
                $optionvalue = array_key_exists($valuefield, $item) ? $item[$valuefield] : $optionkey;
            } else {
                $optionkey = $key;
                $optionvalue = $item;
            }
            $options[$optionkey] = $optionvalue;
        }
        $this->values = $options;
        return $this;
    }

    /**
     * Set help/description text.
     *
     * @param string $description
     * @return $this
     */
    public function set_description($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Set inline style attributes kept from the previous form builder.
     *
     * @param string $style
     * @return $this
     */
    public function set_style($style) {
        $this->style = $style;
        return $this;
    }

    /**
     * Add a raw attribute string kept from the previous form builder.
     *
     * @param string $extra
     * @return $this
     */
    public function add_extras($extra) {
        $this->extras[] = $extra;
        return $this;
    }

    /**
     * Function get_type
     *
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Function get_title
     *
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Function get_name
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Function get_value
     *
     * @return mixed|string
     */
    public function get_value() {
        return $this->value;
    }

    /**
     * Function is_required
     *
     * @return bool
     */
    public function is_required() {
        return $this->required;
    }

    /**
     * Function get_values
     *
     * @return array
     */
    public function get_values() {
        return $this->values;
    }

    /**
     * Function get_description
     *
     * @return string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Function get_style
     *
     * @return string
     */
    public function get_style() {
        return $this->style;
    }

    /**
     * Function get_extras
     *
     * @return array
     */
    public function get_extras() {
        return $this->extras;
    }
}
