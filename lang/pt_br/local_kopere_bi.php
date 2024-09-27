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
 * lang en file
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['area_desc'] = 'Monta um gráfico de área';
$string['area_name'] = 'Gráfico de Área';
$string['block_add'] = 'Adicionar novo Bloco';
$string['block_delete_message'] = 'Deseja realmente excluir este bloco e <br> seus relatórios permanentemente?';
$string['block_delete_title'] = 'Excluir Bloco';
$string['block_extra'] = 'Opções muito avançadas do gráfico';
$string['block_not_found'] = 'Bloco não localizado';
$string['block_theme'] = 'Tema do bloco';
$string['block_theme_blue'] = 'Tema Azul';
$string['block_theme_dark'] = 'Tema Dark';
$string['block_theme_green'] = 'Tema Verde';
$string['block_theme_light'] = 'Tema Claro (padrão)';
$string['block_theme_orange'] = 'Tema Laranja';
$string['block_theme_pink'] = 'Tema Roxo';
$string['blocktype_not_found'] = 'Tipo de bloco não localizado';
$string['c_enablecompletion'] = 'Habilitado a completude';
$string['c_format'] = 'Formato do Curso';
$string['c_fullname'] = 'Nome do curso';
$string['c_id'] = 'ID do Curso';
$string['c_shortname'] = 'Nome curto';
$string['c_tempo'] = 'Tempo de permanência';
$string['c_timemodified'] = 'Modificado em';
$string['ca_completed_activities'] = 'Módulos completos';
$string['cache_time'] = 'Tempo de cache';
$string['cache_time_15min'] = '15 minutos';
$string['cache_time_1d'] = '24 horas';
$string['cache_time_1h'] = '1 hora';
$string['cache_time_30min'] = '30 minutos';
$string['cache_time_6h'] = '6 horas';
$string['cache_time_desc'] = 'Tempo que os resultados da SQL devem ficar em cache antes de ser limpo';
$string['cache_time_no'] = 'Sem cache';
$string['case_complete'] = 'Completo';
$string['case_incomplete'] = 'Incompleto';
$string['case_never_accessed'] = 'Nunca acessou';
$string['cat_description'] = 'Descrição da categoria';
$string['cat_edit'] = 'Editar';
$string['cat_name'] = 'Nome da categoria';
$string['cat_new'] = 'Nova categoria';
$string['cat_not_found'] = 'Categoria não localizada';
$string['cat_title'] = 'Categoria';
$string['cc_id'] = 'Cursos concluídos';
$string['cc_timecompleted'] = 'Completo em';
$string['chart_area_default'] = 'Configuracão padrão do Gráfico de Área';
$string['chart_column_default'] = 'Configuracão padrão do Gráfico de Colunas';
$string['chart_default_desc'] = 'Alteração deste valor não afeta nenhum dos gráficos já adicionados<br>Só edite se você conhece o Apex Charts. Veja em <a target=\'_blank\' href=\'https://apexcharts.com/docs/series/\'>apexcharts.com/docs</a>';
$string['chart_line_default'] = 'Configuracão padrão do Gráfico de Linhas';
$string['chart_pie_default'] = 'Configuracão padrão do Gráfico de Pizza';
$string['city_name'] = 'Cidade';
$string['class_not_found'] = 'Classe não localizado';
$string['click_new_block'] = 'Clique sobre o tipo do Bloco que deseja adicionar.';
$string['client_name'] = 'Navegador';
$string['client_version'] = 'Versão';
$string['cm_cmid'] = 'Course Módule ID';
$string['column_desc'] = 'Monta um gráfico de Colunas';
$string['column_name'] = 'Gráfico de Colunas';
$string['country_name'] = 'Pais';
$string['course_completed'] = 'Porcentagem concluído';
$string['create'] = 'Criar';
$string['create_report'] = 'Criar relatório';
$string['css_extra'] = 'CSS Extra';
$string['css_extra_desc'] = 'Adicione estilos CSS, ou SCSS a este bloco.<br> O CSS adicionado será aplicado apenas ao conteúdos deste Bloco, e não terá nenhum efeito sobre qualquer outra parte do Moodle';
$string['ctx_instanceid'] = 'Cursos inscritos';
$string['data_not_found'] = 'Nenhum dado';
$string['delete_report_text'] = 'Deseja realmente excluir este relatório?';
$string['delete_report_title'] = 'Excluir relatório';
$string['e_enrol'] = 'Tipo de matrícula';
$string['edit_report'] = 'Editar relatório';
$string['error_chart_renderer'] = 'Erro ao renderizar o gráfico';
$string['error_data_loader'] = 'Erro ao carregar os dados do gráfico';
$string['extra_langs_customs_title'] = 'Para lhe ajudar com novas strings, deixei alguns campos vazios:';
$string['extra_langs_filter_component'] = 'Componente';
$string['extra_langs_header_identifier'] = 'Identificador';
$string['extra_langs_header_lang_key'] = 'Chave de substituição';
$string['extra_langs_header_string'] = 'Texto atual';
$string['extra_langs_title'] = 'Chaves que já existem:';
$string['extra_options'] = 'Opções avançadas do bloco';
$string['firstname'] = 'Nome do estudante';
$string['g_finalgrade'] = 'Nota final';
$string['grade'] = 'Nota';
$string['html_after'] = 'Texto (ou HTML) opcional depois do gráfico';
$string['html_block'] = 'HTML do Bloco';
$string['html_block_desc'] = '<p>Os valores gerados pelo SQL devem ser substituídos por chaves.</p><p>Se tens a SQL <code>SELECT <b>email</b>, <b>fullname</b> FROM mdl_user WHERE id = :userid</code> você pode usar no HTML as chaves <code>{email}</code> e <code>{fullname}</code> para substituir no HTML.</p>';
$string['html_desc'] = 'Mostra um Bloco, com formatação HTML, com dados vindo do banco de dados';
$string['html_extra'] = 'Texto (ou HTML) opcional acima do gráfico';
$string['html_name'] = 'Bloco de HTML';
$string['html_sql_warning'] = 'Lembre-se que a SQL abaixo só irá retornar uma única linha.';
$string['info_desc'] = 'Apenas uma informação. Ideal para mostrar o nome do estudante, status de matrícula, etc...';
$string['info_error_sql'] = 'Erro ao executar SQL';
$string['info_name'] = 'Linha de Informação';
$string['info_sql_warning'] = 'Lembre-se que a SQL abaixo deve retornar apenas uma única linha, com apenas uma coluna.';
$string['integracaoroot'] = 'Integrações';
$string['item_not_found'] = 'Item não localizado';
$string['kopere_bi:manage'] = 'Gerenciar Business intelligence';
$string['kopere_bi:view'] = 'Ver Business intelligence';
$string['l_ip'] = 'IP';
$string['l_objecttable'] = 'Nome do módulo';
$string['l_origin'] = 'Origem';
$string['l_timecreated'] = 'Criado em';
$string['line_desc'] = 'Monta um gráfico de linhas';
$string['line_name'] = 'Gráfico de Linhas';
$string['line_sql_warning'] = '<p>Lembre-se que a SQL abaixo deve retornar com a seguinte estrutura:</p>
<ul>
    <li>A primeira coluna deve conter o texto que será utilizado como os nomes do eixo X.</li>
    <li>As demais colunas devem ser estruturadas da seguinte forma:
        <ul>
            <li>O nome da coluna será utilizado como o nome da série. Você pode utilizar as strings de tradução, conforme explicado na
                <a href="?classname=bi-extra_langs&method=index" target="_blank">página de strings</a>.</li>
            <li>O valor da coluna representará os dados da série no gráfico.</li>
        </ul>
    </li>
