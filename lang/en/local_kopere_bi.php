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

$string['active_enrolments'] = 'Matrículas Ativas';
$string['all_learners'] = 'All learners';
$string['area_desc'] = 'Builds an area chart';
$string['area_name'] = 'Area Chart';
$string['block_add'] = 'Add new Block';
$string['block_delete_message'] = 'Do you really want to permanently delete this block and <br> its reports?';
$string['block_delete_title'] = 'Delete Block';
$string['block_extra'] = 'Advanced chart options';
$string['block_not_found'] = 'Block not found';
$string['block_theme'] = 'Block theme';
$string['block_theme_blue'] = 'Blue Theme';
$string['block_theme_dark'] = 'Dark Theme';
$string['block_theme_green'] = 'Green Theme';
$string['block_theme_light'] = 'Light Theme (default)';
$string['block_theme_orange'] = 'Orange Theme';
$string['block_theme_pink'] = 'Pink Theme';
$string['blocktype_not_found'] = 'Block type not found';
$string['c_enablecompletion'] = 'Completion enabled';
$string['c_format'] = 'Course Format';
$string['c_fullname'] = 'Course name';
$string['c_id'] = 'Course ID';
$string['c_shortname'] = 'Short name';
$string['c_tempo'] = 'Time spent';
$string['c_timemodified'] = 'Modified on';
$string['c_visible'] = 'Visible';
$string['ca_completed_activities'] = 'Completed modules';
$string['cache_time'] = 'Cache time';
$string['cache_time_15min'] = '15 minutes';
$string['cache_time_1d'] = '24 hours';
$string['cache_time_1h'] = '1 hour';
$string['cache_time_30min'] = '30 minutes';
$string['cache_time_6h'] = '6 hours';
$string['cache_time_desc'] = 'Time that SQL results should be cached before being cleared';
$string['cache_time_no'] = 'No cache';
$string['case_complete'] = 'Complete';
$string['case_incomplete'] = 'Incomplete';
$string['case_never_accessed'] = 'Never accessed';
$string['cat_description'] = 'Category description';
$string['cat_edit'] = 'Edit';
$string['cat_name'] = 'Category name';
$string['cat_new'] = 'New category';
$string['cat_not_found'] = 'Category not found';
$string['cat_title'] = 'Category';
$string['cc_id'] = 'Completed courses';
$string['cc_timecompleted'] = 'Completed on';
$string['chart_area_default'] = 'Default Area Chart settings';
$string['chart_column_default'] = 'Default Column Chart settings';
$string['chart_default_desc'] = 'Changing this value will not affect any already added charts.<br>Edit only if you are familiar with Apex Charts. See at <a target="_blank" href="https://apexcharts.com/docs/series/">apexcharts.com/docs</a>';
$string['chart_line_default'] = 'Default Line Chart settings';
$string['chart_pie_default'] = 'Default Pie Chart settings';
$string['city_name'] = 'City';
$string['class_not_found'] = 'Class not found';
$string['click_new_block'] = 'Click on the type of Block you want to add.';
$string['client_name'] = 'Browser';
$string['client_version'] = 'Version';
$string['cm_cmid'] = 'Course Module ID';
$string['column_desc'] = 'Builds a Column chart';
$string['column_name'] = 'Column Chart';
$string['completed_learners'] = 'Learners who completed the courses';
$string['completion_status'] = 'Tracking';
$string['completionstate_status'] = 'Completion';
$string['country_name'] = 'Country';
$string['course_completed'] = 'Completion percentage';
$string['create'] = 'Create';
$string['create_report'] = 'Create report';
$string['css_extra'] = 'Extra CSS';
$string['css_extra_desc'] = 'Add CSS or SCSS styles to this block.<br>The added CSS will only apply to this Block’s contents and will not affect any other part of Moodle';
$string['ctx_instanceid'] = 'Enrolled courses';
$string['data_not_found'] = 'No data found';
$string['delete_report_text'] = 'Do you really want to delete this report?';
$string['delete_report_title'] = 'Delete report';
$string['e_enrol'] = 'Enrollment type';
$string['edit_report'] = 'Edit report';
$string['enrol_status'] = 'Enrollment status';
$string['enrol_timeend'] = 'Enrollment completion';
$string['error_chart_renderer'] = 'Error rendering the chart';
$string['error_data_loader'] = 'Error loading chart data';
$string['expired_enrolments'] = 'Matrículas Expiradas';
$string['extra_langs_customs_title'] = 'To assist you with new strings, I left a few fields blank:';
$string['extra_langs_filter_component'] = 'Component';
$string['extra_langs_header_identifier'] = 'Identifier';
$string['extra_langs_header_lang_key'] = 'Replacement key';
$string['extra_langs_header_string'] = 'Current text';
$string['extra_langs_title'] = 'Existing keys:';
$string['extra_options'] = 'Advanced block options';
$string['firstname'] = 'Student\'s first name';
$string['g_finalgrade'] = 'Final grade';
$string['grade'] = 'Grade';
$string['grade_course'] = 'Grade course';
$string['html_after'] = 'Optional text (or HTML) after the chart';
$string['html_block'] = 'Block HTML';
$string['html_block_desc'] = '<p>The values generated by the SQL should be replaced by keys.</p><p>If you have the SQL <code>SELECT <b>email</b>, <b>fullname</b> FROM mdl_user WHERE id = :userid</code>, you can use the keys <code>{email}</code> and <code>{fullname}</code> in the HTML to replace them.</p>';
$string['html_desc'] = 'Displays a block, with HTML formatting, with data from the database';
$string['html_extra'] = 'Optional text (or HTML) above the chart';
$string['html_name'] = 'HTML Block';
$string['html_sql_warning'] = 'Remember that the SQL below will only return a single row.';
$string['inactive_enrolments'] = 'Matrículas Inativas';
$string['info_desc'] = 'Just an information. Ideal for displaying the student\'s name, enrollment status, etc.';
$string['info_error_sql'] = 'Error executing SQL';
$string['info_name'] = 'Information line';
$string['info_sql_warning'] = 'Remember that the SQL below should return only a single row with one column.';
$string['integracaoroot'] = 'Integrations';
$string['item_not_found'] = 'Item not found';
$string['kopere_bi:manage'] = 'Manage Business Intelligence';
$string['kopere_bi:view'] = 'View Business Intelligence';
$string['l_ip'] = 'IP';
$string['l_origin'] = 'Origin';
$string['l_timecreated'] = 'Created on';
$string['line_desc'] = 'Generates a line chart';
$string['line_name'] = 'Line Chart';
$string['line_sql_warning'] = '<p>Remember that the SQL below should return with the following structure:</p>
<ul>
    <li>The first column should contain the text that will be used as the names of the X-axis.</li>
    <li>The remaining columns should be structured as follows: 
        <ul>
            <li>The name of the column will be used as the name of the series. You can use the translation strings, as explained on the <a href="?classname=bi-extra_langs&method=index" target="_blank">strings page</a>.</li>
            <li>The value of the column will represent the data of the series in the graph.</li>
        </ul>
    </li>
