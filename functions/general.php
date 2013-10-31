<?php
/**
 * General template functions.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Outputs the link back to the site.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_site_link() {
	echo hybrid_get_site_link();
}

/**
 * Returns a link back to the site.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_link() {
	return '<a class="site-link" href="' . home_url() . '" rel="home">' . get_bloginfo( 'name' ) . '</a>';
}

/**
 * Displays a link to WordPress.org.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_wp_link() {
	echo hybrid_get_wp_link();
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_wp_link() {
	return '<a class="wp-link" href="http://wordpress.org" title="' . esc_attr__( 'State-of-the-art semantic personal publishing platform', 'hybrid-core' ) . '">' . __( 'WordPress', 'hybrid-core' ) . '</a>';
}

/**
 * Displays a link to the parent theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_theme_link() {
	echo hybrid_get_theme_link();
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_theme_link() {
	$theme = wp_get_theme( get_template() );

	return sprintf( '<a class="theme-link" href="%s">%s</a>', esc_url( $theme->get( 'ThemeURI' ) ), $theme->display( 'Name', false, true ) ); 
}

/**
 * Displays a link to the child theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_child_theme_link() {
	echo hybrid_get_child_theme_link();
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_child_theme_link() {

	if ( !is_child_theme() )
		return '';

	$theme = wp_get_theme();

	return sprintf( '<a class="theme-link" href="%s">%s</a>', esc_url( $theme->get( 'ThemeURI' ) ), $theme->display( 'Name', false, true ) ); 
}

function hybrid_comment_reply_link( $args = array() ) {
	echo hybrid_get_comment_reply_link( $args );
}

/**
 * Outputs the comment reply link.  Note that WP's `comment_reply_link()` doesn't work outside of 
 * `wp_list_comments()` without passing in the proper arguments (it isn't meant to).  This function is just a 
 * wrapper for `comment_reply_link()`, which adds in the arguments automatically.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_get_comment_reply_link( $attr = array() ) {

	if ( !get_option( 'thread_comments' ) || in_array( get_comment_type(), array( 'pingback', 'trackback' ) ) )
		return '';

	$defaults = array(
		'depth'     => intval( $GLOBALS['comment_depth'] ),
		'max_depth' => get_option( 'thread_comments_depth' ),
	);
	$attr = shortcode_atts( $defaults, $attr, 'comment-reply-link' );

	echo get_comment_reply_link( $attr );
}
