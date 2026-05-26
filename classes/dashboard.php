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

namespace local_kopere_bi;

use context_system;
use Exception;
use local_kopere_bi\block\i_block_provider;
use local_kopere_bi\block\util\code_util;
use local_kopere_bi\block\util\details_util;
use local_kopere_bi\block\util\preview_util;
use local_kopere_bi\block\util\scss_util;
use local_kopere_bi\block\util\string_util;
use local_kopere_bi\filters\filter;
use local_kopere_bi\output\renderer_bi_mustache;
use local_kopere_bi\vo\local_kopere_bi_block;
use local_kopere_bi\vo\local_kopere_bi_cat;
use local_kopere_bi\vo\local_kopere_bi_element;
use local_kopere_bi\vo\local_kopere_bi_page;
use local_kopere_dashboard\html\button;
use local_kopere_bi\form\dynamic_moodleform;
use local_kopere_bi\form\input_select;
use local_kopere_bi\form\input_text;
use local_kopere_bi\form\input_textarea;
use local_kopere_dashboard\util\header;
use local_kopere_dashboard\util\html;
use local_kopere_dashboard\util\message;

/**
 * Class dashboard
 *
 * @package   local_kopere_bi
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dashboard extends bi_all {

    /**
     * Function start
     *
     * @throws Exception
     */
    public function start() {
        global $DB, $CFG, $OUTPUT, $USER, $PAGE;

        $koperebicats = $DB->get_records("local_kopere_bi_cat", null, "sortorder ASC");
        $categorys = [];
        $totalpages = 0;

        /** @var local_kopere_bi_cat $koperebicat */
        foreach ($koperebicats as $koperebicat) {

            $koperebipages = $DB->get_records("local_kopere_bi_page", ["cat_id" => $koperebicat->id], "sortorder ASC");

            $newpages = [];
            /** @var local_kopere_bi_page $koperebipage */
            foreach ($koperebipages as $koperebipage) {
                $user = $DB->get_record("user", ["id" => $koperebipage->user_id]);

                $userfullname = $user ? fullname($user) : "";
                $newpages[] = [
                    "page_id" => $koperebipage->id,
                    "page_title" => string_util::get_string($koperebipage->title),

                    "is_user_fullname" => (bool) strlen($userfullname),
                    "user_fullname" => $userfullname,
                    "description" => string_util::get_string($koperebipage->description),
                ];
            }

            $categorydescription = string_util::get_string($koperebicat->description);
            $categorypagecount = count($newpages);
            $totalpages += $categorypagecount;

            $categorys[] = [
                "pages" => $newpages,
                "has_pages" => $categorypagecount > 0,
                "category_id" => $koperebicat->id,
                "category_title" => string_util::get_string($koperebicat->title),
                "category_description" => $categorydescription,
                "has_category_description" => (bool) strlen(trim($categorydescription)),
                "category_page_count" => $categorypagecount,
            ];
        }

        return $OUTPUT->render_from_template("local_kopere_bi/dashboard-start", [
            "categorys" => $categorys,
            "has_categories" => count($categorys) > 0,
            "total_categories" => count($categorys),
            "total_pages" => $totalpages,
            "editing" => isset($USER->editing) ? $USER->editing : 0,
            "wwwroot" => $CFG->wwwroot,
        ]);
    }

    /**
     * Function edit
     *
     * @throws Exception
     */
    public function edit() {
        global $DB, $PAGE;

        $pageid = optional_param("page_id", false, PARAM_INT);
        $pagetitle = optional_param("page_title", false, PARAM_TEXT);
        $pagedescription = optional_param("page_description", false, PARAM_TEXT);
        $catid = optional_param("cat_id", 0, PARAM_INT);

        if ($pageid) {
            /** @var local_kopere_bi_page $page */
            $page = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
            header::notfound_null($page, get_string("page_not_found", "local_kopere_bi"));
            $title = get_string("page_edit", "local_kopere_bi", string_util::get_string($page->title));

            if (dynamic_moodleform::check_post() && isset($pagetitle[3])) {
                $page->cat_id = $catid;
                $page->title = $pagetitle;
                $page->description = $pagedescription;

                $DB->update_record("local_kopere_bi_page", $page);
                header::location("?classname=dashboard&method=edit_page&page_id={$page->id}");
            }

        } else {
            $page = (object) [
                "id" => 0,
                "cat_id" => $catid,
                "title" => $pagetitle,
                "description" => $pagedescription,
                "time" => time(),
            ];
            $title = get_string("page_new_cat", "local_kopere_bi");

            if (dynamic_moodleform::check_post() && isset($pagetitle[3])) {
                unset($page->id);

                $page->refkey = html::link($page->title);
                $page->id = $DB->insert_record("local_kopere_bi_page", $page);
                header::location("?classname=dashboard&method=edit_page&page_id={$page->id}");
            }
        }

        $PAGE->navbar->add($title);
        $PAGE->set_title($title);

        $return = "";
        $return .= '<div class="element-box">';
        $form = new dynamic_moodleform("?classname=dashboard&method=edit&page_id={$page->id}");

        $koperebicats = $DB->get_records("local_kopere_bi_cat", null, "title ASC");
        foreach ($koperebicats as $key => $koperebicat) {
            $koperebicats[$key]->title = string_util::get_string($koperebicats[$key]->title);
        }
        $form->add_input(
            input_select::new_instance()
                ->set_title(get_string("cat_title", "local_kopere_bi"))
                ->set_name("cat_id")
                ->set_value($page->cat_id)
                ->set_values($koperebicats, "id", "title")
        );

        $form->add_input(
            input_text::new_instance()
                ->set_title(get_string("page_name", "local_kopere_bi"))
                ->set_name("page_title")
                ->set_value($page->title)
                ->set_required()
        );

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("page_description", "local_kopere_bi"))
                ->set_name("page_description")
                ->set_value($page->description)
        );

        if ($pageid) {
            $form->create_submit_input(get_string("save", "local_kopere_bi"));
        } else {
            $form->create_submit_input(get_string("create", "local_kopere_bi"));
        }

        $return .= $form->render();
        $return .= "</div>";

        return $return;
    }

    /**
     * Function edit_cat
     *
     * @throws Exception
     */
    public function edit_cat() {
        global $DB, $PAGE;

        $catid = optional_param("cat_id", 0, PARAM_INT);
        $cattitle = optional_param("cat_title", false, PARAM_TEXT);
        $catdescription = optional_param("cat_description", false, PARAM_TEXT);

        if ($catid) {
            /** @var local_kopere_bi_cat $cat */
            $cat = $DB->get_record("local_kopere_bi_cat", ["id" => $catid]);
            header::notfound_null($cat, get_string("cat_not_found", "local_kopere_bi"));
            $title = get_string("cat_edit", "local_kopere_bi", string_util::get_string($cat->title));

            if (dynamic_moodleform::check_post() && isset($cattitle[3])) {
                $cat->title = $cattitle;
                $cat->description = $catdescription;

                $DB->update_record("local_kopere_bi_cat", $cat);
                header::location("?classname=dashboard&method=start");
            }
        } else {
            $cat = (object) [
                "id" => 0,
                "title" => $cattitle,
                "description" => $catdescription,
            ];
            $title = get_string("cat_new", "local_kopere_bi");

            if (dynamic_moodleform::check_post() && isset($cattitle[3])) {
                unset($cat->id);

                $cat->refkey = html::link($cat->title);
                $cat->id = $DB->insert_record("local_kopere_bi_cat", $cat);
                header::location("?classname=dashboard&method=start");
            }
        }

        $PAGE->navbar->add($title);

        $return = "";
        $return .= '<div class="element-box">';
        $form = new dynamic_moodleform("?classname=dashboard&method=edit_cat&cat_id={$cat->id}");

        $form->add_input(
            input_text::new_instance()
                ->set_title(get_string("cat_name", "local_kopere_bi"))
                ->set_name("cat_title")
                ->set_value($cat->title)
                ->set_required()
        );

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("cat_description", "local_kopere_bi"))
                ->set_name("cat_description")
                ->set_value($cat->description)
        );

        if ($catid) {
            $form->create_submit_input(get_string("save", "local_kopere_bi"));
        } else {
            $form->create_submit_input(get_string("create", "local_kopere_bi"));
        }

        $return .= $form->render();
        $return .= "</div>";
        return $return;
    }

    /**
     * Function delete_cat
     *
     * @throws Exception
     */
    public function delete_cat() {
        global $DB, $PAGE;

        $catid = optional_param("cat_id", 0, PARAM_INT);
        $status = optional_param("status", false, PARAM_TEXT);

        $cat = $DB->get_record("local_kopere_bi_cat", ["id" => $catid]);
        header::notfound_null($cat, get_string("cat_not_found", "local_kopere_bi"));

        if ($status) {
            $pages = $DB->get_records("local_kopere_bi_page", ["cat_id" => $cat->id]);
            foreach ($pages as $page) {
                $blocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $page->id]);
                foreach ($blocks as $block) {
                    $DB->delete_records("local_kopere_bi_block", ["id" => $block->id]);
                    $DB->delete_records("local_kopere_bi_element", ["block_id" => $block->id]);
                }

                $DB->delete_records("local_kopere_bi_page", ["id" => $page->id]);
            }
            $DB->delete_records("local_kopere_bi_cat", ["id" => $cat->id]);

            header::location("?classname=dashboard&method=start");
        } else {
            $PAGE->navbar->add(string_util::get_string($cat->title));
            $PAGE->navbar->add(get_string("delete"));
            $PAGE->set_title(get_string("delete"));

            $title = string_util::get_string($cat->title);
            $return = "";
            $return .= "<div class='element-box'>
                          <h3>" . get_string("category_delete_confirm", "local_kopere_bi") . "</h3>
                          <p>" . get_string("category_delete_message", "local_kopere_bi", $title) . "</p>
                          <div>";
            button::delete(
                get_string("yes"),
                "view-ajax.php?classname=dashboard&method=delete_cat&" .
                http_build_query(["cat_id" => $cat->id, "status" => "sim"], "", "&"), "", false
            );
            button::add(
                get_string("no"),
                "?classname=dashboard&method=start", "margin-left-10", false
            );
            $return .= "    </div>
                      </div>";

            return $return;
        }
    }

    /**
     * Function edit_page
     *
     * @throws Exception
     */
    public function edit_page() {
        global $DB, $PAGE, $OUTPUT;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        $title = string_util::get_string($koperebipage->title);
        $PAGE->navbar->add(
            $title,
            "?classname=dashboard&method=edit_page&page_id={$koperebipage->id}"
        );
        $PAGE->set_title($title);

        $return = "";

        if ($koperebipage->description) {
            $return .= "<p>" . string_util::get_string($koperebipage->description) . "</p>";
        }

        $return .= $OUTPUT->render_from_template(
            "local_kopere_bi/dashboard-topo-edit-page",
            ["koperebipage_id" => $koperebipage->id]
        );

        $return .= '<div class="element-box">';

        $koperebiblocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $koperebipage->id], "sequence ASC");

        $return .= "<div>" . get_string("page_new_sequence", "local_kopere_bi") . "</div>";
        $return .= "<div id='page-block-sort'>";

        /** @var local_kopere_bi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            $return .= (new details_util())->html_details_block($koperebiblock);

            $return .= $OUTPUT->render_from_template(
                "local_kopere_bi/dashboard-dialog-confirm-block",
                ["koperebiblock_id" => $koperebiblock->id]
            );
        }

        $return .= "</div>";

        $PAGE->requires->strings_for_js(["delete", "cancel"], "moodle");
        $PAGE->requires->strings_for_js(["block_add"], "local_kopere_bi");
        $PAGE->requires->strings_for_js(["close"], "admin");
        $PAGE->requires->js_call_amd("local_kopere_bi/page-edit_page", "page_sortable", [$koperebipage->id]);
        $PAGE->requires->js_call_amd("local_kopere_bi/page-edit_page", "page_blocks", [$koperebipage->id]);

        $return .= "</div>";

        $return .= (new details_util())->html_details_add($koperebipage->id);

        return $return;
    }

    /**
     * Function delete_page
     *
     * @return string|void
     * @throws Exception
     */
    public function delete_page() {
        global $DB, $PAGE;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        $status = optional_param("status", false, PARAM_TEXT);

        $page = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($page, get_string("page_not_found", "local_kopere_bi"));

        if ($status) {

            $blocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $page->id]);
            foreach ($blocks as $block) {
                $DB->delete_records("local_kopere_bi_block", ["id" => $block->id]);
                $DB->delete_records("local_kopere_bi_element", ["block_id" => $block->id]);
            }

            $DB->delete_records("local_kopere_bi_page", ["id" => $page->id]);

            header::location("?classname=dashboard&method=start");
        } else {
            $PAGE->navbar->add(string_util::get_string($page->title));
            $PAGE->navbar->add(get_string("delete"));
            $PAGE->set_title(get_string("delete"));

            $return = "";
            $return .= "<div class='element-box'>
                          <h3>" . get_string("page_delete_confirm", "local_kopere_bi") . "</h3>
                          <p>" . get_string("page_delete_message", "local_kopere_bi", string_util::get_string($page->title)) . "</p>
                          <div>";
            button::delete(
                get_string("yes"),
                "view-ajax.php?classname=dashboard&method=delete_page&" .
                http_build_query(["page_id" => $page->id, "status" => "sim"], "", "&"), "", false
            );
            button::add(
                get_string("no"),
                "view-ajax.php?classname=dashboard&method=preview&" . http_build_query(["page_id" => $page->id], "", "&"),
                "margin-left-10", false
            );
            $return .= "    </div>
                      </div>";

            return $return;
        }
    }

    /**
     * Function preview
     *
     * @throws Exception
     */
    public function preview() {
        global $DB, $PAGE;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        $editbooton = "";
        $context = context_system::instance();
        if ($PAGE->user_is_editing() && has_capability("local/kopere_bi:manage", $context)) {
            $editbooton =
                button::add(
                    get_string("page_edit", "local_kopere_bi"),
                    "?classname=dashboard&method=edit_page&page_id={$koperebipage->id}", "ml-2", false, true
                ) .
                button::delete(
                    get_string("delete"),
                    "?classname=dashboard&method=delete_page&page_id={$koperebipage->id}", "ml-3", false, true
                );
        }

        $title = string_util::get_string($koperebipage->title);
        $PAGE->navbar->add(
            $title,
            "?classname=dashboard&method=edit_page&page_id={$koperebipage->id}",
            $editbooton
        );
        $PAGE->set_title($title);

        $return = "";

        if ($koperebipage->description) { // phpcs:disable
            // $return.= "<p class='page-description'>" . string_util::get_string($koperebipage->description) . "</p>";
        }

        $koperebiblocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $koperebipage->id], "sequence ASC");

        $return .= filter::create_filter_page($koperebipage->id);

        /** @var local_kopere_bi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            $preview = new preview_util();
            $return .= $preview->details_block($koperebiblock);
        }

        return $return;
    }

    /**
     * Function type_block_select_type
     *
     * @throws Exception
     */
    public function type_block_select_type() {
        global $DB;

        $blockid = required_param("block_id", PARAM_INT);
        $blocknum = required_param("block_num", PARAM_INT);

        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);
        if ($koperebielement) {
            header::location("?classname=dashboard&method=type_block_edit&item_id={$koperebielement->id}");
        }

        (new type_block())->select_type($blockid, $blocknum);
    }

    /**
     * Function type_block_preview
     *
     * @throws Exception
     */
    public function type_block_preview() {
        global $DB, $PAGE;

        $elementid = optional_param("item_id", 0, PARAM_INT);
        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["id" => $elementid]);
        header::notfound_null($koperebielement, get_string("item_not_found", "local_kopere_bi"));

        $koperebielement->info_obj = @json_decode($koperebielement->info, true);

        /** @var local_kopere_bi_block $block */
        $block = $DB->get_record("local_kopere_bi_block", ["id" => $koperebielement->block_id]);
        header::notfound_null($block, get_string("block_not_found", "local_kopere_bi"));

        /** @var local_kopere_bi_page $page */
        $page = $DB->get_record("local_kopere_bi_page", ["id" => $block->page_id]);
        header::notfound_null($page, get_string("page_not_found", "local_kopere_bi"));

        $PAGE->navbar->add(
            string_util::get_string($page->title),
            "?classname=dashboard&method=edit_page&page_id={$page->id}"
        );
        $title = get_string("report_preview", "local_kopere_bi");
        $PAGE->navbar->add($title);
        $PAGE->set_title($title);

        $return = "";

        $return .= filter::create_filter($koperebielement->commandsql);

        $return .= "<div class='chart-box' id='chart-box-{$koperebielement->id}'>";
        $return .= "<div class='element-box theme-{$koperebielement->theme} type-{$koperebielement->type}'>";

        $return .= "<h4 class='block-title type_block_preview'>" . string_util::get_string($koperebielement->title) . "</h4>";

        $return .= $koperebielement->html_before;

        $class = "\\biblocks_{$koperebielement->type}\\provider";
        if (class_exists($class)) {
            /** @var i_block_provider $block */
            $block = new $class();
            $return .= $block->preview($koperebielement);
        } else {
            $return .= message::danger(get_string("block_not_found", "local_kopere_bi"));
        }

        $return .= renderer_bi_mustache::new_instance()->render_from_string($koperebielement->html_after);

        $return .= scss_util::build_css($koperebielement);

        $return .= "</div></div>";

        return $return;
    }

    /**
     * Function type_block_edit
     *
     * @throws Exception
     */
    public function type_block_edit() {
        global $DB, $PAGE;

        $return = "";

        require_capability("local/kopere_bi:manage", context_system::instance());

        $elementid = optional_param("item_id", 0, PARAM_INT);
        if ($elementid) {
            /** @var local_kopere_bi_element $koperebielement */
            $koperebielement = $DB->get_record("local_kopere_bi_element", ["id" => $elementid]);
            header::notfound_null($koperebielement, get_string("item_not_found", "local_kopere_bi"));
        } else {
            $koperebielement = (object) [
                "title" => "",
                "block_id" => optional_param("block_id", 0, PARAM_INT),
                "block_num" => optional_param("block_num", 0, PARAM_INT),
                "type" => optional_param("type", 0, PARAM_TEXT),
                "commandsql" => "",
                "info" => "{}",
            ];
        }
        $koperebielement->info_obj = @json_decode($koperebielement->info, true);

        /** @var local_kopere_bi_block $koperebiblock */
        $koperebiblock = $DB->get_record("local_kopere_bi_block", ["id" => $koperebielement->block_id]);
        header::notfound_null($koperebiblock, get_string("block_not_found", "local_kopere_bi"));

        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $koperebiblock->page_id]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        /** @var i_block_provider $class */
        $class = "\\biblocks_{$koperebielement->type}\\provider";
        if (!class_exists($class)) {
            $return .= message::danger(get_string("blocktype_not_found", "local_kopere_bi"));
        }

        $block = new $class();

        // Save the data.
        type_block::type_block_edit_salvar($koperebielement, $koperebipage, $block);

        $PAGE->navbar->add(
            string_util::get_string($koperebipage->title),
            "?classname=dashboard&method=edit_page&page_id={$koperebipage->id}"
        );
        if ($elementid) {
            /** @var i_block_provider $blockname */
            $blockname = "\\biblocks_{$koperebielement->type}\\provider";
            if (class_exists($blockname)) {
                $title = string_util::get_string($koperebielement->title) . ": {$blockname::get_name()}";
                $PAGE->navbar->add($title);
                $PAGE->set_title($title);
            } else {
                $title = string_util::get_string($koperebielement->title);
                $PAGE->navbar->add($title);
                $PAGE->set_title($title);
            }
        } else {
            $title = get_string("report_new", "local_kopere_bi", $class::get_name());
            $PAGE->navbar->add($title);
            $PAGE->set_title($title);
        }

        $return .= "<div class='element-box'>";

        if (isset($koperebielement->id)) {
            $return .= "<a href='?classname=dashboard&method=type_block_preview&item_id={$koperebielement->id}'
                     class='btn btn-primary' target='_blank'>" . get_string("report_preview", "local_kopere_bi") . "</a>";
        }

        $form = new dynamic_moodleform("?{$_SERVER["QUERY_STRING"]}");
        $form->add_input(
            input_text::new_instance()
                ->set_title(get_string("report_title", "local_kopere_bi"))
                ->set_name("title")
                ->set_value($koperebielement->title)
        );

        $form->add_input(
            input_select::new_instance()
                ->set_title(get_string("block_theme", "local_kopere_bi"))
                ->set_name("theme")
                ->set_value(@$koperebielement->theme)
                ->set_values([
                    ["key" => "light", "value" => get_string("block_theme_light", "local_kopere_bi")],
                    ["key" => "dark", "value" => get_string("block_theme_dark", "local_kopere_bi")],
                    ["key" => "blue", "value" => get_string("block_theme_blue", "local_kopere_bi")],
                    ["key" => "green", "value" => get_string("block_theme_green", "local_kopere_bi")],
                    ["key" => "orange", "value" => get_string("block_theme_orange", "local_kopere_bi")],
                    ["key" => "pink", "value" => get_string("block_theme_pink", "local_kopere_bi")],
                ])
        );

        ob_start();
        $block->edit($form, $koperebielement);
        $return .= ob_get_clean();

        code_util::estilo($form, $koperebielement);

        if ($block->is_edit_columns()) {
            $form->create_submit_input(get_string("report_save", "local_kopere_bi"));
        } else {
            $form->create_submit_input(get_string("save", "local_kopere_bi"));
        }

        $return .= $form->render();

        $return .= "</div>";

        return $return;
    }

    /**
     * Function type_block_edit_columns
     *
     * @throws Exception
     */
    public function type_block_edit_columns() {
        global $DB, $PAGE;

        $return = "";

        $elementid = optional_param("item_id", 0, PARAM_INT);
        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["id" => $elementid]);
        header::notfound_null($koperebielement, get_string("item_not_found", "local_kopere_bi"));

        $koperebielement->info_obj = @json_decode($koperebielement->info, true);

        /** @var local_kopere_bi_block $koperebiblock */
        $koperebiblock = $DB->get_record("local_kopere_bi_block", ["id" => $koperebielement->block_id]);
        header::notfound_null($koperebiblock, get_string("block_not_found", "local_kopere_bi"));

        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $koperebiblock->page_id]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        /** @var i_block_provider $blockclass */
        $blockclass = "\\biblocks_{$koperebielement->type}\\provider";
        if (!class_exists($blockclass)) {
            $return .= message::danger(get_string("blocktype_not_found", "local_kopere_bi"));
        }

        $block = new $blockclass();

        // Save the data.
        type_block::type_block_edit_columns_salvar($koperebielement, $koperebipage, $block);

        $PAGE->navbar->add(
            string_util::get_string($koperebipage->title),
            "?classname=dashboard&method=edit_page&page_id={$koperebipage->id}"
        );
        $PAGE->navbar->add(string_util::get_string($koperebielement->title) . " - type: {$koperebielement->type}");

        $return .= "<div class='element-box'>";
        $return .= button::edit(
            get_string("return_edit", "local_kopere_bi"),
            "?classname=bi-dashboard&method=type_block_edit&item_id={$koperebielement->id}",
            "", true, true
        );

        $return .= filter::create_filter($koperebielement->commandsql);

        $form = new dynamic_moodleform("?{$_SERVER["QUERY_STRING"]}");

        $caneditcolumns = $block->edit_columns($form, $koperebielement);
        $return .= $caneditcolumns["html"];

        if ($caneditcolumns["status"]) {
            $form->create_submit_input(get_string("save", "local_kopere_bi"));
        }
        $return .= $form->render();

        $return .= "</div>";

        return $return;
    }

    /**
     * Function type_block_delete
     *
     * @throws Exception
     */
    public function type_block_delete() {
        global $DB;

        $elementid = required_param("item_id", PARAM_INT);
        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["id" => $elementid]);
        header::notfound_null($koperebielement, get_string("item_not_found", "local_kopere_bi"));

        /** @var local_kopere_bi_block $block */
        $block = $DB->get_record("local_kopere_bi_block", ["id" => $koperebielement->block_id]);

        $DB->delete_records("local_kopere_bi_element", ["id" => $koperebielement->id]);

        header::location("?classname=bi-dashboard&method=edit_page&page_id={$block->page_id}");
    }

    /**
     * Function delete_block
     *
     * @throws Exception
     */
    public function delete_block() {
        global $DB;

        $blockid = required_param("block_id", PARAM_INT);

        /** @var local_kopere_bi_block $koperebiblock */
        $koperebiblock = $DB->get_record("local_kopere_bi_block", ["id" => $blockid]);
        if (!$koperebiblock) {
            header::location("?classname=bi-dashboard&method=start");
        }

        $DB->delete_records("local_kopere_bi_block", ["id" => $blockid]);
        $DB->delete_records("local_kopere_bi_element", ["block_id" => $blockid]);

        header::location("?classname=bi-dashboard&method=edit_page&page_id={$koperebiblock->page_id}");
    }
}
