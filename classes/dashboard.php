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

use local_kopere_bi\local\block\i_type;
use local_kopere_bi\local\block\util\code_util;
use local_kopere_bi\output\renderer_bi_mustache;
use local_kopere_bi\local\util\details_util;
use local_kopere_bi\local\util\filter;
use local_kopere_bi\local\util\preview_util;
use local_kopere_bi\local\util\scss_util;
use local_kopere_bi\local\util\string_util;
use local_kopere_bi\local\vo\local_kopere_bi_block;
use local_kopere_bi\local\vo\local_kopere_bi_cat;
use local_kopere_bi\local\vo\local_kopere_bi_element;
use local_kopere_bi\local\vo\local_kopere_bi_page;
use local_kopere_dashboard\html\button;
use local_kopere_dashboard\html\form;
use local_kopere_dashboard\html\inputs\input_select;
use local_kopere_dashboard\html\inputs\input_text;
use local_kopere_dashboard\html\inputs\input_textarea;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\header;
use local_kopere_dashboard\util\message;

/**
 * Class dashboard
 *
 * @package   local_kopere_bi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dashboard extends bi_all {

    /**
     * Function start
     *
     * @throws \Exception
     */
    public function start() {
        global $DB, $CFG, $OUTPUT, $USER;

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::start_page();

        $koperebicats = $DB->get_records("local_kopere_bi_cat", null, "sortorder ASC");
        $categorys = [];

        /** @var local_kopere_bi_cat $koperebicat */
        foreach ($koperebicats as $koperebicat) {

            $koperebipages = $DB->get_records("local_kopere_bi_page", ["cat_id" => $koperebicat->id], "sortorder ASC");

            $newpages = [];
            /** @var local_kopere_bi_page $koperebipage */
            foreach ($koperebipages as $koperebipage) {
                $user = $DB->get_record("user", ["id" => $koperebipage->user_id]);

                $userfullname = $user ? fullname($user) : "";
                $descriptioncrop = string_util::trunc($koperebipage->description, 100);

                $newpages[] = [
                    "page_id" => $koperebipage->id,
                    "page_title" => string_util::get_string($koperebipage->title),

                    "is_user_fullname" => (bool)strlen($userfullname),
                    "user_fullname" => $userfullname,

                    "is_description_crop" => (bool)strlen($descriptioncrop),
                    "description_crop" => $descriptioncrop,
                ];
            }

            $categorys[] = [
                "pages" => $newpages,
                "category_id" => $koperebicat->id,
                "category_title" => string_util::get_string($koperebicat->title),
                "category_description" => string_util::get_string($koperebicat->description),
            ];
        }

        echo $OUTPUT->render_from_template("local_kopere_bi/dashboard/dashboard_start", [
            "categorys" => $categorys,
            "editing" => isset($USER->editing) ? $USER->editing : 0,
            "wwwroot" => $CFG->wwwroot,
        ]);

        dashboard_util::end_page();
    }

    /**
     * Function edit
     *
     * @throws \Exception
     */
    public function edit() {
        global $DB;

        $pageid = optional_param("page_id", false, PARAM_INT);
        $pagetitle = optional_param("page_title", false, PARAM_TEXT);
        $pagedescription = optional_param("page_description", false, PARAM_TEXT);
        $catid = optional_param("cat_id", 0, PARAM_INT);

        if ($pageid) {
            /** @var local_kopere_bi_page $page */
            $page = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
            header::notfound_null($page, get_string("page_not_found", "local_kopere_bi"));
            $title = get_string("page_edit", "local_kopere_bi", string_util::get_string($page->title));

            if (form::check_post() && isset($pagetitle[3])) {
                $page->cat_id = $catid;
                $page->title = $pagetitle;
                $page->description = $pagedescription;

                $DB->update_record("local_kopere_bi_page", $page);
                header::location("?classname=bi-dashboard&method=edit_page&page_id={$page->id}");
            }

        } else {
            $page = (object)[
                "id" => 0,
                "cat_id" => $catid,
                "title" => $pagetitle,
                "description" => $pagedescription,
                "time" => time(),
            ];
            $title = get_string("page_new_cat", "local_kopere_bi");

            if (form::check_post() && isset($pagetitle[3])) {
                unset($page->id);

                $page->refkey = uniqid();
                $page->id = $DB->insert_record("local_kopere_bi_page", $page);
                header::location("?classname=bi-dashboard&method=edit_page&page_id={$page->id}");
            }
        }

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb($title);
        dashboard_util::start_page();

        echo '<div class="element-box">';
        $form = new form("?classname=bi-dashboard&method=edit&page_id={$page->id}");

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
            $form->create_submit_input(get_string("save", "local_kopere_bi"), "button");
        } else {
            $form->create_submit_input(get_string("create", "local_kopere_bi"), "button");
        }

        echo "</div>";
        dashboard_util::end_page();
    }

    /**
     * Function edit_cat
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function edit_cat() {
        global $DB;

        $catid = optional_param("cat_id", 0, PARAM_INT);
        $cattitle = optional_param("cat_title", false, PARAM_TEXT);
        $catdescription = optional_param("cat_description", false, PARAM_TEXT);

        if ($catid) {
            /** @var local_kopere_bi_cat $cat */
            $cat = $DB->get_record("local_kopere_bi_cat", ["id" => $catid]);
            header::notfound_null($cat, get_string("cat_not_found", "local_kopere_bi"));
            $title = get_string("cat_edit", "local_kopere_bi", $cat->title);

            if (form::check_post() && isset($cattitle[3])) {
                $cat->title = $cattitle;
                $cat->description = $catdescription;

                $DB->update_record("local_kopere_bi_cat", $cat);
                header::location("?classname=bi-dashboard&method=start");
            }
        } else {
            $cat = (object)[
                "id" => 0,
                "title" => $cattitle,
                "description" => $catdescription,
            ];
            $title = get_string("cat_new", "local_kopere_bi");

            if (form::check_post() && isset($cattitle[3])) {
                unset($cat->id);

                $cat->refkey = uniqid();
                $cat->id = $DB->insert_record("local_kopere_bi_cat", $cat);
                header::location("?classname=bi-dashboard&method=start");
            }
        }

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb($title);
        dashboard_util::start_page();

        echo '<div class="element-box">';
        $form = new form("?classname=bi-dashboard&method=edit_cat&cat_id={$cat->id}");

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
            $form->create_submit_input(get_string("save", "local_kopere_bi"), "button");
        } else {
            $form->create_submit_input(get_string("create", "local_kopere_bi"), "button");
        }

        echo "</div>";
        dashboard_util::end_page();
    }

    /**
     * Function edit_page
     *
     * @throws \Exception
     */
    public function edit_page() {
        global $DB, $PAGE, $OUTPUT;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(string_util::get_string($koperebipage->title),
            "?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}");
        dashboard_util::start_page();

        if ($koperebipage->description) {
            echo "<p>" . string_util::get_string($koperebipage->description) . "</p>";
        }

        echo $OUTPUT->render_from_template("local_kopere_bi/dashboard/topo-edit-page",
            ["koperebipage_id" => $koperebipage->id]);

        echo '<div class="element-box">';

        $koperebiblocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $koperebipage->id], "sequence ASC");

        echo "<div>" . get_string("page_new_sequence", "local_kopere_bi") . "</div>";
        echo "<div id='page-block-sort'>";

        /** @var local_kopere_bi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            echo (new details_util())->html_details_block($koperebiblock);

            echo $OUTPUT->render_from_template("local_kopere_bi/dashboard/dialog-confirm-block",
                ["koperebiblock_id" => $koperebiblock->id]);
        }

        echo "</div>";

        $PAGE->requires->strings_for_js(["delete", "cancel"], "moodle");
        $PAGE->requires->strings_for_js(["block_add"], "local_kopere_bi");
        $PAGE->requires->strings_for_js(["close"], "admin");
        $PAGE->requires->js_call_amd("local_kopere_bi/page-edit_page", "page_sortable", [$koperebipage->id]);
        $PAGE->requires->js_call_amd("local_kopere_bi/page-edit_page", "page_blocks", [$koperebipage->id]);

        echo "</div>";

        (new details_util())->html_details_add($koperebipage->id);

        dashboard_util::end_page();
    }

    /**
     * Function preview
     *
     * @throws \Exception
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    public function preview() {
        global $DB, $USER;

        $pageid = optional_param("page_id", 0, PARAM_INT);
        /** @var local_kopere_bi_page $koperebipage */
        $koperebipage = $DB->get_record("local_kopere_bi_page", ["id" => $pageid]);
        header::notfound_null($koperebipage, get_string("page_not_found", "local_kopere_bi"));

        $editbooton = "";
        $context = \context_system::instance();
        if (isset($USER->editing) && $USER->editing) {
            $editbooton = has_capability('moodle/site:config', $context) ?
                button::add(get_string("page_edit", "local_kopere_bi"),
                    "?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}",
                    "margin-left-10", false, true) : "";
        }

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(string_util::get_string($koperebipage->title),
            "?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}",
            $editbooton);
        dashboard_util::start_page();

        if ($koperebipage->description) { // phpcs:disable
            // echo "<p class='page-description'>" . string_util::get_string($koperebipage->description) . "</p>";
        }

        $koperebiblocks = $DB->get_records("local_kopere_bi_block", ["page_id" => $koperebipage->id], "sequence ASC");

        echo filter::create_filter_page($koperebipage->id);

        /** @var local_kopere_bi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            $preview = new preview_util();
            echo $preview->details_block($koperebiblock);
        }

        dashboard_util::end_page();
    }

    /**
     * Function type_block_select_type
     *
     * @throws \Exception
     */
    public function type_block_select_type() {
        global $DB;

        $blockid = required_param("block_id", PARAM_INT);
        $blocknum = required_param("block_num", PARAM_INT);

        /** @var local_kopere_bi_element $koperebielement */
        $koperebielement = $DB->get_record("local_kopere_bi_element", ["block_id" => $blockid, "block_num" => $blocknum]);
        if ($koperebielement) {
            header::location("?classname=bi-dashboard&method=type_block_edit&item_id={$koperebielement->id}");
        }

        (new type_block())->select_type($blockid, $blocknum);
    }

    /**
     * Function type_block_preview
     *
     * @throws \Exception
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    public function type_block_preview() {
        global $DB;

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

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(string_util::get_string($page->title),
            "?classname=bi-dashboard&method=edit_page&page_id={$page->id}");
        dashboard_util::add_breadcrumb(get_string("report_preview", "local_kopere_bi"));
        dashboard_util::start_page();

        echo filter::create_filter($koperebielement->commandsql);

        echo "<div class='chart-box' id='chart-box-{$koperebielement->id}'>";
        echo "<div class='element-box theme-{$koperebielement->theme} type-{$koperebielement->type}'>";

        echo "<h4 class='block-title type_block_preview'>" . string_util::get_string($koperebielement->title) . "</h4>";

        echo $koperebielement->html_before;

        $class = "\\local_kopere_bi\\local\\block\\{$koperebielement->type}";
        if (class_exists($class)) {
            /** @var i_type $block */
            $block = new $class();
            echo $block->preview($koperebielement);
        } else {
            message::print_danger(get_string("block_not_found", "local_kopere_bi"));
        }

        echo renderer_bi_mustache::new_instance()->render_from_string($koperebielement->html_after);

        echo scss_util::build_css($koperebielement);

        echo "</div></div>";

        dashboard_util::end_page();
    }

    /**
     * Function type_block_edit
     *
     * @throws \Exception
     */
    public function type_block_edit() {
        global $DB;

        $elementid = optional_param("item_id", 0, PARAM_INT);
        if ($elementid) {
            /** @var local_kopere_bi_element $koperebielement */
            $koperebielement = $DB->get_record("local_kopere_bi_element", ["id" => $elementid]);
            header::notfound_null($koperebielement, get_string("item_not_found", "local_kopere_bi"));
        } else {
            $koperebielement = (object)[
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

        /** @var i_type $class */
        $class = "\\local_kopere_bi\\local\\block\\{$koperebielement->type}";
        if (!class_exists($class)) {
            message::print_danger(get_string("blocktype_not_found", "local_kopere_bi"));
        }

        /** @var i_type $block */
        $block = new $class();

        // Save the data.
        type_block::type_block_edit_salvar($koperebielement, $koperebipage, $block);

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(string_util::get_string($koperebipage->title),
            "?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}");
        if ($elementid) {
            /** @var i_type $blockname */
            $blockname = "\\local_kopere_bi\\local\\block\\{$koperebielement->type}";
            if (class_exists($blockname)) {
                dashboard_util::add_breadcrumb(string_util::get_string($koperebielement->title) . ": {$blockname::get_name()}");
            } else {
                dashboard_util::add_breadcrumb(string_util::get_string($koperebielement->title));
            }
        } else {
            dashboard_util::add_breadcrumb(get_string("report_new", "local_kopere_bi", $class::get_name()));
        }
        dashboard_util::start_page();

        echo "<div class='element-box'>";

        if (isset($koperebielement->id)) {
            echo "<a href='?classname=bi-dashboard&method=type_block_preview&item_id={$koperebielement->id}'
                     class='btn btn-primary' target='_blank'>" . get_string("report_preview", "local_kopere_bi") . "</a>";
        }

        $form = new form("?{$_SERVER["QUERY_STRING"]}");
        $form->add_input(
            input_text::new_instance()
                ->set_title(get_string("report_title", "local_kopere_bi"))
                ->set_name("title")
                ->set_value($koperebielement->title));

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
                ]));

        $block->edit($form, $koperebielement);

        code_util::estilo($form, $koperebielement);

        if ($block->is_edit_columns()) {
            $form->create_submit_input(get_string("report_save", "local_kopere_bi"));
        } else {
            $form->create_submit_input(get_string("save", "local_kopere_bi"));
        }

        $form->close();

        echo "</div>";

        dashboard_util::end_page();
    }

    /**
     * Function type_block_edit_columns
     *
     * @throws \Exception
     */
    public function type_block_edit_columns() {
        global $DB;

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

        /** @var i_type $blockclass */
        $blockclass = "\\local_kopere_bi\\local\\block\\{$koperebielement->type}";
        if (!class_exists($blockclass)) {
            message::print_danger(get_string("blocktype_not_found", "local_kopere_bi"));
        }

        /** @var i_type $block */
        $block = new $blockclass();

        // Save the data.
        type_block::type_block_edit_columns_salvar($koperebielement, $koperebipage, $block);

        dashboard_util::add_breadcrumb(get_string("title", "local_kopere_bi"), "?classname=bi-dashboard&method=start");
        dashboard_util::add_breadcrumb(string_util::get_string($koperebipage->title),
            "?classname=bi-dashboard&method=edit_page&page_id={$koperebipage->id}");
        dashboard_util::add_breadcrumb(string_util::get_string($koperebielement->title) . " - type: {$koperebielement->type}");
        dashboard_util::start_page();

        echo "<div class='element-box'>";
        button::edit(get_string("return_edit", "local_kopere_bi"),
            "?classname=bi-dashboard&method=type_block_edit&item_id={$koperebielement->id}");

        echo filter::create_filter($koperebielement->commandsql);

        $form = new form("?{$_SERVER["QUERY_STRING"]}");

        if ($block->edit_columns($form, $koperebielement)) {
            $form->create_submit_input(get_string("save", "local_kopere_bi"));
        }
        $form->close();

        echo "</div>";

        dashboard_util::end_page();
    }

    /**
     * Function type_block_delete
     *
     * @throws \Exception
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
     * @throws \Exception
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