</ul>
<blockquote>In the example below, the first column returns the course name and the second column returns the number of news items for each course: 
<pre>SELECT fullname, 
       newsitems AS "Number of course news items" 
  FROM mdl_course</pre></blockquote>
<blockquote>In the example below, in addition to the first column being the course name, it generates two more lines in the graph, with translations of the column names: 
<pre>  SELECT c.fullname AS course_name,
         COUNT(cm.section) AS \'lang::thiscourse::theme_rebel\',
         COUNT(cm.module)  AS \'lang::ca_completed_activities::local_kopere_bi\'
    FROM mdl_course AS c
    JOIN mdl_course_modules AS cm ON c.id = cm.course
GROUP BY c.id</pre></blockquote>';
$string['loading'] = 'Loading...';
$string['maps_1_city'] = '{a1} and one more city';
$string['maps_desc'] = 'Generates an online students map, based on their IP';
$string['maps_many_city'] = '{a1} and {a2} other cities';
$string['maps_name'] = 'Online students map';
$string['maps_online'] = '{a1} student online';
$string['maps_onlines'] = '{a1} students online';
$string['maps_sql_warning'] = '<p>Remember that the SQL below must return only one column, and that column must contain a valid IP.<br>Example: the SQL <code>SELECT lastip FROM {user} WHERE lastaccess > UNIX_TIMESTAMP() - (10 * 60)</code> returns all students who accessed Moodle in the last 10 minutes</p>';
$string['module_name'] = 'Module name';
$string['modulename'] = 'Business intelligence';
$string['new_block'] = 'New Block on this page';
$string['new_block_1'] = 'One block';
$string['new_block_12'] = 'One plus two Blocks';
$string['new_block_2'] = 'Two blocks';
$string['new_block_21'] = 'Two plus one Blocks';
$string['new_block_25'] = 'One wide and one narrow block';
$string['new_block_3'] = 'Three Blocks';
$string['new_block_4'] = 'Four Blocks';
$string['new_block_52'] = 'One narrow and one wide block';
$string['not_accessed_learners'] = 'Learners who did not access the platform';
$string['not_completed_learners'] = 'Learners who did not complete the courses';
$string['num_activelearners'] = 'Number of active learners';
$string['num_completedlearners'] = 'Number of learners who completed courses';
$string['num_courses'] = 'Number of available courses';
$string['num_learners'] = 'Total number of learners';
$string['num_user'] = 'Number of Students';
$string['os_name'] = 'Operating System';
$string['page_description'] = 'Page description';
$string['page_edit'] = 'Edit Page';
$string['page_name'] = 'Page name';
$string['page_new_cat'] = 'New page';
$string['page_new_sequence'] = 'Drag the Block to change the block order.';
$string['page_not_found'] = 'Page not found';
$string['page_preview'] = 'Page Preview';
$string['page_title_edit'] = 'Edit this Page\'s title';
$string['page_title_export'] = 'Export Page';
$string['pie_desc'] = 'Generates a pie chart';
$string['pie_name'] = 'Pie Chart';
$string['pie_sql_warning'] = '<p>The SQL below must return only two columns.</p><p>The first column will be the name and the second column must be a numeric value.</p>';
$string['pluginname'] = 'Business intelligence';
$string['privacy:metadata'] = 'The Business Intelligence plugin does not store any personal data.';
$string['reload_time'] = 'Reload data every';
$string['reload_time_10m'] = '10 minutes';
$string['reload_time_1h'] = '1 hour';
$string['reload_time_1m'] = '1 minute';
$string['reload_time_20m'] = '20 minutes';
$string['reload_time_2h'] = '2 hours';
$string['reload_time_30m'] = '30 minutes';
$string['reload_time_40m'] = '40 minutes';
$string['reload_time_50m'] = '50 minutes';
$string['reload_time_5m'] = '5 minutes';
$string['reload_time_desc'] = 'Reload data every X minutes. This value must be greater than the cache value!';
$string['reload_time_none'] = 'Never';
$string['report_1_cat_description'] = 'Reports on student performance and progress in their courses.';
$string['report_1_cat_title'] = 'Students';
$string['report_1_categories'] = 'Categories';
$string['report_1_description'] = 'Student status reports';
$string['report_1_modules'] = 'Modules';
$string['report_1_title'] = 'Active Students';
$string['report_1_user_status'] = 'Student Status';
$string['report_1_user_summary'] = 'Summary of Active Students';
$string['report_1_users'] = 'Students';
$string['report_2_cat_description'] = 'Comprehensive analysis of all available courses, performance, progress, and student development.';
$string['report_2_cat_title'] = 'Courses';
$string['report_2_completion_progress'] = 'Progress with completion percentage';
$string['report_2_course_access_statistics'] = 'Course Access Statistics';
$string['report_2_course_analysis_participation_completion'] = 'Course Analysis: Participation and Completion';
$string['report_2_course_progress'] = 'Course Progress';
$string['report_2_description'] = 'Course Report';
$string['report_2_title'] = 'Courses';
$string['report_2_total_engagement'] = 'Total Engagement by Course';
$string['report_3_cat_description'] = 'Report of all online in Moodle';
$string['report_3_cat_title'] = 'Online';
$string['report_3_course_access_time'] = 'Course Access Time';
$string['report_3_description'] = 'Shows online students and top regions';
$string['report_3_operating_systems'] = 'Operating Systems';
$string['report_3_title'] = 'Online Students';
$string['report_3_top_browsers'] = 'Most Used Browsers';
$string['report_3_top_country_access'] = 'Countries with Most Access';
$string['report_3_users_online'] = 'Online Students';
$string['report_3_users_online_list'] = 'List of Online Students';
$string['report_4_cat_description'] = '';
$string['report_4_cat_title'] = 'Estatísticas de Conclusão';
$string['report_4_completion_0'] = 'Rastreamento de conclusão desativado para esta atividade';
$string['report_4_completion_1'] = 'Rastreamento de conclusão manual habilitado para esta atividade';
$string['report_4_completion_2'] = 'Rastreamento de conclusão automático habilitado para esta atividade';
$string['report_4_completion_none'] = 'Conclusão desconhecida';
$string['report_4_completionstate_0'] = 'Atividade não concluída';
$string['report_4_completionstate_1'] = 'Atividade concluída, mas sem especificar aprovação ou reprovação';
$string['report_4_completionstate_2'] = 'Atividade concluída com nota acima da mínima para aprovação';
$string['report_4_completionstate_3'] = 'Atividade concluída com nota abaixo da mínima para aprovação';
$string['report_4_completionstate_4'] = 'Recebeu nota de reprovação em item oculto';
$string['report_4_completionstate_none'] = 'Conclusão desconhecida';
$string['report_4_coursesenrollmentstatus'] = 'Courses enrollment status';
$string['report_4_description'] = '';
$string['report_4_enrolpercourse'] = 'Matriculas por curso';
$string['report_4_title'] = 'Estatísticas';
$string['report_5_cat_description'] = 'This report provides an overview of the main metrics and information of the system, allowing for a comprehensive analysis of its performance and operation.';
$string['report_5_cat_title'] = 'System';
$string['report_5_component'] = 'Component';
$string['report_5_coursefilesizes'] = 'Course files';
$string['report_5_coursefilesizes_title'] = 'Space occupied by each course';
$string['report_5_description'] = 'The General report offers a detailed summary of all aspects of the system, including usage data, performance, and areas for improvement. Ideal for administrators and managers looking to optimize system efficiency.';
$string['report_5_filesize'] = 'File size';
$string['report_5_filesizes'] = 'File sizes';
$string['report_5_modulesdeleting'] = 'Modules deleting';
$string['report_5_modulesfilesizes'] = 'Module files';
$string['report_5_numfiles'] = 'Number of files';
$string['report_5_title'] = 'General';
$string['report_5_upload_title'] = 'Space occupied by each module';
$string['report_6_login'] = 'Logins';
$string['report_6_cat_description'] = 'Login Monitoring and Statistics in the System';
$string['report_6_login_report'] = 'Moodle Login Report';
$string['report_6_cat_title'] = 'Login Reports';
$string['report_7_inactive'] = 'Inactive Enrollments';
$string['report_7_inactive_report'] = 'Inactive Enrollments Report';
$string['report_7_users_not_accessed_course'] = 'Registered users who have not accessed their course';
$string['report_8_engagement'] = 'Engagement';
$string['report_8_student_teacher_engagement'] = 'Student and Teacher Engagement Report';
$string['report_8_teacher_access'] = 'Teacher Access';
$string['report_8_total_engagement_per_course'] = 'Total Engagement by Course';
$string['report_new'] = 'New report for "{$a}"';
$string['report_preview'] = 'Preview report';
$string['report_save'] = 'Save & go to column settings';
$string['report_title'] = 'Report Title';
$string['reports_selectcourse'] = 'Select the course to generate the report';
$string['reports_selectuser'] = 'Select the student to generate the report';
$string['return_edit'] = '<< Return to editing';
$string['rolename'] = 'Role name';
$string['save'] = 'Save';
$string['secounds'] = 'How long';
$string['select_report_select_type'] = 'Select the Report Type';
$string['select_report_select_type_desc'] = 'First, choose which report type you want for this block';
$string['select_report_type'] = 'Report Type';
$string['select_report_type_desc'] = 'You can switch between types "{$a->line}", "{$a->area}", or "{$a->column}"';
$string['setting_apex'] = 'Apex Charts Settings';
$string['setting_apex_desc'] = 'Edit only if you are familiar with Apex Charts. See more at <a target="_blank" href="https://apexcharts.com/docs/series/">apexcharts.com/docs</a>';
$string['sql_read_only'] = 'All SQL queries are protected by a read-only connection, and there is no way to execute INSERT/UPDATE/DELETE commands.';
$string['sql_replace_keys'] = '<h4>Replacement Keys</h4>
<ul>
    <li><b>:userid</b> Student ID to generate the report.</li>
    <li><b>:courseid</b> Course ID to generate the report.</li>