</ul>
<blockquote>No exemplo abaixo a primeiroa coluna retorna o nome do curso e a segujnda coluna retorna a quantidade de notícias de cada curso:
<pre>SELECT fullname,
       newsitems AS "Quantidade de notícias do curso"
  FROM mdl_course</pre></blockquote>
<blockquote>Já no exemplo abaixo, além da primeira coluna sendo o nome do curso, gera mais duas linhas no gráfico, com tradução do nome das colunas:
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['loading'] = 'Carregando...';
$string['maps_1_city'] = '{a1} e mais uma cidade';
$string['maps_desc'] = 'Monta um Mapa de estudantes online, baseado no IP dele';
$string['maps_many_city'] = '{a1} e outras {a2} cidades';
$string['maps_name'] = 'Mapa de estudantes online';
$string['maps_online'] = '{a1} estudante online';
$string['maps_onlines'] = '{a1} estudantes online';
$string['maps_sql_warning'] = '<p>Lembre-se que a SQL abaixo deve retornar apenas uma coluna, e está coluna deve conter um IP válido.<br>Exemplo, o SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> retorna todos os estudantes que acessaram Moodle nos últimos 10 minutos</p>';
$string['modulename'] = 'Business intelligence';
$string['new_block'] = 'Novo Bloco nesta página';
$string['new_block_1'] = 'Um bloco';
$string['new_block_12'] = 'Um mais dois Blocos';
$string['new_block_2'] = 'Dois blocos';
$string['new_block_21'] = 'Dois mais um Blocos';
$string['new_block_25'] = 'Um largo e um estreito';
$string['new_block_3'] = 'Três Blocos';
$string['new_block_4'] = 'Quatro Blocos';
$string['new_block_52'] = 'Um estreito e um Largo';
$string['num_user'] = 'Quantidade de Estudantes';
$string['os_name'] = 'Sistema Operacional';
$string['page_description'] = 'Descrição da página';
$string['page_edit'] = 'Editar Página';
$string['page_name'] = 'Nome da página';
$string['page_new'] = 'Nova página';
$string['page_new_cat'] = 'Nova página';
$string['page_new_sequence'] = 'Arraste os Bloco para mudar a order dos blocos.';
$string['page_not_found'] = 'Página não localizada';
$string['page_preview'] = 'Preview da Página';
$string['page_title_edit'] = 'Editar título destá Página';
$string['page_title_export'] = 'Exportar Página';
$string['pie_desc'] = 'Monta um gráfico de pie';
$string['pie_name'] = 'Gráfico de pie';
$string['pie_sql_warning'] = '<p>A SQL abaixo deve retornar apenas duas columas.</p><p>A primeira coluna será o nome da coluna e a segunda coluna deve ser valor numérico.</p>';
$string['pluginname'] = 'Business intelligence';
$string['privacy:metadata'] = 'O plugin do Business intelligence não armazena nenhum dado pessoal.';
$string['reload_time'] = 'Recarregar dados a cada';
$string['reload_time_10m'] = '10 minutos';
$string['reload_time_1h'] = '1 horas';
$string['reload_time_1m'] = '1 minutos';
$string['reload_time_20m'] = '20 minutos';
$string['reload_time_2h'] = '2 horas';
$string['reload_time_30m'] = '30 minutos';
$string['reload_time_40m'] = '40 minutos';
$string['reload_time_50m'] = '50 minutos';
$string['reload_time_5m'] = '5 minutos';
$string['reload_time_desc'] = 'Recarregar os dados a cada X minutos. Este valor deve ser maior que o valor do cache!';
$string['reload_time_none'] = 'Nunca';
$string['report_1_cat_description'] = 'Relatórios sobre o desempenho e progresso dos estudantes em seus cursos.';
$string['report_1_cat_title'] = 'Estudantes';
$string['report_1_categories'] = 'Categorias';
$string['report_1_courses'] = 'Cursos';
$string['report_1_description'] = 'Relatórios de status dos estudantes';
$string['report_1_modules'] = 'Módulos';
$string['report_1_title'] = 'Estudantes Ativos';
$string['report_1_user_status'] = 'Status dos estudantes';
$string['report_1_user_summary'] = 'Resumo dos estudantes ativos';
$string['report_1_users'] = 'Estudantes';
$string['report_2_cat_description'] = 'Análises completas de todos os cursos disponíveis, desempenho, progresso e desenvolvimento dos estudantes.';
$string['report_2_cat_title'] = 'Cursos';
$string['report_2_completion_progress'] = 'Progresso com percentual de conclusão';
$string['report_2_course_access_statistics'] = 'Estatística de acesso ao Curso';
$string['report_2_course_analysis_participation_completion'] = 'Análise do Curso: Participação e Conclusão';
$string['report_2_course_progress'] = 'Progresso do Curso';
$string['report_2_description'] = 'Relatório de cursos';
$string['report_2_title'] = 'Cursos';
$string['report_3_course_access_time'] = 'Tempo de acesso aos cursos';
$string['report_3_description'] = 'Mostra os estudantes online e principais regiões';
$string['report_3_operating_systems'] = 'Sistemas Operacionais';
$string['report_3_title'] = 'Estudantes Online';
$string['report_3_top_browsers'] = 'Navegadores mais utilizados';
$string['report_3_top_country_access'] = 'Paises com mais acessos';
$string['report_3_users_online'] = 'Estudantes online';
$string['report_3_users_online_list'] = 'Lista de estudantes online';
$string['report_4_cat_description'] = 'Relatórios de matrículas, cobrindo histórico, status atual e tendências de inscrições nos cursos.';
$string['report_4_cat_title'] = 'Matrículas';
$string['report_new'] = 'Novo relatório de "{$a}"';
$string['report_preview'] = 'Pré-visualizar relatório';
$string['report_save'] = 'Salvar & ir para configuração das colunas';
$string['report_title'] = 'Título do relatório';
$string['reports_selectcourse'] = 'Selecione o curso para gerar o relatório';
$string['reports_selectuser'] = 'Selecione o estudante para gerar o relatório';
$string['return_edit'] = '<< Voltar a edição';
$string['save'] = 'Salvar';
$string['secounds'] = 'Quanto tempo';
$string['select_report_select_type'] = 'Selecione o tipo de Relatório';
$string['select_report_select_type_desc'] = 'Primeiro, escolha qual tipo de relatório você deseja para este bloco';
$string['select_report_type'] = 'Tipo de Relatório';
$string['select_report_type_desc'] = 'Você pode alternar entre os tipos "{$a->line}", "{$a->area}" ou "{$a->column}"';
$string['setting_apex'] = 'Configurações do Apex Charts';
$string['setting_apex_desc'] = 'Só edite se você conhece o Apex Charts. Veja em <a target=\'_blank\' href=\'https://apexcharts.com/docs/series/\'>apexcharts.com/docs</a>';
$string['sql_read_only'] = 'Todas as consultas SQL estão protegidas por por conexão somente leitura e não há como executar comandos de INSERT/UPDATE/DELETE.';
$string['sql_replace_keys'] = '<h4>Chaves de substituição</h4>
<ul>
    <li><b>:userid</b> ID do estudante para gerar o relatório.</li>
    <li><b>:courseid</b> ID do curso para gerar o relatório.</li>
