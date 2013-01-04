<?php
/**
 * Deprecated functions that should be avoided in favor of newer functions. Also handles removed 
 * functions to avoid errors. Developers should not use these functions in their parent themes and users 
 * should not use these functions in their child themes.  The functions below will all be removed at some 
 * point in a future release.  If your theme is using one of these, you should use the listed alternative or 
 * remove it from your theme if necessary.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2012, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * @since 0.2.0
 * @deprecated 0.7.0
 */
function hybrid_after_single() {
	_deprecated_function( __FUNCTION__, '0.7', "do_atomic( 'after_singular' )" );
	hybrid_after_singular();
}

/**
 * @since 0.2.0
 * @deprecated 0.7.0
 */
function hybrid_after_page() {
	_deprecated_function( __FUNCTION__, '0.7', "do_atomic( 'after_singular' )" );
	hybrid_after_singular();
}

/**
 * @since 0.2.2
 * @deprecated 0.8.0
 */
function hybrid_comment_author() {
	_deprecated_function( __FUNCTION__, '0.8', 'hybrid_comment_author_shortcode()' );
	return hybrid_comment_author_shortcode();
}

/**
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_theme_settings() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'hybrid_get_default_theme_settings()' );
	return apply_filters( hybrid_get_prefix() . '_settings_args', hybrid_get_default_theme_settings() );
}

/**
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_doctype() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	if ( !preg_match( "/MSIE 6.0/", esc_attr( $_SERVER['HTTP_USER_AGENT'] ) ) )
		$doctype = '<' . '?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . "\n";

	$doctype .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo apply_atomic( 'doctype', $doctype );
}

/**
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_meta_content_type() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	$content_type = '<meta http-equiv="Content-Type" content="' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . '" />' . "\n";
	echo apply_atomic( 'meta_content_type', $content_type );
}

/**
 * @since 0.4.0
 * @deprecated 1.0.0
 */
function hybrid_head_pingback() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	$pingback = '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
	echo apply_atomic( 'head_pingback', $pingback );
}

/**
 * @since 0.6.0
 * @deprecated 1.0.0
 */
function hybrid_profile_uri() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
	echo apply_atomic( 'profile_uri', 'http://gmpg.org/xfn/11' );
}

/**
 * @since 0.3.2
 * @deprecated 1.0.0
 */
function hybrid_before_html() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_html' )" );
	do_atomic( 'before_html' );
}

/**
 * @since 0.3.2
 * @deprecated 1.0.0
 */
function hybrid_after_html() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_html' )" );
	do_atomic( 'after_html' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_head() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'wp_head' );
	do_atomic( 'head' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_before_header() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_header' )" );
	do_atomic( 'before_header' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_header() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'header' )" );
	do_atomic( 'header' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_after_header() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_header' )" );
	do_atomic( 'after_header' );
}

/**
 * @since 0.8.0
 * @deprecated 1.0.0
 */
function hybrid_before_primary_menu() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_primary_menu' )" );
	do_atomic( 'before_primary_menu' );
}

/**
 * @since 0.8.0
 * @deprecated 1.0.0
 */
function hybrid_after_primary_menu() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_primary_menu' )" );
	do_atomic( 'after_primary_menu' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_before_container() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_container' )" );
	do_atomic( 'before_container' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_before_content() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_content' )" );
	do_atomic( 'before_content' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_after_content() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_content' )" );
	do_atomic( 'after_content' );
}

/**
 * @since 0.5.0
 * @deprecated 1.0.0
 */
function hybrid_before_entry() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_entry' )" );
	do_atomic( 'before_entry' );
}

/**
 * @since 0.5.0
 * @deprecated 1.0.0
 */
function hybrid_after_entry() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_entry' )" );
	do_atomic( 'after_entry' );
}

/**
 * @since 0.7.0
 * @deprecated 1.0.0
 */
function hybrid_after_singular() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_singular' )" );
	do_atomic( 'after_singular' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_before_primary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_primary' )" );
	do_atomic( 'before_primary' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_after_primary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_primary' )" );
	do_atomic( 'after_primary' );
}

/**
 * @since 0.2.0
 * @deprecated 1.0.0
 */
function hybrid_before_secondary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_secondary' )" );
	do_atomic( 'before_secondary' );
}

/**
 * @since 0.2.0
 * @deprecated 1.0.0
 */
function hybrid_after_secondary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_secondary' )" );
	do_atomic( 'after_secondary' );
}

/**
 * @since 0.3.1
 * @deprecated 1.0.0
 */
function hybrid_before_subsidiary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_subsidiary' )" );
	do_atomic( 'before_subsidiary' );
}

/**
 * @since 0.3.1
 * @deprecated 1.0.0
 */
function hybrid_after_subsidiary() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_subsidiary' )" );
	do_atomic( 'after_subsidiary' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_after_container() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_container' )" );
	do_atomic( 'after_container' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_before_footer() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_footer' )" );
	do_atomic( 'before_footer' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_footer() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'footer' )" );
	do_atomic( 'footer' );
}

/**
 * @since 0.1.0
 * @deprecated 1.0.0
 */
function hybrid_after_footer() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_footer' )" );
	do_atomic( 'after_footer' );
}

