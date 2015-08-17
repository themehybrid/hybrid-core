<?php
/**
 * Functions for outputting common site data in the `<head>` area of a site.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Adds common theme items to <head>.
add_action( 'wp_head', 'hybrid_meta_charset',   0 );
add_action( 'wp_head', 'hybrid_meta_viewport',  1 );
add_action( 'wp_head', 'hybrid_meta_generator', 1 );
add_action( 'wp_head', 'hybrid_link_pingback',  3 );

# Filter the WordPress title.
add_filter( 'wp_title', 'hybrid_wp_title', 0 );

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
 * Filters the `wp_title` output early. Note that since WordPress 4.1.0 introduced the `_wp_render_title_tag()`
 * function, theme authors can no longer control this on their own. In the past, Hybrid Core defaulted to
 * a colon, so we're overwriting this regardless of what it was defined as. Later filters on `wp_title` can
 * change if needed.  Since core is now defining the separator, this shouldn't be an issue.
 *
 * @since  2.0.0
 * @access publc
 * @param  string  $title
 * @return string
 */
function hybrid_wp_title( $doctitle ) {

	// Custom separator for backwards compatibility.
	$separator = ':';

	if ( is_front_page() )
		$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

	elseif ( is_home() || is_singular() )
		$doctitle = single_post_title( '', false );

	elseif ( is_category() )
		$doctitle = single_cat_title( '', false );

	elseif ( is_tag() )
		$doctitle = single_tag_title( '', false );

	elseif ( is_tax() )
		$doctitle = single_term_title( '', false );

	elseif ( is_post_type_archive() )
		$doctitle = post_type_archive_title( '', false );

	elseif ( is_author() )
		$doctitle = hybrid_get_single_author_title();

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$doctitle = hybrid_get_single_minute_hour_title();

	elseif ( get_query_var( 'minute' ) )
		$doctitle = hybrid_get_single_minute_title();

	elseif ( get_query_var( 'hour' ) )
		$doctitle = hybrid_get_single_hour_title();

	elseif ( is_day() )
		$doctitle = hybrid_get_single_day_title();

	elseif ( get_query_var( 'w' ) )
		$doctitle = hybrid_get_single_week_title();

	elseif ( is_month() )
		$doctitle = single_month_title( ' ', false );

	elseif ( is_year() )
		$doctitle = hybrid_get_single_year_title();

	elseif ( is_archive() )
		$doctitle = hybrid_get_single_archive_title();

	elseif ( is_search() )
		$doctitle = hybrid_get_search_title();

	elseif ( is_404() )
		$doctitle = hybrid_get_404_title();

	// If the current page is a paged page.
	if ( ( ( $page = get_query_var( 'paged' ) ) || ( $page = get_query_var( 'page' ) ) ) && $page > 1 )
		// Translators: 1 is the page title. 2 is the page number.
		$doctitle = sprintf( __( '%1$s Page %2$s', 'hybrid-core' ), $doctitle . $separator, number_format_i18n( absint( $page ) ) );

	// Trim separator + space from beginning and end.
	return trim( strip_tags( $doctitle ), "{$separator} " );
}