</ul>
<h4>Multi-idiomas</h4>
<p>Para retornar colunas que serão traduzidas com base nos pacotes de idiomas do Moodle, é necessário seguir um formato específico que permite que as strings sejam processadas e localizadas adequadamente. O formato correto é:</p>
<pre>lang::{identifier}::{component}</pre>
<p>Sendo:</p>
<ul>
    <li><b>{identifier}</b>: Representa o identificador da string, o qual será usado para buscar a tradução no pacote de idiomas.</li>
    <li><b>{component}</b>: Refere-se ao componente onde a string de idioma está definida e geralmente é o nome do plugin (por exemplo, <code>mod_forum</code>, <code>local_kopere_dashboard</code>, <code>theme_degrade</code>).</li>
</ul>
<p><em>Ex: Se você precisa retornar uma string traduzida para o componente <code>mod_forum</code> com o identificador <code>postmessage</code>, o retorno deverá ser estruturado da seguinte forma:</em></p>
<pre>SELECT \'<b>lang::postmessage::mod_forum</b>\' FROM mdl_forum</pre>
<p>Acesse a <a href="?classname=bi-extra_langs&method=index" target="_blank">página de strings</a> e veja todas as strings disponíveis.</p>';
$string['sql_replace_keys_mdl'] = '<h4>O prefixo do banco de dados</h4>
<p>Você pode usar sempre o prefixo <code>mdl_</code> mesmo seu banco usando o prefixo <code>{$a}</code>.
        O Business intelligence fará esta substituição.</p>';
