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

use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use local_kopere_bi\vo\local_kopere_bi_online;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Contabiliza o tempo gasto de um usuário para o dashboard
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class online_update extends external_api {
    /**
     * Parâmetros recebidos pelo webservice
     *
     * @return external_function_parameters
     */
    public static function api_parameters() {
        return new external_function_parameters([
            'online_id' => new external_value(PARAM_INT, 'The online id'),
            'cache_key' => new external_value(PARAM_TEXT, 'The cache id'),
            'seconds' => new external_value(PARAM_INT, 'The time spent by the user on the page'),
        ]);
    }

    /**
     * Identificador do retorno do webservice
     *
     * @return external_single_structure
     */
    public static function api_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, "Operation Success", VALUE_OPTIONAL),
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
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     */
    public static function api($onlineid, $cachekey, $seconds) {
        global $DB, $USER;

        if (isset($USER->koperebionline_time[$cachekey])) {
            $USER->koperebionline_time[$cachekey] = time();
        }

        // Resgata a instância do contexto de sistema.
        $systemcontext = \context_system::instance();

        // Valida o contexto do sistema.
        self::validate_context($systemcontext);

        /** @var local_kopere_bi_online $online */
        $online = $DB->get_record('local_kopere_bi_online', ['id' => $onlineid]);
        if ($online) {
            $online->currenttime = time();
            $online->seconds = $online->seconds + $seconds;

            $DB->update_record('local_kopere_bi_online', $online);
        }
        return ['success' => true];
    }
}