</ul>
<h4>Multi-language</h4>
<p>To return columns that will be translated based on Moodle\'s language packs, it is necessary to follow a specific format that allows strings to be processed and localized properly. The correct format is:</p>
<pre>lang::{identifier}::{component}</pre>
<p>Where:</p>
<ul>
    <li><b>{identifier}</b>: Represents the string identifier, which will be used to fetch the translation from the language pack.</li>
    <li><b>{component}</b>: Refers to the component where the language string is defined, usually the plugin name (for example, <code>mod_forum</code>, <code>local_kopere_dashboard</code>, <code>theme_degrade</code>).</li>
</ul>
<p><em>Example: If you need to return a translated string for the <code>mod_forum</code> component with the identifier <code>postmessage</code>, the return should be structured as follows:</em></p>
<pre>SELECT \'<b>lang::postmessage::mod_forum</b>\' FROM mdl_forum</pre>
<p>Visit the <a href="?classname=bi-extra_langs&method=index" target="_blank">strings page</a> to see all available strings.</p>';
$string['sql_replace_keys_mdl'] = '<h4>The database prefix</h4>
<p>You can always use the <code>mdl_</code> prefix even if your database uses the <code>{$a}</code> prefix. Business intelligence will handle this substitution.</p>';
$string['table_col_title'] = 'Column title';
$string['table_column_not_configured'] = 'Columns not configured in this table';
$string['table_desc'] = 'Displays a table with data pagination.';
$string['table_edit_column'] = 'Column';
$string['table_first_5'] = 'The first five records of the query';
$string['table_info_secound'] = 'Here you can define a name for each column and then specify the desired format for displaying the data.
<ul>
    <li><strong>Do not show this column</strong>: Hides the selected column from view, making it invisible to the student without affecting the stored data.</li>
    <li><strong>No formatting</strong>: Displays the column content exactly as stored, without applying any additional formatting, ensuring raw data visibility.</li>
    <li><strong>Numbers</strong>: Formats the column to display only numerical values, applying standard number display rules such as thousand and decimal separators.</li>
    <li><strong>Convert column to full name "fullname()"</strong>: Executes the fullname() function to create the full name based on the student’s language.</li>
    <li><strong>Convert student ID to profile picture</strong>: Replaces the student ID in the column with their respective profile picture, allowing immediate visual identification of students.</li>
    <li><strong>Binary field to True/False</strong>: Interprets the binary value in the column as a status indicator, where "0" or "false" means inactive and "1" or "true" means active.</li>
    <li><strong>Binary field to Active/Inactive</strong>: Uses the binary value to determine visibility, where "0" or "false" represents Inactive and "1" or "true" represents Active.</li>
    <li><strong>Binary field to Visible/Invisible</strong>: Uses the binary value to determine visibility, where "0" or "false" represents invisible and "1" or "true" represents visible.</li>
    <li><strong>Binary field to active/deleted</strong>: This field identifies the status of an item as active (0) or deleted (1), allowing data management and recovery in storage systems.</li>
    <li><strong>"Time" field formatted to date</strong>: Converts the time (timestamp) value in the column into a readable date, displaying only the date (day/month/year).</li>
    <li><strong>"Time" field formatted to date and time</strong>: Displays the time (timestamp) value in the column as a full date, including the time (day/month/year and hours:minutes).</li>
    <li><strong>"Time" field formatted to time</strong>: Formats the time (timestamp) value in the column to display only the time (hours:minutes), omitting the date.</li>
