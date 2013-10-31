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
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * @since 1.5.0
 * @deprecated 1.6.0
 */
function post_format_tools_post_has_content( $id = 0 ) {
	_deprecated_function( __FUNCTION__, '1.6.0', 'hybrid_post_has_content()' );
	hybrid_post_has_content( $id );
}

/**
 * @since 1.5.0
 * @deprecated 1.6.0
 */
function post_format_tools_url_grabber() {
	_deprecated_function( __FUNCTION__, '1.6.0', 'hybrid_get_the_post_format_url()' );
	hybrid_get_the_post_format_url();
}

/**
 * @since 1.5.0
 * @deprecated 1.6.0
 */
function post_format_tools_get_image_attachment_count() {
	_deprecated_function( __FUNCTION__, '1.6.0', 'hybrid_get_gallery_image_count()' );
	hybrid_get_gallery_image_count();
}

/**
 * @since 1.5.0
 * @deprecated 1.6.0
 */
function post_format_tools_get_video( $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '1.6.0', 'hybrid_media_grabber()' );
	hybrid_media_grabber();
}

/**
 * @since 0.8.0
 * @deprecated 1.6.0
 */
function get_atomic_template( $template ) {
	_deprecated_function( __FUNCTION__, '1.6.0', '' );

	$templates = array();

	$theme_dir = trailingslashit( THEME_DIR ) . $template;
	$child_dir = trailingslashit( CHILD_THEME_DIR ) . $template;

	if ( is_dir( $child_dir ) || is_dir( $theme_dir ) ) {
		$dir = true;
		$templates[] = "{$template}/index.php";
	}
	else {
		$dir = false;
		$templates[] = "{$template}.php";
	}

	foreach ( hybrid_get_context() as $context )
		$templates[] = ( ( $dir ) ? "{$template}/{$context}.php" : "{$template}-{$context}.php" );

	return locate_template( array_reverse( $templates ), true, false );
}

/**
 * Dynamic element to wrap the site title in.  If it is the front page, wrap it in an <h1> element.  One other 
 * pages, wrap it in a <div> element. 
 *
 * @since      0.1.0
 * @deprecated 2.0.0
 * @access     public
 * @return     void
 */
function hybrid_site_title() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	/* If viewing the front page of the site, use an <h1> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h1' : 'div';

	/* Get the site title.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $title = get_bloginfo( 'name' ) )
		$title = sprintf( '<%1$s id="site-title"><a href="%2$s" title="%3$s" rel="home"><span>%4$s</span></a></%1$s>', tag_escape( $tag ), home_url(), esc_attr( $title ), $title );

	/* Display the site title and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the front page, wrap it in an <h2> element.  
 * On other pages, wrap it in a <div> element.
 *
 * @since 0.1.0
 * @access public
 * @return void
 */
function hybrid_site_description() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	/* If viewing the front page of the site, use an <h2> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h2' : 'div';

	/* Get the site description.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $desc = get_bloginfo( 'description' ) )
		$desc = sprintf( '<%1$s id="site-description"><span>%2$s</span></%1$s>', tag_escape( $tag ), $desc );

	/* Display the site description and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_description', $desc );
}

/* === Removed Functions (note that functions removed prior to the 1.5 branch are gone). === */

/* Functions removed in the 1.5 branch. */

function hybrid_get_theme_data() {
	hybrid_function_removed( __FUNCTION__ );
}

/* Functions removed in the 1.6 branch. */

function post_format_tools_single_term_title() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_aside_infinity() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_quote_content() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_link_content() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_chat_content() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_chat_row_id() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_get_plural_string() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_get_plural_strings() {
	hybrid_function_removed( __FUNCTION__ );
}

function post_format_tools_clean_post_format_slug() {
	hybrid_function_removed( __FUNCTION__ );
}

/* Functions removed in the 2.0 branch. */

function hybrid_after_single() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_page() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_comment_author() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_theme_settings() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_doctype() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_meta_content_type() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_head_pingback() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_profile_uri() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_html() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_html() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_head() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_header() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_header() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_header() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_primary_menu() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_primary_menu() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_container() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_content() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_content() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_entry() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_entry() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_singular() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_primary() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_primary() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_secondary() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_secondary() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_subsidiary() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_subsidiary() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_container() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_footer() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_footer() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_footer() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_comment() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_comment() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_before_comment_list() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_after_comment_list() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_back_compat_update_settings() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_enqueue_script() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_admin_enqueue_style() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_enqueue_style() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_enqueue_script() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_admin_init() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_contextual_help() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_load_textdomain() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_debug_stylesheet() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_help() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_init() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_capability() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_get_settings_page_name() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_add_meta_boxes() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_load_settings_page_meta_boxes() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_save_theme_settings() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_enqueue_styles() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_enqueue_scripts() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_settings_page_load_scripts() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_register_sidebars() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_get_sidebars() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_register_menus() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_extra_theme_headers() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_single_post_format_title() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_get_plural_post_format_string() {
	hybrid_function_removed( __FUNCTION__ );
}

function hybrid_get_plural_post_format_strings() {
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