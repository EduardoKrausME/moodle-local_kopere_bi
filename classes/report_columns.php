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
 * Global, opt-in report columns, independent of report SQL definitions.
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_bi;

use context_course;
use context_system;
use context_user;
use core_course\customfield\course_handler;

/**
 * Enriches tabular datasets without rewriting SQL or modifying saved report elements.
 */
final class report_columns {
    /** @var array<string, string> */
    private const USER_FIELDS = [
        'name' => 'fullname',
        'email' => 'email',
        'username' => 'username',
        'idnumber' => 'idnumber',
        'phone1' => 'phone1',
        'phone2' => 'phone2',
        'city' => 'city',
        'country' => 'country',
        'institution' => 'institution',
        'department' => 'department',
    ];

    /** @var array<string, string> */
    private const COURSE_FIELDS = [
        'fullname' => 'fullname',
        'shortname' => 'shortname',
        'idnumber' => 'idnumber',
        'category' => 'category',
        'startdate' => 'startdate',
        'enddate' => 'enddate',
        'visible' => 'visible',
    ];

    /**
     * Discover the currently available standard and custom fields.
     *
     * @param string $entity 'user' or 'course'.
     * @return array<string, string>
     */
    public static function available(string $entity): array {
        global $DB;

        if ($entity === 'user') {
            $fields = [];
            foreach (self::USER_FIELDS as $key => $unused) {
                $fields[$key] = get_string('reportcols_user_' . $key, 'local_kopere_bi');
            }
            foreach ($DB->get_records('user_info_field', [], 'sortorder ASC, id ASC') as $field) {
                if (preg_match('/^[a-zA-Z0-9_]+$/D', $field->shortname)) {
                    $fields['custom_' . $field->shortname] = get_string(
                        'reportcols_customlabel', 'local_kopere_bi', $field->name
                    );
                }
            }
            return $fields;
        }

        if ($entity === 'course') {
            $fields = [];
            foreach (self::COURSE_FIELDS as $key => $unused) {
                $fields[$key] = get_string('reportcols_course_' . $key, 'local_kopere_bi');
            }
            foreach (course_handler::create()->get_fields() as $field) {
                $shortname = (string)$field->get('shortname');
                if (preg_match('/^[a-zA-Z0-9_]+$/D', $shortname)) {
                    $fields['custom_' . $shortname] = get_string(
                        'reportcols_customlabel', 'local_kopere_bi', (string)$field->get('name')
                    );
                }
            }
            return $fields;
        }

        throw new \InvalidArgumentException('Unknown BI report column entity.');
    }

    /**
     * An unsaved configuration must leave legacy reports exactly as before.
     *
     * @param string $entity
     * @return bool
     */
    public static function enabled(string $entity): bool {
        return get_config('local_kopere_bi', 'reportcols_' . $entity) !== false;
    }

    /**
     * @param string $entity
     * @return string[]
     */
    public static function selected(string $entity): array {
        $raw = get_config('local_kopere_bi', 'reportcols_' . $entity);
        if ($raw === false) {
            return $entity === 'user' ? ['name', 'email'] : ['fullname', 'shortname'];
        }
        $selected = json_decode((string)$raw, true);
        return is_array($selected) ? array_values(array_intersect(
            array_keys(self::available($entity)), array_filter($selected, 'is_string')
        )) : [];
    }

    /**
     * Apply globally to every compatible report. Unknown field IDs are never accepted.
     *
     * @param string[] $user
     * @param string[] $course
     * @return void
     */
    public static function save(array $user, array $course): void {
        require_capability('local/kopere_bi:manage', context_system::instance());
        foreach (['user' => $user, 'course' => $course] as $entity => $chosen) {
            $valid = array_keys(self::available($entity));
            $chosen = array_values(array_unique(array_intersect($valid, $chosen)));
            $mandatory = $entity === 'user' ? 'name' : 'fullname';
            if (!in_array($mandatory, $chosen, true)) {
                array_unshift($chosen, $mandatory);
            }
            set_config('reportcols_' . $entity, json_encode($chosen), 'local_kopere_bi');
        }
        // A cached raw row is harmless; cached Mustache output can contain old identities.
        foreach (['none', '15m', '30m', '1h', '6h', '1d'] as $ttl) {
            \cache::make('local_kopere_bi', 'block_chart_data_' . $ttl)->purge();
        }
    }

