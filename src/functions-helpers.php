<?php
/**
 * Helper functions.
 *
 * Helpers are functions designed for quickly accessing data from the container
 * that we need throughout the framework.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

use Hybrid\Proxies\App;
use Hybrid\Tools\Collection;

/**
 * The single instance of the app. Use this function for quickly working with
 * data.  Returns an instance of the `\Hybrid\Core\Application` class. If the
 * `$abstract` parameter is passed in, it'll resolve and return the value from
 * the container.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $abstract
 * @param  array   $params
 * @return mixed
 */
function app( $abstract = '', $params = [] ) {

	return App::resolve( $abstract ?: 'app', $params );
}

/**
 * Wrapper function for the `Collection` class.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $items
 * @return object
 */
function collect( $items = [] ) {

	return new Collection( $items );
}

/**
 * Returns the directory path of the framework. If a file is passed in, it'll be
 * appended to the end of the path.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function path( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file ? App::resolve( 'path' ) . "/{$file}" : App::resolve( 'path' );
}

/**
 * Returns the framework version.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function version() {

	return App::resolve( 'version' );
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
