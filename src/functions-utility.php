<?php
/**
 * Utility functions.
 *
 * Additional helper functions that the framework or themes may use.  The
 * functions in this file are functions that don't really have a home within any
 * other parts of the framework.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Autoloader for the framework. Looks in the framework folder for classes. File
 * names are prefixed with `class-` and are a lowercased version of the class
 * name. Classes are broken up by uppercase letter.
 *
 * `ABC\MyClass`       = `/app/class-my-class.php`
 * `ABC\Admin\MyClass` = `/app/admin/class-my-class.php`
 *
 * @since  5.0.0
 * @access public
 * @param  string  $class
 * @return void
 */
function autoload( $class, $args = [] ) {

	$args = $args + [
		'namespace' => __NAMESPACE__,
		'path'      => HYBRID_DIR
	];

	$args['namespace'] = trim( $args['namespace'], '\\' ) . '\\';

	// Bail if the class is not in our namespace.
	if ( 0 !== strpos( $class, $args['namespace'] ) ) {
		return;
	}

	$file       = '';
	$new_pieces = [];
	$prefix     = 'class';

	// Remove the namespace.
	$class = str_replace( $args['namespace'], '', $class );

	// Explode the full class name into an array of items by sub-namespace
	// and class name.
	$pieces = explode( '\\', $class );

	foreach ( $pieces as $piece ) {

		// Split pieces by uppercase letter.  Assume sub-namespaces and
		// classes are in "PascalCase".
		$pascal = preg_split( '/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/', $piece,  -1, PREG_SPLIT_NO_EMPTY );

		// Lowercase and hyphenate the word pieces within a string.
		$new_pieces[] = strtolower( join( '-', $pascal ) );
	}

	// If a contract/interface or trait, change the filename prefix.
	if ( !! array_intersect( [ 'contract', 'contracts', 'interface', 'interfaces' ], $new_pieces ) ) {
		$prefix = 'interface';

	} elseif ( !! array_intersect( [ 'trait', 'traits' ], $new_pieces ) ) {
		$prefix = 'trait';
	}

	// Pop the last item off the array and re-add it with the `class-` prefix
	// and the `.php` file extension.  This is our class file.
	$new_pieces[] = sprintf( '%s-%s.php', $prefix, array_pop( $new_pieces ) );

	// Join all the pieces together by a forward slash. These are directories.
	$file = join( DIRECTORY_SEPARATOR, $new_pieces );

	// Append the file name to the framework directory.
	$file = trailingslashit( $args['path'] ) . $file;

	// Include the file only if it exists.
	if ( file_exists( $file ) ) {
		include( $file );
	}
}

/**
 * Replaces `%1$s` and `%2$s` with the template and stylesheet directory paths.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $value
 * @return string
 */
function sprintf_theme_dir( $value ) {

	return sprintf( $value, get_template_directory(), get_stylesheet_directory() );
}

/**
 * Replaces `%1$s` and `%2$s` with the template and stylesheet directory URIs.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $value
 * @return string
 */
function sprintf_theme_uri( $value ) {

	return sprintf( $value, get_template_directory_uri(), get_stylesheet_directory_uri() );
}

/**
 * Converts a hex color to RGB.  Returns the RGB values as an array.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $hex
 * @return array
 */
function hex_to_rgb( $hex ) {

	// Remove "#" if it was added.
	$color = trim( $hex, '#' );

	// If the color is three characters, convert it to six.
        if ( 3 === strlen( $color ) ) {
		$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
	}

	// Get the red, green, and blue values.
	$red   = hexdec( $color[0] . $color[1] );
	$green = hexdec( $color[2] . $color[3] );
	$blue  = hexdec( $color[4] . $color[5] );

	// Return the RGB colors as an array.
	return [ 'r' => $red, 'g' => $green, 'b' => $blue ];
}

/**
 * Conditional check to determine if we are in script debug mode.  This is
 * generally used to decide whether to load development versions of scripts/styles.
 *
 * @since  5.0.0
 * @access public
 * @return bool
 */
function is_script_debug() {

	return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
}

/**
 * Helper function for replacing a class in an HTML string. This function only
 * replaces the first class attribute it comes upon and stops.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $class
 * @param  string  $html
 * @return string
 */
function replace_html_class( $class, $html ) {

	return preg_replace(
		"/class=(['\"]).+?(['\"])/i",
		'class=$1' . esc_attr( $class ) . '$2',
		$html,
		1
	);
}

/**
 * Checks if a widget exists.  Pass in the widget class name.  This function is
 * useful for checking if the widget exists before directly calling `the_widget()`
 * within a template.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $widget
 * @return bool
 */
function widget_exists( $widget ) {

	return isset( $GLOBALS['wp_widget_factory']->widgets[ $widget ] );
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
function blog_url() {

	$blog_url = '';

	if ( 'posts' === get_option( 'show_on_front' ) ) {
		$blog_url = home_url();

	} elseif ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) ) {
		$blog_url = get_permalink( $page_for_posts );
	}

	return $blog_url ?: '';
}

/**
 * Function for figuring out if we're viewing a "plural" page.  In WP, these
 * pages are archives, search results, and the home/blog posts index.  Note that
 * this is similar to, but not quite the same as `! is_singular()`, which
 * wouldn't account for the 404 page.
 *
 * @since  5.0.0
 * @access public
 * @return bool
 */
function is_plural() {

	return is_home() || is_archive() || is_search();
}

/**
 * Retrieve the general archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function archive_title() {

	return esc_html__( 'Archives', 'hybrid-core' );
}

/**
 * Retrieve the author archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function author_title() {

	return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
}

/**
 * Retrieve the year archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function year_title() {

	return get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'hybrid-core' ) );
}

/**
 * Retrieve the week archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function week_title() {

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
function day_title() {

	return get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'hybrid-core' ) );
}

/**
 * Retrieve the hour archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function hour_title() {

	return get_the_time( esc_html_x( 'g a', 'hour archives time format', 'hybrid-core' ) );
}

/**
 * Retrieve the minute archive title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function minute_title() {

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
function minute_hour_title() {

	return get_the_time( esc_html_x( 'g:i a', 'minute and hour archives time format', 'hybrid-core' ) );
}

/**
 * Retrieve the search results title.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function search_title() {

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
function error_title() {

	return esc_html__( '404 Not Found', 'hybrid-core' );
}