$string['table_col_title'] = 'Título da coluna';
$string['table_collumn_not_configured'] = 'Colunas não configuradas nesta tabela';
$string['table_desc'] = 'Mostra uma tabela com paginação de dados.';
$string['table_edit_collumn'] = 'Coluna';
$string['table_first_5'] = 'Os cinco primeiros registros da consulta';
$string['table_info_secound'] = 'Aqui você pode definir um nome para cada coluna e, em seguida, especificar o formato desejado para a exibição dos dados.
<ul>
    <li><strong>Não mostrar esta coluna</strong>: Oculta a coluna selecionada na visualização, tornando-a invisível para o estudante, sem afetar os dados armazenados.</li>
    <li><strong>Sem formatação</strong>: Exibe o conteúdo da coluna exatamente como está armazenado, sem aplicar qualquer formatação adicional, garantindo a visualização dos dados brutos.</li>
    <li><strong>Números</strong>: Formata a coluna para exibir apenas valores numéricos, aplicando as regras padrão de exibição de números, como separadores de milhares e decimais.</li>
    <li><strong>Converter a coluna para nome completo "fullname()"</strong>: Executa a função fullname() para criar o nome completo baseado na linguagem do estudante.</li>
    <li><strong>Converter ID do estudante para foto de perfil</strong>: Substitui o ID do estudante na coluna por sua respectiva foto de perfil, permitindo uma identificação visual imediata dos estudantes.</li>
    <li><strong>Campo binário para Verdadeiro/False</strong>: Interpreta o valor binário na coluna como um indicador de status, onde \'0\' ou \'false\' significa inativo e \'1\' ou \'true\' significa ativo.</li>
    <li><strong>Campo binário para Ativo/Inativo</strong>: Usa o valor binário para determinar a visibilidade, onde \'0\' ou \'false\' representa Inativo e \'1\' ou \'true\' representa Inativo.</li>
    <li><strong>Campo binário para Visível/Invisível</strong>: Usa o valor binário para determinar a visibilidade, onde \'0\' ou \'false\' representa invisível e \'1\' ou \'true\' representa visível.</li>
    <li><strong>Campo binário para ativo/deletado:</strong> Este campo identifica o status de um item como ativo (0) ou deletado (1), permitindo a gestão e recuperação de dados em sistemas de armazenamento.</li>
    <li><strong>Campo "time" formatado para data</strong>: Converte o valor de tempo (timestamp) na coluna para uma data legível, exibindo apenas a data (dia/mês/ano).</li>
    <li><strong>Campo "time" formatado para data e hora</strong>: Exibe o valor de tempo (timestamp) na coluna como uma data completa, incluindo a hora (dia/mês/ano e horas:minutos).</li>
    <li><strong>Campo "time" formatado para hora</strong>: Formata o valor de tempo (timestamp) na coluna para exibir apenas a hora (horas:minutos), omitindo a data.</li>
