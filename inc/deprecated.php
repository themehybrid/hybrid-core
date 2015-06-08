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
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* === Deprecated Functions === */

/**
 * Loop pagination function for paginating loops with multiple posts.  This should be used on archive, blog, and
 * search pages.  It is not for singular views.
 *
 * @since      loop-pagination-0.1.0
 * @deprecated 3.0.0
 * @access     public
 * @param      array   $args
 * @return     string
 */
function loop_pagination( $args = array() ) {

	_deprecated_function( __FUNCTION__, '3.0.0', 'the_posts_pagination()' );

	return isset( $args['echo'] ) && false === $args['echo'] ? get_the_posts_pagination( $args ) : the_posts_pagination( $args );
}

/**
 * Outputs the loop title.
 *
 * @since      2.0.0
 * @deprecated 3.0.0
 * @access     public
 * @return     void
 */
function hybrid_loop_title() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'the_archive_title()' );

	the_archive_title();
}

/**
 * Gets the loop title.  This function should only be used on archive-type pages, such as archive, blog, and
 * search results pages.  It outputs the title of the page.
 *
 * @link       http://core.trac.wordpress.org/ticket/21995
 * @since      2.0.0
 * @deprecated 3.0.0
 * @access     public
 * @return     string
 */
function hybrid_get_loop_title() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'get_the_archive_title()' );

	return get_the_archive_title();
}

/**
 * Outputs the loop description.
 *
 * @since      2.0.0
 * @deprecated 3.0.0
 * @access     public
 * @return     void
 */
function hybrid_loop_description() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'the_archive_description()' );

	the_archive_description();
}

/**
 * Gets the loop description.  This function should only be used on archive-type pages, such as archive, blog, and
 * search results pages.  It outputs the description of the page.
 *
 * @link       http://core.trac.wordpress.org/ticket/21995
 * @since      2.0.0
 * @deprecated 3.0.0
 * @access     public
 * @return     string
 */
function hybrid_get_loop_description() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'get_the_archive_description()' );

	return get_the_archive_description();
}

/**
 * Registers admin scripts.
 *
 * @note   Temp. deprecated. We might need in future.
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_admin_register_scripts() {}

/* === Removed Functions === */

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
