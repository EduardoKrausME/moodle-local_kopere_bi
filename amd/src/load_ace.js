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
 * load_ace file
 *
 * @package   local_kopere_bi
 * @copyright 2025 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery"], function($) {
    return {

        getScript : function(id_key, type, minLines) {
            $.getScript("https://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js", function() {
                var html = `
                    <div id="editor_${id_key}_area">
                        <div id='editor_${id_key}' style='width:100%;height:300px;'>${inputhtml}</div>
                    </div>`;
                var inputhtml = $(`#${id_key}`)
                    .hide()
                    .after(html)
                    .val();
                var editorDiv = $(`#editor_${id_key}`);

                var editor = ace.edit(`editor_${id_key}`);
                editor.setTheme(`ace/theme/textmate`);
                editor.getSession().setMode(`ace/mode/${type}`);
                editor.setValue(inputhtml);

                editor.session.on("change", function(delta) {
                    $(`#${id_key}`).val(editor.getValue());

                    var numLines = editor.getSession().getDocument().getLength();
                    var newHeight = editor.renderer.lineHeight * numLines;
                    if (newHeight < 100) {
                        newHeight = 100;
                    }
                    editorDiv.height(newHeight);
                    editor.resize();
                });
                editor.session.on("changeAnnotation", function() {
                    var annotations = editor.session.getAnnotations() || [], i = len = annotations.length;
                    while (i--) {
                        if (/doctype first\. Expected/.test(annotations[i].text)) {
                            annotations.splice(i, 1);
                        }
                    }
                    if (len > annotations.length) {
                        editor.session.setAnnotations(annotations);
                    }
                });

                if (!minLines) {
                    minLines = 6;
                }

                editor.setOptions({
                    // maxLines : 21,
                    minLines : minLines,
                });

                window[`editor_${id_key}`] = editor;
            });
        },

    };
});