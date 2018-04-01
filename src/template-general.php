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

use Hybrid\Template\Pagination;

/**
 * Returns the template hierarchy from the theme wrapper.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_template_hierarchy() {

	return app()->get( 'template_hierarchy' )->hierarchy;
}

/**
 * Creates a hierarchy based on the current post.  For use with content-specific templates.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_post_hierarchy() {

	// Set up an empty array and get the post type.
	$hierarchy = array();
	$post_type = get_post_type();

	// If attachment, add attachment type templates.
	if ( 'attachment' === $post_type ) {

		$type    = get_attachment_type();
		$subtype = get_attachment_subtype();

		if ( $subtype ) {
			$hierarchy[] = "attachment-{$type}-{$subtype}";
			$hierarchy[] = "attachment-{$subtype}";
		}

		$hierarchy[] = "attachment-{$type}";
	}

	// If the post type supports 'post-formats', get the template based on the format.
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// Get the post format.
		$post_format = get_post_format() ? get_post_format() : 'standard';

		// Template based off post type and post format.
		$hierarchy[] = "{$post_type}-{$post_format}";

		// Template based off the post format.
		$hierarchy[] = $post_format;
	}

	// Template based off the post type.
	$hierarchy[] = $post_type;

	return $hierarchy;
}

/**
 * Outputs the site title.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @param  array   $attr
 * @return void
 */
function site_title( $args = [], $attr = [] ) {

	echo get_site_title( $args, $attr );
}

/**
 * Returns the site title.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @param  array   $attr
 * @return string
 */
function get_site_title( $args = [], $attr = [] ) {

	$html  = '';
	$title = get_bloginfo( 'name', 'display' );

	$args = wp_parse_args( $args, [ 'tag' => is_front_page() ? 'h1' : 'div' ] );
	$attr = wp_parse_args( $attr, [ 'class' => 'site-title' ] );

	if ( $title ) {
		$link = sprintf( '<a href="%s">%s</a>', esc_url( home_url() ), $title );

		$html = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			attributes( 'site-title', '', $attr )->fetch(),
			$link
		);
	}

	return apply_filters( 'hybrid/site_title', $html );
}

/**
 * Outputs the site description.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @param  array   $attr
 * @return void
 */
function site_description( $args = [], $attr = [] ) {

	echo get_site_description( $args, $attr );
}

/**
 * Returns the site title.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @param  array   $attr
 * @return string
 */
function get_site_description( $args = [], $attr = [] ) {

	$html = '';
	$desc = get_bloginfo( 'description', 'display' );

	$args = wp_parse_args( $args, [ 'tag' => 'div' ] );
	$attr = wp_parse_args( $attr, [ 'class' => 'site-description' ] );

	if ( $desc ) {
		$html = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			attributes( 'site-description', '', $attr )->fetch(),
			$desc
		);
	}

	return apply_filters( 'hybrid/site_description', $html );
}

/**
 * Outputs the link back to the site.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function site_link() {

	echo get_site_link();
}

/**
 * Returns a link back to the site.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_site_link() {

	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>', esc_url( home_url() ), get_bloginfo( 'name' ) );
}

/**
 * Displays a link to WordPress.org.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function wp_link() {

	echo get_wp_link();
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_wp_link() {

	return sprintf( '<a class="wp-link" href="%s">%s</a>', esc_url( __( 'https://wordpress.org', 'hybrid-core' ) ), esc_html__( 'WordPress', 'hybrid-core' ) );
}

/**
 * Displays a link to the parent theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function theme_link() {

	echo get_theme_link();
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_theme_link() {

	$theme = wp_get_theme( \get_template() );

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	// Note: URI is escaped via `WP_Theme::markup_header()`.
	return sprintf(
		'<a class="theme-link" href="%s">%s</a>',
		$theme->display( 'ThemeURI' ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);
}

/**
 * Displays a link to the child theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function child_theme_link() {

	echo get_child_theme_link();
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_child_theme_link() {

	if ( ! is_child_theme() ) {
		return '';
	}

	$theme = wp_get_theme();

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	// Note: URI is escaped via `WP_Theme::markup_header()`.
	return sprintf(
		'<a class="child-link" href="%s">%s</a>',
		$theme->display( 'ThemeURI' ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);
}

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

	return apply_filters( 'hybrid_is_plural', is_home() || is_archive() || is_search() );
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

/**
 * Returns a new `Pagination` object.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return object
 */
function pagination( $args = [] ) {

	return new Pagination( $args );
}

/**
 * Outputs the posts pagination.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function posts_pagination( $args = [] ) {

	echo pagination( $args )->fetch();
}

/**
 * Single post pagination. This is a replacement for `wp_link_pages()` using our
 * `Pagination` class.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @global int    $page
 * @global int    $numpages
 * @global bool   $multipage
 * @global bool   $more
 * @global object $wp_rewrite
 * @return void
 */
function singular_pagination( $args = [] ) {
	global $page, $numpages, $multipage, $more, $wp_rewrite;

	if ( ! $multipage ) {
		return;
	}

	$url_parts = explode( '?', html_entity_decode( get_permalink() ) );
	$base      = trailingslashit( $url_parts[0] ) . '%_%';

	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $base, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( '%#%' ) : '?page=%#%';

	$args = (array) $args + [
		'base'    => $base,
		'format'  => $format,
		'current' => ! $more && 1 === $page ? 0 : $page,
		'total'   => $numpages
	];

	echo pagination( $args )->fetch();
}
