<?php
/**
 * Deprecated functions that should be avoided in favor of newer functions. Developers should not use
 * these functions in their parent themes and users should not use these functions in their child themes.
 * All deprecated functions will be removed at some point in a future release.  If your theme is using one
 * of these, you should use the listed alternative or remove it from your theme if necessary.
 *
 * This file also maintains a list of "removed" functions.  Removed functions simply exist as function names
 * for an added layer of protection in the off-chance that a developer failed to switch over to an
 * alternative when the function was first deprecated.  Removed functions are periodically permanently
 * removed from the code base.
 *
 * Functions deprecated prior to the 2.0.0 version are no longer available.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* === Deprecated Functions === */

/* === Removed Functions === */

/* Functions removed in the 4.0 branch. */

function hybrid_attr_header() {}
function hybrid_attr_footer() {}
function hybrid_attr_content() {}
function hybrid_attr_sidebar() {}
function hybrid_attr_menu() {}
function hybrid_attr_head() {}
function hybrid_attr_branding() {}
function hybrid_attr_site_title() {}
function hybrid_attr_site_description() {}
function hybrid_attr_archive_header() {}
function hybrid_attr_archive_title() {}
function hybrid_attr_archive_description() {}
function hybrid_attr_entry_title() {}
function hybrid_attr_entry_author() {}
function hybrid_attr_entry_published() {}
function hybrid_attr_entry_content() {}
function hybrid_attr_entry_summary() {}
function hybrid_attr_entry_terms() {}
function hybrid_attr_comment_author() {}
function hybrid_attr_comment_published() {}
function hybrid_attr_comment_permalink() {}
function hybrid_attr_comment_content() {}

function hybrid_wp_title() {}
function hybrid_comment_reply_link_filter() {}
function hybrid_get_post_template_meta_key() {}
function hybrid_get_post_templates() {}
function hybrid_meta_box_post_add_template() {}
function hybrid_meta_box_post_display_template() {}
function hybrid_meta_box_post_save_template() {}
function loop_pagination() {}
function hybrid_loop_title() {}
function hybrid_get_loop_title() {}
function hybrid_loop_description() {}
function hybrid_get_loop_description() {}
function hybrid_add_post_layout_meta_box() {}
function hybrid_post_layout_enqueue() {}
function hybrid_post_layout_meta_box() {}
function hybrid_save_post_layout() {}
function hybrid_media_meta_factory() {}
function hybrid_layout_factory() {}

function hybrid_style_filter() {}
function hybrid_get_post_style() {}
function hybrid_set_post_style() {}
function hybrid_delete_post_style() {}
function hybrid_has_post_style() {}
function hybrid_get_style_meta_key() {}
function hybrid_add_post_style_meta_box() {}
function hybrid_post_style_meta_box() {}
function hybrid_save_post_style() {}
function hybrid_get_post_styles() {}
function hybrid_admin_load_post_meta_boxes() {}

function hybrid_date_template() {}
function hybrid_user_template() {}
function hybrid_taxonomy_template() {}
function hybrid_singular_template() {}
function hybrid_front_page_template() {}

class Hybrid_Media_Meta_Factory {}
class Hybrid_Layout_Factory {}

/* Fuctions removed in the 3.0 branch. */