    /**
     * The detection is intentionally conservative: an id alone does not mean a user.
     * Built-in and custom reports may explicitly set info.entities.user.idcolumn.
     *
     * @param array<string, mixed> $sample
     * @param string $sql
     * @param array $info
     * @param string $entity
     * @return string|null
     */
    public static function identity_column(array $sample, string $sql, array $info, string $entity): ?string {
        $declared = $info['entities'][$entity]['idcolumn'] ?? null;
        if (is_string($declared) && array_key_exists($declared, $sample)) {
            return $declared;
        }
        if ($entity === 'user') {
            foreach (['userid', 'user_id'] as $column) {
                if (array_key_exists($column, $sample)) {
                    return $column;
                }
            }
            if (array_key_exists('username', $sample) && preg_match('/(?:\bmdl_user\b|\{user\})/i', $sql)) {
                return 'username';
            }
            if (isset($sample['id'], $sample['firstname'], $sample['lastname']) &&
                    preg_match('/(?:\bmdl_user\b|\{user\})/i', $sql)) {
                return 'id';
            }
        } else {
            foreach (['courseid', 'course_id', 'c_id'] as $column) {
                if (array_key_exists($column, $sample)) {
                    return $column;
                }
            }
            if (array_key_exists('shortname', $sample) &&
                    preg_match('/(?:\bmdl_course\b|\{course\})/i', $sql)) {
                return 'shortname';
            }
        }
        return null;
    }

    /**
     * Prepare extra headers and per-row values for table, JSON and MCP consumers.
     * The input rows and the saved SQL/column definitions are never mutated.
     *
     * @param array $rows list of array or object records
     * @param array $definitions existing column definitions, keyed by SQL alias
     * @param string $sql original SQL, not user input
     * @param array $info saved element information
     * @param int|null $viewerid authenticated user (MCP may supply a different identity)
     * @return array array{0: array, 1: array}
     */
    public static function prepare(array $rows, array $definitions, string $sql,
            array $info = [], ?int $viewerid = null): array {
        global $DB, $USER;

        $viewerid = $viewerid ?? (int)$USER->id;
        $rows = array_map(static function($row) {
            return (object)(array)$row;
        }, $rows);
        $sample = $rows ? (array)$rows[0] : array_fill_keys(array_keys($definitions), null);

        foreach (['user', 'course'] as $entity) {
            if (!self::enabled($entity)) {
                continue;
            }

            $identity = self::identity_column($sample, $sql, $info, $entity);
            if ($identity === null) {
                continue;
            }

            $identityvalues = array_map(static function($row) use ($identity) {
                return (string)($row->{$identity} ?? '');
            }, $rows);
            $selected = self::selected($entity);
            $standard = $entity === 'user' ? self::USER_FIELDS : self::COURSE_FIELDS;
            $hidden = array_diff(array_keys($standard), $selected);

            // Hidden built-in columns must be absent from the returned JSON, not just CSS-hidden.
            foreach ($hidden as $key) {
                if ($key === 'name' || $key === 'fullname') {
                    continue;
                }
                if (isset($definitions[$key])) {
                    unset($definitions[$key]);
                }
                foreach ($rows as $row) {
                    unset($row->{$key});
                }
            }

            $extra = [];
            foreach ($selected as $key) {
                $existing = $entity === 'user' ?
                    ($key === 'name' ? ['firstname', 'fullname', 'user_fullname'] : [$key]) :
                    ($key === 'fullname' ? ['course', 'c_fullname', 'fullname', 'course_fullname'] : [$key]);
                if (array_intersect($existing, array_keys($sample))) {
                    continue;
                }
                $alias = $entity . '_' . ($key === 'name' ? 'fullname' : $key);
                if (array_key_exists($alias, $sample) || isset($definitions[$alias])) {
                    continue;
                }
                $extra[$key] = $alias;
                $labels = self::available($entity);
                $definitions[$alias] = [
                    'key' => $alias,
                    'title' => $labels[$key],
                    'type' => 'string',
                    'mustache' => '',
                ];
            }
            if (!$extra || !$rows) {
                continue;
            }

            // Batch metadata, never a SQL lookup per report row.
            $ids = array_values(array_unique(array_filter($identityvalues,
                static function($id) { return $id !== ''; })));
            if (!$ids) {
                continue;
            }
            if ($entity === 'user') {
                $records = self::user_records($identity, $ids);
                $custom = self::user_custom_data($records, array_keys($extra), $viewerid);
            } else {
                $records = self::course_records($identity, $ids);
                $custom = self::course_custom_data($records, array_keys($extra), $viewerid);
            }

            foreach ($rows as $rowindex => $row) {
                $lookup = $identityvalues[$rowindex];
                $record = $records[$lookup] ?? null;
                foreach ($extra as $key => $alias) {
                    $value = '';
                    if ($record) {
                        if (str_starts_with($key, 'custom_')) {
                            $value = $custom[(int)$record->id][$key] ?? '';
                        } else if ($entity === 'user') {
                            $private = self::can_view_user_details((int)$record->id, $viewerid);
                            if ($key === 'name') {
                                $value = fullname($record);
                            } else if ($private) {
                                $value = $record->{self::USER_FIELDS[$key]} ?? '';
                            }
                        } else if (self::can_view_course((int)$record->id, $viewerid)) {
                            $value = $record->{self::COURSE_FIELDS[$key]} ?? '';
                        }
                    }
                    $row->{$alias} = s((string)$value);
                }
            }
        }
        return [$rows, $definitions];
    }