</ul>';
$string['table_info_topo'] = 'Primeiro, você verá uma prévia dos resultados da busca. Em seguida, será apresentada uma sequência de colunas para que você possa nomear os títulos e definir o formato dos dados de cada coluna.';
$string['table_name'] = 'Tabela de dados';
$string['table_renderer_date'] = 'Campo "time" formatado para data';
$string['table_renderer_datetime'] = 'Campo "time" formatado para data e hora';
$string['table_renderer_deleted'] = 'Campo binário para ativo/deletado';
$string['table_renderer_fullname'] = 'Converter a coluna para nome completo "fullname()"';
$string['table_renderer_none'] = 'Não mostrar esta coluna';
$string['table_renderer_number'] = 'Números';
$string['table_renderer_seconds'] = 'Campo "time" formatado hora';
$string['table_renderer_status'] = 'Campo binário para Ativo/Inativo';
$string['table_renderer_title'] = 'Formatação da coluna';
$string['table_renderer_translate'] = 'Use o get_string("identificador","component") para traduzir a coluna';
$string['table_renderer_truefalse'] = 'Campo binário para Verdadeiro/False';
$string['table_renderer_userphoto'] = 'Converter ID do estudante para foto de perfil';
$string['table_renderer_visible'] = 'Campo binário para Visível/Invisível';
$string['theme_palette_default'] = 'Paleta Padrão';
$string['theme_palette_desc'] = 'Cores deta paleta:';
$string['theme_palette_desc2'] = 'Veja todos os temas aqui';
$string['theme_palette_palette1'] = 'Paleta 1';
$string['theme_palette_palette10'] = 'Paleta 10';
$string['theme_palette_palette2'] = 'Paleta 2';
$string['theme_palette_palette3'] = 'Paleta 3';
$string['theme_palette_palette4'] = 'Paleta 4';
$string['theme_palette_palette5'] = 'Paleta 5';
$string['theme_palette_palette6'] = 'Paleta 6';
$string['theme_palette_palette7'] = 'Paleta 7';
$string['theme_palette_palette8'] = 'Paleta 8';
$string['theme_palette_palette9'] = 'Paleta 9';
$string['theme_palette_title'] = 'Paleta de cores';
$string['timecompleted'] = 'Matrícula concluída';
$string['title'] = 'Business intelligence';
$string['u_fullname'] = 'Nome do estudante';
$string['u_id'] = 'ID do estudante';
$string['u_idnumber'] = 'Número de identificação';
$string['ue_id'] = 'ID da Matrícula';
$string['ue_status'] = 'Status da Matrícula';
$string['ue_timecreated'] = 'Matrícula criada em';
$string['ue_timeend'] = 'Matrícula termina em';
$string['ul_timeaccess'] = 'Ultimo acesso';
$string['word_extra_00'] = '';
$string['word_extra_01'] = '';
$string['word_extra_02'] = '';
$string['word_extra_03'] = '';
$string['word_extra_04'] = '';
$string['word_extra_05'] = '';
$string['word_extra_06'] = '';
$string['word_extra_07'] = '';
$string['word_extra_08'] = '';
$string['word_extra_09'] = '';
$string['word_extra_10'] = '';
$string['word_extra_11'] = '';
$string['word_extra_12'] = '';
$string['word_extra_13'] = '';
$string['word_extra_14'] = '';
$string['word_extra_15'] = '';
$string['word_extra_16'] = '';
$string['word_extra_17'] = '';
$string['word_extra_18'] = '';
$string['word_extra_19'] = '';
$string['word_extra_20'] = '';
$string['word_extra_21'] = '';
$string['word_extra_22'] = '';
$string['word_extra_23'] = '';
$string['word_extra_24'] = '';
$string['word_extra_25'] = '';
$string['word_extra_26'] = '';
$string['word_extra_27'] = '';
$string['word_extra_28'] = '';
$string['word_extra_29'] = '';
$string['word_extra_30'] = '';
$string['word_extra_31'] = '';
$string['word_extra_32'] = '';
$string['word_extra_33'] = '';
$string['word_extra_34'] = '';
$string['word_extra_35'] = '';
$string['word_extra_36'] = '';
$string['word_extra_37'] = '';
$string['word_extra_38'] = '';
$string['word_extra_39'] = '';
$string['word_extra_40'] = '';
$string['word_extra_41'] = '';
$string['word_extra_42'] = '';
$string['word_extra_43'] = '';
$string['word_extra_44'] = '';
$string['word_extra_45'] = '';
$string['word_extra_46'] = '';
$string['word_extra_47'] = '';
$string['word_extra_48'] = '';
$string['word_extra_49'] = '';
$string['word_extra_50'] = '';
$string['word_extra_51'] = '';
$string['word_extra_52'] = '';
$string['word_extra_53'] = '';
$string['word_extra_54'] = '';
$string['word_extra_55'] = '';
$string['word_extra_56'] = '';
$string['word_extra_57'] = '';
$string['word_extra_58'] = '';
$string['word_extra_59'] = '';
$string['word_extra_60'] = '';
$string['word_extra_61'] = '';
$string['word_extra_62'] = '';
$string['word_extra_63'] = '';
$string['word_extra_64'] = '';
$string['word_extra_65'] = '';
$string['word_extra_66'] = '';
$string['word_extra_67'] = '';
$string['word_extra_68'] = '';
$string['word_extra_69'] = '';
$string['word_extra_70'] = '';
$string['word_extra_71'] = '';
$string['word_extra_72'] = '';
$string['word_extra_73'] = '';
$string['word_extra_74'] = '';
$string['word_extra_75'] = '';
$string['word_extra_76'] = '';
$string['word_extra_77'] = '';
$string['word_extra_78'] = '';
$string['word_extra_79'] = '';
$string['word_extra_80'] = '';
