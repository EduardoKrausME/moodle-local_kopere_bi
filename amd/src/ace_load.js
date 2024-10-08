define(["jquery"], function($) {
    return {

        load_ace_commandsql : function() {
            $.getScript("https://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js", function() {
                var commandsql = $("#commandsql")
                    .hide()
                    .after('<div id="editor_sql_area"></div>')
                    .val();

                $("#editor_sql_area").html("<div id='editor_sql' style='width:100%;height:300px;'>" + commandsql + "</div>");

                var editor = ace.edit("editor_sql");
                editor.setTheme("ace/theme/textmate");
                editor.getSession().setMode("ace/mode/sql");

                editor.session.on('change', function(delta) {
                    $("#commandsql").val(editor.getValue());
                });
            });
        },

        load_ace_infochart_options_area : function() {
            $.getScript("https://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js", function() {
                var infochart_options = $("#infochart_options")
                    .hide()
                    .after('<div id="editor_infochart_options_area"></div>')
                    .val();

                $("#editor_infochart_options_area").html("<div id='editor_infochart_options' style='width:100%;height:500px;'>" + infochart_options + "</div>");

                ace.config.set("basePath", M.cfg.wwwroot + "/local/kopere_bi/amd/src/ace-sources/");
                var editor = ace.edit("editor_infochart_options");
                editor.setTheme("ace/theme/textmate");
                editor.getSession().setMode("ace/mode/json5");

                editor.session.on('change', function(delta) {
                    $("#infochart_options").val(editor.getValue());
                });
            });
        },

        load_ace_css : function() {
            $.getScript("https://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js", function() {
                var $editor = $('<div id="editor_css" style="height:200px;"></div>');
                var $input = $("#css");
                var html = $input
                    .hide()
                    .after($editor)
                    .val();

                html = html.split("<").join("&lt;");
                html = html.split(">").join("&gt;");
                $editor.html(html);

                ace.config.set("basePath", M.cfg.wwwroot + "/local/kopere_bi/amd/src/ace-sources/");
                var editor = ace.edit("editor_css");
                editor.setTheme("ace/theme/textmate");
                editor.getSession().setMode("ace/mode/css");

                editor.session.on("change", function(delta) {
                    $input.val(editor.getValue());
                });

            });
        },

        load_ace_html : function(id_key) {
            $.getScript("https://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js", function() {
                var $editor = $('<div id="editor_' + id_key + '" style="height:200px;"></div>');
                var $input = $("#" + id_key);
                var html = $input
                    .hide()
                    .after($editor)
                    .val();

                html = html.split("<").join("&lt;");
                html = html.split(">").join("&gt;");
                $editor.html(html);

                ace.config.set("basePath", M.cfg.wwwroot + "/local/kopere_bi/amd/src/ace-sources/");
                var editor = ace.edit("editor_" + id_key);
                editor.setTheme("ace/theme/textmate");
                editor.getSession().setMode("ace/mode/html");

                editor.session.on("change", function(delta) {
                    $input.val(editor.getValue());
                });

            });
        }

    };
});