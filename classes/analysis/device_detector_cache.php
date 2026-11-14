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

namespace local_kopere_bi\analysis;

use cache;
use DeviceDetector\Cache\Cache as detector_cache;

/**
 * MUC-backed cache for parsed DeviceDetector YAML regex fixtures.
 *
 * Loaded after the vendored DeviceDetector autoloader in access::agent().
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class device_detector_cache implements detector_cache {
    /** @var cache */
    private $cache;

    /**
     * Initialise the Moodle application cache for regular expressions.
     */
    public function __construct() {
        $this->cache = cache::make("local_kopere_bi", "device_detector_regexes");
    }

    /**
     * Normalise the upstream cache key for MUC.
     *
     * @param string $id Upstream cache identifier.
     * @return string
     */
    private static function key($id): string {
        return hash("sha256", (string)$id);
    }

    /**
     * Fetch a cached regex fixture.
     *
     * @param string $id Cache identifier.
     * @return mixed False on miss.
     */
    public function fetch($id) {
        return $this->cache->get(self::key($id));
    }

    /**
     * Check whether the fixture exists.
     *
     * @param string $id Cache identifier.
     * @return bool
     */
    public function contains($id) {
        return $this->fetch($id) !== false;
    }

    // phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase -- Vendor interface uses lifeTime.
    /**
     * Save a parsed fixture.
     *
     * @param string $id Cache identifier.
     * @param mixed $data Parsed rules.
     * @param int $lifeTime Lifetime in seconds, as required by DeviceDetector's interface.
     * @return bool
     */
    public function save($id, $data, $lifeTime = 0) {
        return $this->cache->set(self::key($id), $data);
    }
    // phpcs:enable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase

    /**
     * Remove one fixture.
     *
     * @param string $id Cache identifier.
     * @return bool
     */
    public function delete($id) {
        return $this->cache->delete(self::key($id));
    }

    // phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod -- Required by vendor interface.
    /**
     * Purge all parser regex fixtures for this cache definition.
     *
     * @return bool
     */
    public function flushAll() {
        $this->cache->purge();
        return true;
    }
    // phpcs:enable moodle.NamingConventions.ValidFunctionName.LowercaseMethod
}
