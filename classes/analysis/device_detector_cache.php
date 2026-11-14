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

    public function __construct() {
        $this->cache = cache::make("local_kopere_bi", "device_detector_regexes");
    }

    private static function key($id): string {
        return hash("sha256", (string)$id);
    }

    public function fetch($id) {
        return $this->cache->get(self::key($id));
    }

    public function contains($id) {
        return $this->fetch($id) !== false;
    }

    public function save($id, $data, $lifeTime = 0) {
        return $this->cache->set(self::key($id), $data);
    }

    public function delete($id) {
        return $this->cache->delete(self::key($id));
    }

    public function flushAll() {
        $this->cache->purge();
        return true;
    }
}
