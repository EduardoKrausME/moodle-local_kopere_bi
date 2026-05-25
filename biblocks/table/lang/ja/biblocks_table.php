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

$string['pluginname'] = 'データテーブル';
$string['pluginname_desc'] = 'データのページネーション付きテーブルを表示します。';
$string['table_col_title'] = 'カラムタイトル';
$string['table_column_not_configured'] = 'このテーブルにはカラムが設定されていません';
$string['table_edit_column'] = 'カラム';
$string['table_first_records'] = 'クエリの最初の10件のレコード';
$string['table_info_topo'] = '最初に、クエリ結果のプレビューが表示されます。その後、各カラムのタイトル名を付け、各カラムのデータ形式を定義するための一連のカラムが表示されます。';
$string['table_info_types'] = 'ここでは、カラム名を設定し、希望する形式を指定できます。また、Mustache を使った追加の書式設定を行うかどうかも指定できます。
<ul>
    <li><strong>書式なし</strong>: コンテンツをそのまま表示するか、追加した場合は Mustache を適用します。</li>
    <li><strong>このカラムを表示しない</strong>: 選択したカラムを表示上は非表示にしますが、データは Mustache 処理で引き続き利用できます。</li>
    <ul><li>Mustache は利用できません</li></ul>
    <li><strong>数値</strong>: カラムを数値のみ表示するように書式設定し、桁区切りや小数点区切りなど、標準の数値表示ルールを適用します。</li>
    <ul><li>Mustache は利用できません</li></ul>
    <li><strong>カラムをフルネーム "fullname()" に変換</strong>: <code>fullname()</code> 関数を実行して言語に基づくフルネームを生成し、この同じカラムに保存します。これを機能させるには、<code>lastname</code> カラムが必要で、可能であれば非表示にしてください。</li>
    <li><strong>学生IDをプロフィール画像に変換</strong>: このカラムのIDを使用してプロフィール画像を作成します。</li>
    <li><strong>表示/非表示のバイナリフィールド</strong>: バイナリ値を使用して表示状態を判定します。"0"/"false" は非表示、"1"/"true" は表示を意味します。</li>
    <li><strong>有効/無効のバイナリフィールド</strong>: バイナリ値を使用して状態を判定します。"0"/"false" は無効、"1"/"true" は有効を意味します。</li>
    <li><strong>"Time" フィールドを日付として書式設定</strong>: カラム内の時間値（timestamp）を読みやすい日付に変換し、日付のみ（日/月/年）を表示します。</li>
    <ul><li>Mustache は利用できません</li></ul>
    <li><strong>"Time" フィールドを日付と時刻として書式設定</strong>: カラム内の時間値（timestamp）を、時刻を含む完全な日付（日/月/年および時:分）として表示します。</li>
    <ul><li>Mustache は利用できません</li></ul>
    <li><strong>"Time" フィールドを時刻として書式設定</strong>: カラム内の時間値（timestamp）を、日付を省略して時刻のみ（時:分）として表示するように書式設定します。</li>
    <ul><li>Mustache は利用できません</li></ul>
</ul>';
$string['table_renderer_date'] = '"Time" フィールドを日付として書式設定';
$string['table_renderer_datetime'] = '"Time" フィールドを日付と時刻として書式設定';
$string['table_renderer_filesize'] = 'ディスク上のデータサイズに変換';
$string['table_renderer_mustache'] = 'カラムのHTML
<a href="https://moodledev.io/docs/guides/templates" target="_blank">Mustache</a>';
$string['table_renderer_none'] = 'このカラムを表示しない';
$string['table_renderer_number'] = '数値';
$string['table_renderer_seconds'] = '"Time" フィールドを時刻として書式設定';
$string['table_renderer_status'] = '有効/無効のバイナリフィールド';
$string['table_renderer_title'] = 'カラムの書式設定';
$string['table_renderer_translate'] = 'カラムを翻訳するには get_string("identifier", "component") を使用します';
$string['table_renderer_userfullname'] = 'カラムを "fullname()" 関数で学生のフルネームに変換';
$string['table_renderer_userphoto'] = '学生IDをプロフィール画像に変換';
$string['table_renderer_visible'] = '表示/非表示のバイナリフィールド';
