<?php
/**
 * General template tags.
 *
 * General template functions.  These functions are for use throughout the
 * theme's various template files. Their main purpose is to handle many of the
 * template tags that are currently lacking in core WordPress.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Gets the "blog" (posts page) page URL.  `home_url()` will not always work for
 * this because it returns the front page URL.  Sometimes the blog page URL is
 * set to a different page.  This function handles both scenarios.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_blog_url() {

	$blog_url = '';

	if ( 'posts' === get_option( 'show_on_front' ) ) {
		$blog_url = home_url();

	} elseif ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) ) {
		$blog_url = get_permalink( $page_for_posts );
	}

	return $blog_url ? esc_url( $blog_url ) : '';
}

/**
 * Function for figuring out if we're viewing a "plural" page.  In WP, these
 * pages are archives, search results, and the home/blog posts index.  Note that
 * this is similar to, but not quite the same as `!is_singular()`, which wouldn't
 * account for the 404 page.
 *
 * @since  5.0.0
 * @access public
 * @return bool
 */
function is_plural() {

	return apply_filters( 'hybrid/is_plural', is_home() || is_archive() || is_search() );
}

/**
 * Print the general archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_archive_title() {

	echo get_single_archive_title();
}

/**
 * Retrieve the general archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_archive_title() {

	return esc_html__( 'Archives', 'hybrid-core' );
}

/**
 * Print the author archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_author_title() {

	echo get_single_author_title();
}

/**
 * Retrieve the author archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function get_single_author_title() {

	return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
}

/**
 * Print the year archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_year_title() {

	echo get_single_year_title();
}

/**
 * Retrieve the year archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_year_title() {

	return get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) );
}

/**
 * Print the week archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_week_title() {

	echo get_single_week_title();
}

/**
 * Retrieve the week archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_week_title() {

	return sprintf(
		// Translators: 1 is the week number and 2 is the year.
		esc_html__( 'Week %1$s of %2$s', 'hybrid-core' ),
		get_the_time( esc_html_x( 'W', 'weekly archives date format', 'hybrid-core' ) ),
		get_the_time( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) )
	);
}

/**
 * Print the day archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_day_title() {

	echo get_single_day_title();
}

/**
 * Retrieve the day archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_day_title() {

	return get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'hybrid-core' ) );
}

/**
 * Print the hour archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_hour_title() {

	echo get_single_hour_title();
}

/**
 * Retrieve the hour archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_hour_title() {

	return get_the_time( esc_html_x( 'g a', 'hour archives time format', 'hybrid-core' ) );
}

/**
 * Print the minute archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_minute_title() {

	echo get_single_minute_title();
}

/**
 * Retrieve the minute archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_minute_title() {

	return sprintf(
		// Translators: Minute archive title. %s is the minute time format.
		esc_html__( 'Minute %s', 'hybrid-core' ),
		get_the_time( esc_html_x( 'i', 'minute archives time format', 'hybrid-core' ) )
	);
}

/**
 * Print the minute + hour archive title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function single_minute_hour_title() {

	echo get_single_minute_hour_title();
}

/**
 * Retrieve the minute + hour archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_single_minute_hour_title() {

	return get_the_time( esc_html_x( 'g:i a', 'minute and hour archives time format', 'hybrid-core' ) );
}

/**
 * Print the search results title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function search_title() {

	echo get_search_title();
}

/**
 * Retrieve the search results title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_search_title() {

	return sprintf(
		// Translators: %s is the search query.
		esc_html__( 'Search results for: %s', 'hybrid-core' ),
		get_search_query()
	);
}

/**
 * Retrieve the 404 page title.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function error_title() {

	echo get_error_title();
}

/**
 * Retrieve the 404 page title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_error_title() {

	return esc_html__( '404 Not Found', 'hybrid-core' );
}
