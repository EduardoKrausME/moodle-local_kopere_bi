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
 * Lang file
 *
 * @package   biblocks_table
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Tabela de dados';
$string['pluginname_desc'] = 'Apresenta uma tabela com paginação de dados.';
$string['table_col_title'] = 'Título da coluna';
$string['table_column_not_configured'] = 'Colunas não configuradas nesta tabela';
$string['table_edit_column'] = 'Coluna';
$string['table_first_records'] = 'Os dez primeiros registos da consulta';
$string['table_info_topo'] = 'Primeiro, verá uma pré-visualização dos resultados da consulta. Em seguida, será apresentada uma série de colunas para indicar os títulos e definir o formato dos dados de cada coluna.';
$string['table_info_types'] = 'Agora pode definir um nome para a coluna e depois especificar o formato pretendido, bem como indicar se pretende alguma formatação extra com Mustache.
<ul>
    <li><strong>Sem formatação</strong>: Apresenta o conteúdo exatamente como está ou aplica Mustache caso o adicione.</li>
    <li><strong>Não mostrar esta coluna</strong>: Oculta a coluna selecionada na visualização, mas os dados continuam disponíveis para processamento com Mustache.</li>
    <ul><li>Mustache não disponível</li></ul>
    <li><strong>Números</strong>: Formata a coluna para apresentar apenas valores numéricos, aplicando as regras padrão de apresentação de números, como separadores de milhares e decimais.</li>
    <ul><li>Mustache não disponível</li></ul>
    <li><strong>Converter coluna para nome completo "fullname()"</strong>: Executa a função <code>fullname()</code> para gerar o nome completo com base no idioma, que será guardado nesta mesma coluna. Para isto funcionar, a coluna <code>lastname</code> é obrigatória e deve ser ocultada, se possível.</li>
    <li><strong>Converter ID do estudante para fotografia de perfil</strong>: Usa o ID desta coluna para criar a fotografia de perfil.</li>
    <li><strong>Campo binário para Visível/Invisível</strong>: Usa o valor binário para determinar a visibilidade, em que "0"/"false" significa invisível e "1"/"true" significa visível.</li>
    <li><strong>Campo binário para Ativo/Inativo</strong>: Usa o valor binário para determinar o estado, em que "0"/"false" significa Inativo e "1"/"true" significa Ativo.</li>
    <li><strong>Campo "Time" formatado como data</strong>: Converte o valor de tempo (timestamp) da coluna para uma data legível, apresentando apenas a data (dia/mês/ano).</li>
    <ul><li>Mustache não disponível</li></ul>
    <li><strong>Campo "Time" formatado como data e hora</strong>: Apresenta o valor de tempo (timestamp) da coluna como uma data completa, incluindo a hora (dia/mês/ano e horas:minutos).</li>
    <ul><li>Mustache não disponível</li></ul>
    <li><strong>Campo "Time" formatado como hora</strong>: Formata o valor de tempo (timestamp) da coluna para apresentar apenas a hora (horas:minutos), omitindo a data.</li>
    <ul><li>Mustache não disponível</li></ul>
</ul>';
$string['table_renderer_date'] = 'Campo "Time" formatado como data';
$string['table_renderer_datetime'] = 'Campo "Time" formatado como data e hora';
$string['table_renderer_filesize'] = 'Converte para tamanho de dados em disco';
$string['table_renderer_mustache'] = 'HTML da coluna
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'Não apresentar esta coluna';
$string['table_renderer_number'] = 'Números';
$string['table_renderer_seconds'] = 'Campo "Time" formatado como hora';
$string['table_renderer_status'] = 'Campo binário para Ativo/Inativo';
$string['table_renderer_title'] = 'Formatação da coluna';
$string['table_renderer_translate'] = 'Usar get_string("identifier", "component") para traduzir a coluna';
$string['table_renderer_userfullname'] = 'Converter a coluna para o nome completo do estudante com a função "fullname()"';
$string['table_renderer_userphoto'] = 'Converter ID do estudante para fotografia de perfil';
$string['table_renderer_visible'] = 'Campo binário para Visível/Invisível';