// class Hybrid_Customize_Control_Textarea extends WP_Customize_Control {}
// class Hybrid_Customize_Control_Background_Image extends WP_Customize_Background_Image_Control {}
class Hybrid_Theme_Settings{}
function hybrid_get_styles() {}
function hybrid_doctitle() {}
//function hybrid_admin_register_styles() {} // Re-added in 3.0.0.
function hybrid_admin_enqueue_styles() {}
function hybrid_settings_field_id() {}
function hybrid_settings_field_name() {}
function hybrid_get_setting() {}
function hybrid_get_default_theme_settings() {}
function hybrid_enqueue_styles() {}
function hybrid_list_comments_args() {}
function hybrid_get_textdomain() {}
function hybrid_meta_box_post_remove_template() {}
function hybrid_set_prefix() {}
function hybrid_get_prefix() {}
function hybrid_do_atomic() {}
function hybrid_apply_atomic() {}
function hybrid_apply_atomic_shortcode() {}
function hybrid_format_hook() {}
function hybrid_get_attachment_id_from_url() {}
function hybrid_sanitize_meta() {}
function post_layouts_get_layout() {}
function theme_layouts_register_meta() {}
function theme_layouts_sanitize_meta() {}
function theme_layouts_add_post_type_support() {}
function theme_layouts_remove_post_type_support() {}
function theme_layouts_get_layouts() {}
function theme_layouts_get_args() {}
function theme_layouts_filter_layout() {}
function theme_layouts_get_layout() {}
function get_post_layout() {}
function set_post_layout() {}
function delete_post_layout() {}
function has_post_layout() {}
function get_user_layout() {}
function set_user_layout() {}
function delete_user_layout() {}
function has_user_layout() {}
function theme_layouts_body_class() {}
function theme_layouts_strings() {}
function theme_layouts_get_string() {}
function theme_layouts_admin_setup() {}
function theme_layouts_load_meta_boxes() {}
function theme_layouts_add_meta_boxes() {}
function theme_layouts_post_meta_box() {}
function theme_layouts_save_post() {}
function theme_layouts_attachment_fields_to_edit() {}
function theme_layouts_attachment_fields_to_save() {}
function theme_layouts_customize_register() {}
function theme_layouts_customize_preview_script() {}
function theme_layouts_get_meta_key() {}
function hybrid_the_year_shortcode() {}
function hybrid_site_link_shortcode() {}
function hybrid_wp_link_shortcode() {}
function hybrid_theme_link_shortcode() {}
function hybrid_child_link_shortcode() {}
function hybrid_attr_loop_meta() {}
function hybrid_attr_loop_title() {}
function hybrid_attr_loop_description() {}
function hybrid_admin_setup() {}
function hybrid_attachment_id3_keys() {}
function hybrid_image_size_names_choose() {}
function hybrid_meta_template() {}
function hybrid_load_customize_controls() {}
function hybrid_is_textdomain_loaded() {}
function hybrid_get_the_post_format_chat() {}
function hybrid_chat_row_id() {}

/* Functions removed in the 2.0 branch. */

function hybrid_function_removed() {}
function post_format_tools_post_has_content() {}
function post_format_tools_url_grabber() {}
function post_format_tools_get_image_attachment_count() {}
function post_format_tools_get_video() {}
function get_atomic_template() {}
function do_atomic() {}
function apply_atomic() {}
function apply_atomic_shortcode() {}
function hybrid_body_attributes() {}
function hybrid_body_class() {}
function hybrid_get_body_class() {}
function hybrid_footer_content() {}
function hybrid_post_attributes() {}
function hybrid_post_class() {}
function hybrid_entry_class() {}
function hybrid_get_post_class() {}
function hybrid_comment_attributes() {}
function hybrid_comment_class() {}
function hybrid_get_comment_class() {}
function hybrid_avatar() {}
function hybrid_document_title() {}
function hybrid_loginout_link_shortcode() {}
function hybrid_query_counter_shortcode() {}
function hybrid_nav_menu_shortcode() {}
function hybrid_entry_edit_link_shortcode() {}
function hybrid_entry_published_shortcode() {}
function hybrid_entry_comments_link_shortcode() {}
function hybrid_entry_author_shortcode() {}
function hybrid_entry_terms_shortcode() {}
function hybrid_entry_title_shortcode() {}
function hybrid_entry_shortlink_shortcode() {}
function hybrid_entry_permalink_shortcode() {}
function hybrid_post_format_link_shortcode() {}
function hybrid_comment_published_shortcode() {}
function hybrid_comment_author_shortcode() {}
function hybrid_comment_permalink_shortcode() {}
function hybrid_comment_edit_link_shortcode() {}
function hybrid_comment_reply_link_shortcode() {}
function hybrid_get_transient_expiration() {}
function hybrid_translate() {}
function hybrid_translate_plural() {}
function hybrid_gettext() {}
function hybrid_gettext_with_context() {}
function hybrid_ngettext() {}
function hybrid_ngettext_with_context() {}
function hybrid_extensions_gettext() {}
function hybrid_extensions_gettext_with_context() {}
function hybrid_extensions_ngettext() {}
function hybrid_extensions_ngettext_with_context() {}
function hybrid_register_widgets() {}
function hybrid_unregister_widgets() {}