/**
 * @since 0.5.0
 * @deprecated 1.0.0
 */
function hybrid_before_comment() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_comment' )" );
	do_atomic( 'before_comment' );
}

/**
 * @since 0.5.0
 * @deprecated 1.0.0
 */
function hybrid_after_comment() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_comment' )" );
	do_atomic( 'after_comment' );
}

/**
 * @since 0.6.0
 * @deprecated 1.0.0
 */
function hybrid_before_comment_list() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'before_comment_list' )" );
	do_atomic( 'before_comment_list' );
}

/**
 * @since 0.6.0
 * @deprecated 1.0.0
 */
function hybrid_after_comment_list() {
	_deprecated_function( __FUNCTION__, '1.0.0', "do_atomic( 'after_comment_list' )" );
	do_atomic( 'after_comment_list' );
}

/* @deprecated 1.0.0. Backwards compatibility with old theme settings. */
add_action( 'check_admin_referer', 'hybrid_back_compat_update_settings' );

/**
 * Backwards compatibility function for updating child theme settings.  Do not use this function or the 
 * available hook in development.
 *
 * @since 1.0.0
 * @deprecated 1.0.0
 */
function hybrid_back_compat_update_settings( $action ) {
	//_deprecated_function( __FUNCTION__, '1.0.0' );

	$prefix = hybrid_get_prefix();

	if ( "{$prefix}_theme_settings-options" == $action )
		do_action( "{$prefix}_update_settings_page" );
}

/**
 * @since 0.1.0
 * @deprecated 1.2.0
 */
function hybrid_enqueue_script() {
	_deprecated_function( __FUNCTION__, '1.2.0', 'hybrid_enqueue_scripts' );
	return;
}

/**
 * @since 1.0.0
 * @deprecated 1.2.0
 */
function hybrid_admin_enqueue_style() {
	_deprecated_function( __FUNCTION__, '1.2.0', 'hybrid_admin_enqueue_styles' );
	return;
}

/**
 * @since 0.7.0
 * @deprecated 1.2.0
 */
function hybrid_settings_page_enqueue_style() {
	_deprecated_function( __FUNCTION__, '1.2.0', 'hybrid_settings_page_enqueue_styles' );
	return;
}

/**
 * @since 0.7.0
 * @deprecated 1.2.0
 */
function hybrid_settings_page_enqueue_script() {
	_deprecated_function( __FUNCTION__, '1.2.0', 'hybrid_settings_page_enqueue_scripts' );
	return;
}

/**
 * @since 0.7.0
 * @deprecated 1.3.0
 */
function hybrid_admin_init() {
	_deprecated_function( __FUNCTION__, '1.3.0', 'hybrid_admin_setup' );
	return;
}

/**
 * @since 1.2.0
 * @deprecated 1.3.0
 */
function hybrid_settings_page_contextual_help() {
	_deprecated_function( __FUNCTION__, '1.3.0', 'hybrid_settings_page_help' );
	return;
}

/**
 * @since 0.9.0
 * @deprecated 1.3.0
 */
function hybrid_load_textdomain( $mofile, $domain ) {
	_deprecated_function( __FUNCTION__, '1.3.0', 'hybrid_load_textdomain_mofile' );
	return hybrid_load_textdomain_mofile( $mofile, $domain );
}

/**
 * @since 0.9.0
 * @deprecated 1.5.0
 */
function hybrid_debug_stylesheet( $stylesheet_uri, $stylesheet_dir_uri ) {
	_deprecated_function( __FUNCTION__, '1.5.0', 'hybrid_min_stylesheet_uri' );
	return hybrid_min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri );
}

/* === Removed Functions === */

/* Functions removed in the 0.8 branch. */

function hybrid_content_wrapper() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_handle_attachment() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_widget_class() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_ping_list() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_ping_list() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_pings_callback() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_pings_end_callback() {
	hybrid_function_removed( __FUNCTION__ );
}

/* Functions removed in the 1.2 branch. */

function hybrid_get_comment_form() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_comment_form() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_comment_form() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_get_utility_after_single() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_get_utility_after_page() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_create_post_meta_box() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_meta_box_args() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_meta_box() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_meta_box_text() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_meta_box_select() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_meta_box_textarea() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_meta_box_radio() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_save_post_meta_box() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_create_settings_meta_boxes() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_footer_settings_meta_box() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_about_theme_meta_box() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_load_settings_page() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_page_nav() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_cat_nav() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_category_menu() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_search_form() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_class() {
	hybrid_function_removed( __FUNCTION__ );
}

function is_sidebar_active() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_enqueue_style() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_add_theme_support() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_post_stylesheets() {
	hybrid_function_removed( __FUNCTION__ );
}

/* Functions removed in the 1.5 branch. */

function hybrid_get_theme_data() {
	hybrid_function_removed( __FUNCTION__ );
}

/**
 * Message to display for removed functions.
 * @since 0.5.0
 */
function hybrid_function_removed( $func = '' ) {
	die( sprintf( __( '<code>%1$s</code> &mdash; This function has been removed or replaced by another function.', 'hybrid-core' ), $func ) );
}

?>