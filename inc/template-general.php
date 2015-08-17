<?php
/**
 * General template functions.  These functions are for use throughout the theme's various template files.
 * Their main purpose is to handle many of the template tags that are currently lacking in core WordPress.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
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
	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>', esc_url( home_url() ), get_bloginfo( 'name' ) );
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
	return sprintf( '<a class="wp-link" href="%s">%s</a>', esc_url( __( 'http://wordpress.org', 'hybrid-core' ) ), esc_html__( 'WordPress', 'hybrid-core' ) );
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
	$theme   = wp_get_theme( get_template() );
	$allowed = array( 'abbr' => array( 'title' => true ), 'acronym' => array( 'title' => true ), 'code' => true, 'em' => true, 'strong' => true );

	// Note: URI is escaped via `WP_Theme::markup_header()`.
	return sprintf( '<a class="theme-link" href="%s">%s</a>', $theme->display( 'ThemeURI' ), wp_kses( $theme->display( 'Name' ), $allowed ) );
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

	if ( ! is_child_theme() )
		return '';

	$theme   = wp_get_theme();
	$allowed = array( 'abbr' => array( 'title' => true ), 'acronym' => array( 'title' => true ), 'code' => true, 'em' => true, 'strong' => true );

	// Note: URI is escaped via `WP_Theme::markup_header()`.
	return sprintf( '<a class="child-link" href="%s">%s</a>', $theme->display( 'ThemeURI' ), wp_kses( $theme->display( 'Name' ), $allowed ) );
}

/**
 * Gets the "blog" (posts page) page URL.  `home_url()` will not always work for this because it
 * returns the front page URL.  Sometimes the blog page URL is set to a different page.  This
 * function handles both scenarios.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_blog_url() {

	if ( 'posts' === get_option( 'show_on_front' ) )
		$blog_url = home_url();

	elseif ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) )
		$blog_url = get_permalink( $page_for_posts );

	return ! empty( $blog_url ) ? esc_url( $blog_url ) : '';
}

/**
 * Outputs the site title.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function hybrid_site_title() {
	echo hybrid_get_site_title();
}

/**
 * Returns the linked site title wrapped in an `<h1>` tag.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_title() {

	if ( $title = get_bloginfo( 'name' ) )
		$title = sprintf( '<h1 %s><a href="%s" rel="home">%s</a></h1>', hybrid_get_attr( 'site-title' ), esc_url( home_url() ), $title );

	return apply_filters( 'hybrid_site_title', $title );
}

/**
 * Outputs the site description.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function hybrid_site_description() {
	echo hybrid_get_site_description();
}

/**
 * Returns the site description wrapped in an `<h2>` tag.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_description() {

	if ( $desc = get_bloginfo( 'description' ) )
		$desc = sprintf( '<h2 %s>%s</h2>', hybrid_get_attr( 'site-description' ), $desc );

	return apply_filters( 'hybrid_site_description', $desc );
}

/**
 * Function for figuring out if we're viewing a "plural" page.  In WP, these pages are archives,
 * search results, and the home/blog posts index.  Note that this is similar to, but not quite
 * the same as `!is_singular()`, which wouldn't account for the 404 page.
 *
 * @since  3.0.0
 * @access public
 * @return bool
 */
function hybrid_is_plural() {
	return apply_filters( 'hybrid_is_plural', is_home() || is_archive() || is_search() );
}

/**
 * Print the general archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_archive_title() {
	echo hybrid_get_single_archive_title();
}

/**
 * Retrieve the general archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_archive_title() {
	return esc_html__( 'Archives', 'hybrid-core' );
}

/**
 * Print the author archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_author_title() {
	echo hybrid_get_single_author_title();
}

/**
 * Retrieve the author archive title.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_get_single_author_title() {
	return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
}

/**
 * Print the year archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_year_title() {
	echo hybrid_get_single_year_title();
}

/**
 * Retrieve the year archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_year_title() {
	return get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) );
}

/**
 * Print the week archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_week_title() {
	echo hybrid_get_single_week_title();
}

/**
 * Retrieve the week archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_week_title() {

	// Translators: 1 is the week number and 2 is the year.
	return sprintf( esc_html__( 'Week %1$s of %2$s', 'hybrid-core' ), get_the_time( esc_html_x( 'W', 'weekly archives date format', 'hybrid-core' ) ), get_the_time( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) ) );
}

/**
 * Print the day archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_day_title() {
	echo hybrid_get_single_day_title();
}

/**
 * Retrieve the day archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_day_title() {
	return get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'hybrid-core' ) );
}

/**
 * Print the hour archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_hour_title() {
	echo hybrid_get_single_hour_title();
}

/**
 * Retrieve the hour archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_hour_title() {
	return get_the_time( esc_html_x( 'g a', 'hour archives time format', 'hybrid-core' ) );
}

/**
 * Print the minute archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_minute_title() {
	echo hybrid_get_single_minute_title();
}

/**
 * Retrieve the minute archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_minute_title() {

	// Translators: Minute archive title. %s is the minute time format.
	return sprintf( esc_html__( 'Minute %s', 'hybrid-core' ), get_the_time( esc_html_x( 'i', 'minute archives time format', 'hybrid-core' ) ) );
}

/**
 * Print the minute + hour archive title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_single_minute_hour_title() {
	echo hybrid_get_single_minute_hour_title();
}

/**
 * Retrieve the minute + hour archive title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_single_minute_hour_title() {
	return get_the_time( esc_html_x( 'g:i a', 'minute and hour archives time format', 'hybrid-core' ) );
}

/**
 * Print the search results title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_search_title() {
	echo hybrid_get_search_title();
}

/**
 * Retrieve the search results title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_search_title() {

	// Translators: %s is the search query. The HTML entities are opening and closing curly quotes.
	return sprintf( esc_html__( 'Search results for &#8220;%s&#8221;', 'hybrid-core' ), get_search_query() );
}

/**
 * Retrieve the 404 page title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_404_title() {
	echo hybrid_get_404_title();
}

/**
 * Retrieve the 404 page title.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_404_title() {
	return esc_html__( '404 Not Found', 'hybrid-core' );
}