</ul>';
$string['table_info_topo'] = 'First, you will see a preview of the search results. Then, a sequence of columns will be presented for you to name the titles and define the format of the data for each column.';
$string['table_name'] = 'Data table';
$string['table_renderer_date'] = '"Time" field formatted to date';
$string['table_renderer_datetime'] = '"Time" field formatted to date and time';
$string['table_renderer_deleted'] = 'Binary field to active/deleted';
$string['table_renderer_filesize'] = 'Converts to disk data size';
$string['table_renderer_fullname'] = 'Convert column to full name "fullname()"';
$string['table_renderer_none'] = 'Do not show this column';
$string['table_renderer_number'] = 'Numbers';
$string['table_renderer_seconds'] = '"Time" field formatted to time';
$string['table_renderer_status'] = 'Binary field to Active/Inactive';
$string['table_renderer_title'] = 'Column formatting';
$string['table_renderer_translate'] = 'Use get_string("identifier","component") to translate the column';
$string['table_renderer_truefalse'] = 'Binary field to True/False';
$string['table_renderer_userphoto'] = 'Convert student ID to profile picture';
$string['table_renderer_visible'] = 'Binary field to Visible/Invisible';
$string['theme_palette_default'] = 'Default Palette';
$string['theme_palette_desc'] = 'Colors of this palette:';
$string['theme_palette_desc2'] = 'See all themes here';
$string['theme_palette_palette1'] = 'Palette 1';
$string['theme_palette_palette10'] = 'Palette 10';
$string['theme_palette_palette2'] = 'Palette 2';
$string['theme_palette_palette3'] = 'Palette 3';
$string['theme_palette_palette4'] = 'Palette 4';
$string['theme_palette_palette5'] = 'Palette 5';
$string['theme_palette_palette6'] = 'Palette 6';
$string['theme_palette_palette7'] = 'Palette 7';
$string['theme_palette_palette8'] = 'Palette 8';
$string['theme_palette_palette9'] = 'Palette 9';
$string['theme_palette_title'] = 'Color palette';
$string['timecompleted'] = 'Enrollment completed';
$string['title'] = 'Business intelligence';
$string['u_fullname'] = 'Student name';
$string['u_id'] = 'Student ID';
$string['u_idnumber'] = 'Identification number';
$string['ue_id'] = 'Enrollment ID';
$string['ue_status'] = 'Enrollment status';
$string['ue_timecreated'] = 'Enrollment created on';
$string['ue_timeend'] = 'Enrollment ends on';
$string['ul_timeaccess'] = 'Last access';
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
