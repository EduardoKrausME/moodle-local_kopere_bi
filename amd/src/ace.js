define(["jquery"], function($) {
    return {

        load : function(id_key, type, minLines) {
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