    /**
     * @param string $identity
     * @param string[] $ids
     * @return array<string, object>
     */
    private static function user_records(string $identity, array $ids): array {
        global $DB;

        $records = [];
        foreach (array_chunk($ids, 250) as $chunk) {
            $part = $identity === 'username' ?
                $DB->get_records_list('user', 'username', $chunk, '', '*') :
                $DB->get_records_list('user', 'id', array_map('intval', $chunk), '', '*');
            foreach ($part as $record) {
                $records[] = $record;
            }
        }
        $result = [];
        foreach ($records as $record) {
            if (!$record->deleted) {
                $result[(string)($identity === 'username' ? $record->username : $record->id)] = $record;
            }
        }
        return $result;
    }

    /**
     * @param string $identity
     * @param string[] $ids
     * @return array<string, object>
     */
    private static function course_records(string $identity, array $ids): array {
        global $DB;

        $field = $identity === 'shortname' ? 'shortname' : 'id';
        $records = [];
        foreach (array_chunk($ids, 250) as $chunk) {
            $part = $DB->get_records_list('course', $field,
                $field === 'id' ? array_map('intval', $chunk) : $chunk, '', '*');
            foreach ($part as $record) {
                $records[] = $record;
            }
        }
        $result = [];
        foreach ($records as $record) {
            $result[(string)$record->{$field}] = $record;
        }
        return $result;
    }

    /**
     * @param int $userid
     * @param int $viewerid
     * @return bool
     */
    private static function can_view_user_details(int $userid, int $viewerid): bool {
        return $userid === $viewerid || has_capability('local/kopere_bi:manage', context_system::instance(), $viewerid) ||
            has_capability('moodle/user:viewalldetails', context_user::instance($userid), $viewerid);
    }

    /**
     * @param int $courseid
     * @param int $viewerid
     * @return bool
     */
    private static function can_view_course(int $courseid, int $viewerid): bool {
        return has_capability('local/kopere_bi:manage', context_system::instance(), $viewerid) ||
            has_capability('moodle/course:view', context_course::instance($courseid), $viewerid);
    }

    /**
     * @param array<string, object> $records
     * @param string[] $keys
     * @param int $viewerid
     * @return array<int, array<string, string>>
     */
    private static function user_custom_data(array $records, array $keys, int $viewerid): array {
        global $DB;

        $customkeys = array_filter($keys, static function($key) {
            return str_starts_with($key, 'custom_');
        });
        if (!$customkeys || !$records) {
            return [];
        }
        $wanted = array_map(static function($key) {
            return substr($key, strlen('custom_'));
        }, $customkeys);
        $fields = array_filter($DB->get_records('user_info_field'), static function($field) use ($wanted) {
            return in_array($field->shortname, $wanted, true);
        });
        if (!$fields) {
            return [];
        }
        $userids = array_map(static function($record) { return (int)$record->id; }, array_values($records));
        $result = [];
        foreach (array_chunk($userids, 250) as $chunk) {
            [$usersql, $userparams] = $DB->get_in_or_equal($chunk, SQL_PARAMS_NAMED, 'biu');
            [$fieldsql, $fieldparams] = $DB->get_in_or_equal(array_keys($fields), SQL_PARAMS_NAMED, 'bif');
            $data = $DB->get_records_sql(
                "SELECT d.id, d.userid, d.fieldid, d.data
                   FROM {user_info_data} d
                  WHERE d.userid $usersql AND d.fieldid $fieldsql",
                $userparams + $fieldparams
            );
            foreach ($data as $datum) {
                $field = $fields[$datum->fieldid];
                if ((int)$field->visible === 2 ||
                    self::can_view_user_details((int)$datum->userid, $viewerid)) {
                    $result[(int)$datum->userid]['custom_' . $field->shortname] = $datum->data;
                }
            }
        }
        return $result;
    }

    /**
     * @param array<string, object> $records
     * @param string[] $keys
     * @param int $viewerid
     * @return array<int, array<string, string>>
     */
    private static function course_custom_data(array $records, array $keys, int $viewerid): array {
        $wanted = array_filter($keys, static function($key) {
            return str_starts_with($key, 'custom_');
        });
        if (!$wanted || !$records) {
            return [];
        }
        $values = course_handler::create()->get_instances_data(array_map(
            static function($record) { return (int)$record->id; }, array_values($records)
        ));
        $result = [];
        foreach ($values as $courseid => $fields) {
            if (!self::can_view_course((int)$courseid, $viewerid)) {
                continue;
            }
            foreach ($fields as $fielddata) {
                $key = 'custom_' . $fielddata->get_field()->get('shortname');
                if (in_array($key, $wanted, true)) {
                    $value = $fielddata->get_value();
                    $result[(int)$courseid][$key] = is_scalar($value) ? (string)$value : '';
                }
            }
        }
        return $result;
    }
}
