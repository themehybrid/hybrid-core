<?php
/**
 * Title class.
 *
 * This is a static class for quickly grabbing the current page title, no
 * matter which page we're on.  It covers some additional archive use cases that
 * are not covered in core WP.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Util;

/**
 * Title static class.
 *
 * @since  5.0.0
 * @access public
 */
class Title {

	/**
	 * Retrieve the current page title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function current() {

		$title = '';

		if ( is_front_page() ) {
			$title = static::frontPage();

		} elseif ( is_home() || is_singular() ) {
			$title = static::post();

		} elseif ( is_archive() ) {
			$title = static::archive();

		} elseif ( is_search() ) {
			$title = static::search();

		} elseif ( is_404() ) {
			$title = static::error();
		}

		return $title;
	}

	/**
	 * Retrieve the general archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function archive() {

		$title = '';

		if ( is_category() || is_tag() || is_tax() ) {
			$title = static::term();

		} elseif ( is_post_type_archive() ) {
			$title = static::postTypeArchive();

		} elseif ( is_author() ) {
			$title = static::author();

		} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
			$title = static::minuteHour();

		} elseif ( get_query_var( 'minute' ) ) {
			$title = static::minute();

		} elseif ( get_query_var( 'hour' ) ) {
			$title = static::hour();

		} elseif ( is_day() ) {
			$title = static::day();

		} elseif ( get_query_var( 'w' ) ) {
			$title = static::week();

		} elseif ( is_month() ) {
			$title = static::month();

		} elseif ( is_year() ) {
			$title = static::year();
		} else {
			$title = esc_html__( 'Archives', 'hybrid-core' );
		}

		return $title;
	}

	/**
	 * Retrieve the front page title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function frontPage() {

		return get_bloginfo( 'name', 'display' );
	}

	/**
	 * Retrieve the single post title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function post() {

		return single_post_title( '', false );
	}

	/**
	 * Retrieve the home/posts-page title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function home() {

		return get_post_field( 'post_title', get_queried_object_id() );
	}

	/**
	 * Retrieve the search results title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function search() {

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
	 * @return string
	 */
	public static function error() {

		return esc_html__( '404 Not Found', 'hybrid-core' );
	}

	/**
	 * Retrieve the term archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function term() {

		return single_term_title( '', false );
	}

	/**
	 * Retrieve the post type archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function postTypeArchive() {

		return post_type_archive_title( '', false );
	}

	/**
	 * Retrieve the month archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function month() {

		return single_month_title( ' ', false );
	}

	/**
	 * Retrieve the author archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function author() {

		return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
	}

	/**
	 * Retrieve the year archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function year() {

		return get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) );
	}

	/**
	 * Retrieve the week archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function week() {

		return sprintf(
			// Translators: 1 is the week number and 2 is the year.
			esc_html__( 'Week %1$s of %2$s', 'hybrid-core' ),
			get_the_time( esc_html_x( 'W', 'weekly archives date format', 'hybrid-core' ) ),
			get_the_time( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) )
		);
	}

	/**
	 * Retrieve the day archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function day() {

		return get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'hybrid-core' ) );
	}

	/**
	 * Retrieve the hour archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function hour() {

		return get_the_time( esc_html_x( 'g a', 'hour archives time format', 'hybrid-core' ) );
	}

	/**
	 * Retrieve the minute archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function minute() {

		return sprintf(
			// Translators: Minute archive title. %s is the minute time format.
			esc_html__( 'Minute %s', 'hybrid-core' ),
			get_the_time( esc_html_x( 'i', 'minute archives time format', 'hybrid-core' ) )
		);
	}

	/**
	 * Retrieve the minute + hour archive title.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public static function minuteHour() {

		return get_the_time( esc_html_x( 'g:i a', 'minute and hour archives time format', 'hybrid-core' ) );
	}
}
