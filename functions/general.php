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

/**
 * Outputs the loop title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_loop_title() {
	echo hybrid_get_loop_title();
}

/**
 * Gets the loop title.  This function should only be used on archive-type pages, such as archive, blog, and 
 * search results pages.  It outputs the title of the page.
 *
 * @link   http://core.trac.wordpress.org/ticket/21995
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_loop_title() {

	$loop_title = '';

	if ( is_home() && !is_front_page() )
		$loop_title = get_post_field( 'post_title', get_queried_object_id() );

	elseif ( is_category() ) 
		$loop_title = single_cat_title( '', false );

	elseif ( is_tag() )
		$loop_title = single_tag_title( '', false );

	elseif ( is_tax() )
		$loop_title = single_term_title( '', false );

	elseif ( is_author() )
		$loop_title = get_the_author();

	elseif ( is_search() )
		$loop_title = get_search_query();

	elseif ( is_post_type_archive() )
		$loop_title = post_type_archive_title( '', false );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$loop_title = get_the_time( _x( 'g:i a', 'minute and hour archives time format', 'hybrid-core' ) );

	elseif ( get_query_var( 'minute' ) )
		$loop_title = sprintf( 'Minute %s', get_the_time( _x( 'i', 'minute archives time format', 'hybrid-core' ) ) );

	elseif ( get_query_var( 'hour' ) )
		$loop_title = get_the_time( _x( 'g a', 'hour archives time format', 'hybrid-core' ) );

	elseif ( is_day() )
		$loop_title = get_the_date();

	elseif ( get_query_var( 'w' ) )
		/* Translators: 1 is the week number and 2 is the year. */
		$loop_title = sprintf( __( 'Week %1$s of %2$s', 'hybrid-core' ), get_the_time( _x( 'W', 'weekly archives date format', 'hybrid-core' ) ), get_the_time( _x( 'Y', 'yearly archives date format', 'hybrid-core' ) ) );

	elseif ( is_month() )
		$loop_title = single_month_title( ' ', false );

	elseif ( is_year() )
		$loop_title = get_the_date( _x( 'Y', 'yearly archives date format', 'hybrid-core' ) );

	elseif ( is_archive() )
		$loop_title = __( 'Archives', 'hybrid-core' );

	return apply_filters( 'hybrid_loop_title', $loop_title );
}

/**
 * Outputs the loop description.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_loop_description() {
	echo hybrid_get_loop_description();
}

/**
 * Gets the loop description.  This function should only be used on archive-type pages, such as archive, blog, and 
 * search results pages.  It outputs the description of the page.
 *
 * @link   http://core.trac.wordpress.org/ticket/21995
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_loop_description() {

	$loop_desc = '';

	if ( is_home() && !is_front_page() )
		$loop_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	elseif ( is_category() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	elseif ( is_tag() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	elseif ( is_tax() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	elseif ( is_author() )
		$loop_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	elseif ( is_search() )
		$loop_desc = sprintf( __( 'You are browsing the search results for &#8220;%s&#8221;', 'hybrid-core' ), get_search_query() );

	elseif ( is_post_type_archive() )
		$loop_desc = get_post_type_object( get_query_var( 'post_type' ) )->description;

	elseif ( is_time() )
		$loop_desc = __( 'You are browsing the site archives by time.', 'hybrid-core' );

	elseif ( is_day() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'hybrid-core' ), get_the_date() );

	elseif ( is_month() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'hybrid-core' ), single_month_title( ' ', false ) );

	elseif ( is_year() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'hybrid-core' ), get_the_time( _x( 'Y', 'yearly archives date format', 'hybrid-core' ) ) );

	elseif ( is_date() )
		$loop_desc = __( 'You are browsing the site archives by date.', 'hybrid-core' );

	elseif ( is_archive() )
		$loop_desc = __( 'You are browsing the site archives.', 'hybrid-core' );

	return apply_filters( 'hybrid_loop_description', $loop_desc );
}
