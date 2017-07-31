<?php
/**
 * Functions for outputting common site data in the `<head>` area of a site.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Adds common theme items to <head>.
add_action( 'wp_head', 'hybrid_meta_charset',   0 );
add_action( 'wp_head', 'hybrid_meta_viewport',  1 );
add_action( 'wp_head', 'hybrid_meta_generator', 1 );
add_action( 'wp_head', 'hybrid_link_pingback',  3 );

# Filter the WordPress title.
add_filter( 'document_title_parts', 'hybrid_document_title_parts', 5 );

/**
 * Adds the meta charset to the header.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_meta_charset() {

	printf( '<meta charset="%s" />' . "\n", esc_attr( get_bloginfo( 'charset' ) ) );
}

/**
 * Adds the meta viewport to the header.
 *
 * @since  2.0.0
 * @access public
 */
function hybrid_meta_viewport() {

	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Adds the theme generator meta tag.  This is particularly useful for checking theme users' version
 * when handling support requests.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_meta_generator() {
	$theme     = wp_get_theme( get_template() );
	$generator = sprintf( '<meta name="generator" content="%s %s" />' . "\n", esc_attr( $theme->get( 'Name' ) ), esc_attr( $theme->get( 'Version' ) ) );

	echo apply_filters( 'hybrid_meta_generator', $generator );
}

/**
 * Adds the pingback link to the header.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_link_pingback() {

	if ( 'open' === get_option( 'default_ping_status' ) )
		printf( '<link rel="pingback" href="%s" />' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
}

/**
 * Replacement for the older filter on `wp_title` since WP has moved to a new document
 * title system.  This new filter merely alters the `title` key based on the current
 * page being viewed.  It also makes sure that all tags are stripped, which WP doesn't
 * do by default (it escapes HTML).
 *
 * @since  4.0.0
 * @access public
 * @param  array   $doctitle
 * @return array
 */
function hybrid_document_title_parts( $doctitle ) {

	if ( is_front_page() )
		$doctitle['title'] = get_bloginfo( 'name', 'display' );

	elseif ( is_home() || is_singular() )
		$doctitle['title'] = single_post_title( '', false );

	elseif ( is_category() || is_tag() || is_tax() )
		$doctitle['title'] = single_term_title( '', false );

	elseif ( is_post_type_archive() )
		$doctitle['title'] = post_type_archive_title( '', false );

	elseif ( is_author() )
		$doctitle['title'] = hybrid_get_single_author_title();

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$doctitle['title'] = hybrid_get_single_minute_hour_title();

	elseif ( get_query_var( 'minute' ) )
		$doctitle['title'] = hybrid_get_single_minute_title();

	elseif ( get_query_var( 'hour' ) )
		$doctitle['title'] = hybrid_get_single_hour_title();

	elseif ( is_day() )
		$doctitle['title'] = hybrid_get_single_day_title();

	elseif ( get_query_var( 'w' ) )
		$doctitle['title'] = hybrid_get_single_week_title();

	elseif ( is_month() )
		$doctitle['title'] = single_month_title( '', false );

	elseif ( is_year() )
		$doctitle['title'] = hybrid_get_single_year_title();

	elseif ( is_archive() )
		$doctitle['title'] = hybrid_get_single_archive_title();

	elseif ( is_search() )
		$doctitle['title'] = hybrid_get_search_title();

	elseif ( is_404() )
		$doctitle['title'] = hybrid_get_404_title();

	// Return the title and make sure to strip tags.
	return array_map( 'strip_tags', $doctitle );
}
