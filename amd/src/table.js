define(["jquery"], function($) {
    return {
        select : function(collkey, changemustache) {

            var $select = $(`#columntype${collkey}`);
            $select.change(function() {
                var select_key = $select.val();
                mustache(select_key, 300, true);
            });

            mustache($select.val(), 0, changemustache);

            function mustache(select_key, time, _changemustache) {
                console.log([
                    collkey, select_key, time, changemustache, _changemustache
                ]);
                var mustachehtml = false;

                $(`#area_column-title${collkey}`).show();
                switch (select_key) {
                    case "none":
                        hide_mustache(time);
                        $(`#area_column-title${collkey}`).hide();
                        break;
                    case "string":
                        show_mustache(time);
                        if (_changemustache) {
                            if (collkey == "email") {
                                mustachehtml = `<a href="mailto:{{{${collkey}}}}" target="_blank">{{{${collkey}}}}</a>`;
                                set_mustache(mustachehtml);
                            } else if (collkey.indexOf("phone") === 0) {
                                mustachehtml = `<a href="tel:{{{${collkey}}}}" target="_blank">{{{${collkey}}}}</a>`;
                                set_mustache(mustachehtml);
                            } else if (collkey == "c_fullname") {
                                mustachehtml =
                                    `<a href="{{{config.wwwroot}}}/course/view.php?id={{{c_id}}}"\n` +
                                    `   target="course">{{{c_fullname}}}</a>`;
                                set_mustache(mustachehtml);
                            } else {
                                mustachehtml = `{{{${collkey}}}}`;
                                set_mustache(mustachehtml);
                            }
                        }
                        break;
                    case "number":
                        hide_mustache(time);
                        break;
                    case "userfullname":
                        show_mustache(time);
                            mustachehtml =
                                `<a href="{{{config.wwwroot}}}/user/view.php?id={{{u_id}}}"\n` +
                                `   target="profile">{{{u_fullname}}}</a>`;
                            set_mustache(mustachehtml);
                        break;
                    case "userphotoRenderer":
                        hide_mustache(time);
                        break;
                    case "visibleRenderer":
                        show_mustache(time);
                            if (true) {
                            mustachehtml =
                                `{{#${collkey}}}\n`+
                                `    <span class="w-100 badge bg-success">\n` +
                                `        {{#str}} emaildisplayno,moodle {{/str}}\n` +
                                `    </span>\n`+
                                `{{/${collkey}}}\n`+
                                `{{^${collkey}}}\n`+
                                `    <span class="w-100 badge bg-danger">\n` +
                                `        {{#str}} visible,moodle {{/str}}\n` +
                                `    </span>\n`+
                                `{{/${collkey}}}`;
                            set_mustache(mustachehtml);
                        }
                        break;
                    case "statusRenderer":
                        show_mustache(time);
                        if (true) {
                            mustachehtml =
                                `{{#${collkey}}}\n`+
                                `    <span class="w-100 badge bg-danger">\n` +
                                `        {{#str}} inactive,moodle {{/str}}\n` +
                                `    </span>\n`+
                                `{{/${collkey}}}\n`+
                                `{{^${collkey}}}\n`+
                                `    <span class="w-100 badge bg-success">\n` +
                                `        {{#str}} active,moodle {{/str}}\n` +
                                `    </span>\n`+
                                `{{/${collkey}}}`;
                            set_mustache(mustachehtml);
                        }
                        break;
                    case "dateRenderer":
                        hide_mustache(time);
                        break;
                    case "datetimeRenderer":
                        hide_mustache(time);
                        break;
                    case "timeRenderer":
                        hide_mustache(time);
                        break;
                    case "translate":
                        show_mustache(time);
                        break;
                    case "filesizeRenderer":
                        hide_mustache(time);
                        break;
                }
            }

            function show_mustache(time) {
                $(`#area_column-mustache${collkey}`).show(time);
            }

            function hide_mustache(time) {
                $(`#area_column-mustache${collkey}`).hide(time);
            }

            function set_mustache(mustachehtml) {
                console.log([
                    collkey, mustachehtml
                ]);
                console.log(`editor_columnmustache${collkey}`);
                if (window[`editor_columnmustache${collkey}`]) {
                    console.log("aaaa2");
                    window[`editor_columnmustache${collkey}`].setValue(mustachehtml);
                } else {
                    console.log("bbb2");
                }
                $(`#columnmustache${collkey}`).val(mustachehtml);
            }
        }
    };
});