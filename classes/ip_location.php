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
 * IP location resolver and persistence.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use dml_exception;
use stdClass;

/**
 * Stores one location per IP and links online records to it.
 */
class ip_location {

    /** Pending lookup. */
    public const STATUS_PENDING = 0;

    /** Location resolved successfully. */
    public const STATUS_RESOLVED = 1;

    /** Private, reserved or otherwise non-routable IP. */
    public const STATUS_UNRESOLVABLE = 2;

    /** Temporary lookup error. */
    public const STATUS_RETRY = 3;

    /** Do not retry a failed lookup from page AJAX more than once per hour. */
    private const RETRY_AFTER = HOURSECS;

    /**
     * Finds a previously stored IP without calling any external service.
     *
     * @param string $ip
     * @return stdClass|false
     */
    public static function find(string $ip) {
        global $DB;

        $ip = self::normalise_ip($ip);
        if ($ip === '') {
            return false;
        }

        return $DB->get_record('local_kopere_bi_iplocation', ['ip' => $ip]);
    }

    /**
     * Resolves the IP used by an online record and links all matching online records.
     *
     * @param int $onlineid
     * @param bool $force Force retry even when the last attempt was recent.
     * @return stdClass|false
     */
    public static function resolve_online_record(int $onlineid, bool $force = false) {
        global $DB;

        $online = $DB->get_record('local_kopere_bi_online', ['id' => $onlineid], 'id,lastip,iplocationid');
        if (!$online || empty($online->lastip)) {
            return false;
        }

        return self::resolve($online->lastip, $force);
    }

    /**
     * Gets or creates the location row and, when necessary, calls the external IP API.
     *
     * Database is always checked before the external service. The lock prevents several
     * simultaneous users behind the same NAT from resolving the same IP at once.
     *
     * @param string $ip
     * @param bool $force Force retry even when the last attempt was recent.
     * @return stdClass|false
     */
    public static function resolve(string $ip, bool $force = false) {
        global $DB;

        $ip = self::normalise_ip($ip);
        if ($ip === '') {
            return false;
        }

        $location = self::find($ip);
        if ($location) {
            self::link_online_records($ip, (int)$location->id);

            if ((int)$location->status === self::STATUS_RESOLVED ||
                    (int)$location->status === self::STATUS_UNRESOLVABLE) {
                return $location;
            }

            if (!$force && (int)$location->lastattempt > time() - self::RETRY_AFTER) {
                return $location;
            }
        }

        $factory = \core\lock\lock_config::get_lock_factory('local_kopere_bi_iplocation');
        $lock = $factory->get_lock(hash('sha256', $ip), 0);
        if (!$lock) {
            return $location ?: false;
        }

        try {
            // Another request may have resolved the IP while this request was waiting for the lock.
            $location = self::find($ip);
            if ($location) {
                self::link_online_records($ip, (int)$location->id);

                if ((int)$location->status === self::STATUS_RESOLVED ||
                        (int)$location->status === self::STATUS_UNRESOLVABLE) {
                    return $location;
                }

                if (!$force && (int)$location->lastattempt > time() - self::RETRY_AFTER) {
                    return $location;
                }
            } else {
                $now = time();
                $location = (object)[
                    'ip' => $ip,
                    'city_name' => null,
                    'country_name' => null,
                    'country_code' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'status' => self::STATUS_PENDING,
                    'lastattempt' => 0,
                    'timecreated' => $now,
                    'timemodified' => $now,
                ];

                try {
                    $location->id = $DB->insert_record('local_kopere_bi_iplocation', $location);
                } catch (dml_exception $e) {
                    // The unique IP index protects against a race even if the lock backend is unavailable across nodes.
                    $location = self::find($ip);
                    if (!$location) {
                        throw $e;
                    }
                }

                self::link_online_records($ip, (int)$location->id);
            }

            if (!self::is_public_ip($ip)) {
                $location->status = self::STATUS_UNRESOLVABLE;
                $location->lastattempt = time();
                $location->timemodified = time();
                $DB->update_record('local_kopere_bi_iplocation', $location);
                return $location;
            }

            $location->lastattempt = time();
            $location->timemodified = time();
            $data = self::request_location($ip);

            if (!$data) {
                $location->status = self::STATUS_RETRY;
                $DB->update_record('local_kopere_bi_iplocation', $location);
                return $location;
            }

            $location->city_name = $data->city ?? null;
            $location->country_name = $data->country ?? null;
            $location->country_code = $data->countryCode ?? null;
            $location->latitude = isset($data->lat) ? (string)$data->lat : null;
            $location->longitude = isset($data->lon) ? (string)$data->lon : null;
            $location->status = self::STATUS_RESOLVED;
            $location->timemodified = time();

            $DB->update_record('local_kopere_bi_iplocation', $location);
            self::link_online_records($ip, (int)$location->id);

            return $location;
        } finally {
            $lock->release();
        }
    }

    /**
     * Links all not-yet-linked online rows for the same IP using an integer relation.
     *
     * @param string $ip
     * @param int $locationid
     * @return void
     */
    private static function link_online_records(string $ip, int $locationid): void {
        global $DB;

        $DB->set_field_select(
            'local_kopere_bi_online',
            'iplocationid',
            $locationid,
            'lastip = :ip AND iplocationid IS NULL',
            ['ip' => $ip]
        );
    }

    /**
     * Calls the external service. This method is never used by the output hook.
     *
     * @param string $ip
     * @return stdClass|false
     */
    private static function request_location(string $ip) {
        $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,message,country,countryCode,city,lat,lon,query';
        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false || $response === '') {
            return false;
        }

        $data = json_decode($response);
        if (!is_object($data) || ($data->status ?? '') !== 'success') {
            return false;
        }

        return $data;
    }

    /**
     * Checks if an IP is globally routable before sending it to an external service.
     *
     * @param string $ip
     * @return bool
     */
    private static function is_public_ip(string $ip): bool {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    /**
     * Normalises and validates the textual IP representation.
     *
     * @param string $ip
     * @return string
     */
    private static function normalise_ip(string $ip): string {
        $ip = trim($ip);
        if ($ip === '' || strlen($ip) > 45 || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return '';
        }

        return $ip;
    }
}
