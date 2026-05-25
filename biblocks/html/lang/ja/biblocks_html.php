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
 * @package   biblocks_html
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['html_block'] = 'Mustache 対応 HTML ブロック';
$string['html_block_desc'] = '<p>このフィールドに追加する HTML は <strong>Mustache</strong> 形式に従う必要があります。これにより、ページ内でデータを動的に差し替えることができます。HTML 内で SQL カラムの値を直接参照するには、二重波括弧 <code>{{ }}</code> を使用し、データが正しく挿入されるようにします。</p>
<blockquote>
    <p>たとえば、次の SQL クエリを使用します。</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE id = :userid</pre>
    <p>返された値は、次の構文を使用して HTML 内で参照できます。</p>
    <pre>&lt;p&gt;メール: {{{email}}}&lt;/p&gt;
    &lt;p&gt;氏名: {{{firstname}}} {{{lastname}}}&lt;/p&gt;</pre>
</blockquote>
<blockquote>
    <p>この例では、SQL の結果から複数行を表示します。この SQL は、手動認証で登録されたユーザーの一覧を返します。使用する SQL は次のとおりです。</p>
    <pre>SELECT firstname, lastname, email FROM mdl_user WHERE auth = \'manual\'</pre>
    <p>結果を繰り返し処理して表形式でデータを表示するために、テンプレート内で <code>{{#lines}}</code> を使用し、返された各レコードごとに内容を繰り返します。テンプレートは次のようになります。</p>
    <pre>&lt;table class="table table-bordered"&gt;
    &lt;tr&gt;
        &lt;th&gt;メール&lt;/th&gt;
        &lt;th&gt;氏名&lt;/th&gt;
    &lt;/tr&gt;
    <strong>{{#lines}}</strong>
        &lt;tr&gt;
            &lt;td&gt;{{{email}}}&lt;/td&gt;
            &lt;td&gt;{{{firstname}}} {{{lastname}}}&lt;/td&gt;
        &lt;/tr&gt;
    <strong>{{/lines}}</strong>
&lt;/table&gt;</pre>
</blockquote>
<p>三重波括弧 <code>{{{ }}}</code> を使用すると、HTML エスケープを行わずに値を挿入できます。これは、HTML タグを含む可能性があるコンテンツを表示する場合に便利です。</p>
<p>Moodle で Mustache テンプレートを使用する方法の詳細については、公式ドキュメントを参照してください: <a href="https://moodledev.io/docs/guides/templates" target="_blank">Moodle テンプレートガイド</a>.</p>';
$string['pluginname'] = 'HTML ブロック';
$string['pluginname_desc'] = 'データベースから取得したデータを使用して HTML ブロックを表示します';
