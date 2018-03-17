<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Returns an `Element` object.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $tag
 * @param  string  $content
 * @param  object  $attr
 */
function element( $tag, $content = '', Attributes $attr = null ) {

	return new Element( $tag, $content, $attr );
}

/**
 * This is a wrapper function for core WP's `get_theme_mod()` function.  Core doesn't
 * provide a filter hook for the default value (useful for child themes).  The purpose
 * of this function is to provide that additional filter hook.  To filter the final
 * theme mod, use the core `theme_mod_{$name}` filter hook.
 *
 * @since  4.0.0
 * @access public
 * @param  string  $name
 * @param  mixed   $default
 * @return mixed
 */
function get_theme_mod( $name, $default = false ) {

	return get_theme_mod( $name, apply_filters( "hybrid_theme_mod_{$name}_default", $default ) );
}

/**
 * Function for setting the content width of a theme.  This does not check if a content width has been set; it
 * simply overwrites whatever the content width is.
 *
 * @since  1.2.0
 * @access public
 * @param  int    $width
 * @return void
 */
function set_content_width( $width = '' ) {

	$GLOBALS['content_width'] = absint( $width );
}

/**
 * Function for getting the theme's content width.
 *
 * @since  1.2.0
 * @access public
 * @return int
 */
function get_content_width() {

	return absint( $GLOBALS['content_width'] );
}

/**
 * Loops through an array of file names within both the child and parent theme
 * directories.  Once a file is found, the full path to the file is returned.
 *
 * @since  5.0.0
 * @access public
 * @param  array|string  $file_names
 * @return string
 */
function locate_file_path( $file_names ) {
	$located = '';

	// Loops through each of the given file names.
	foreach ( (array) $file_names as $file ) {

		// If the file exists in the stylesheet (child theme) directory.
		if ( is_child_theme() && file_exists( app()->child_dir . $file ) ) {
			$located = app()->child_dir . $file;
			break;
		}

		// If the file exists in the template (parent theme) directory.
		elseif ( file_exists( app()->parent_dir . $file ) ) {
			$located = app()->parent_dir . $file;
			break;
		}
	}

	return $located;
}

/**
 * Loops through an array of file names within both the child and parent theme
 * directories.  Once a file is found, the URI to the file is returned.
 *
 * @since  5.0.0
 * @access public
 * @param  array|string  $file_names
 * @return string
 */
function locate_file_uri( $file_names ) {
	$located = '';

	// Loops through each of the given file names.
	foreach ( (array) $file_names as $file ) {

		// If the file exists in the stylesheet (child theme) directory.
		if ( is_child_theme() && file_exists( app()->child_dir . $file ) ) {
			$located = app()->child_uri . $file;
			break;
		}

		// If the file exists in the template (parent theme) directory.
		elseif ( file_exists( app()->parent_dir . $file ) ) {
			$located = app()->parent_uri . $file;
			break;
		}
	}

	return $located;
}

/**
 * Converts a hex color to RGB.  Returns the RGB values as an array.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $hex
 * @return array
 */
function hex_to_rgb( $hex ) {

	// Remove "#" if it was added.
	$color = trim( $hex, '#' );

	// If the color is three characters, convert it to six.
        if ( 3 === strlen( $color ) )
		$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];

	// Get the red, green, and blue values.
	$red   = hexdec( $color[0] . $color[1] );
	$green = hexdec( $color[2] . $color[3] );
	$blue  = hexdec( $color[4] . $color[5] );

	// Return the RGB colors as an array.
	return array( 'r' => $red, 'g' => $green, 'b' => $blue );
}

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function get_menu_location_name( $location ) {

	$locations = get_registered_nav_menus();

	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}

/**
 * Function for grabbing a WP nav menu name based on theme location.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function get_menu_name( $location ) {

	$locations = get_nav_menu_locations();

	$menu = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : '';

	return $menu ? $menu->name : '';
}

/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function get_min_suffix() {

	return is_script_debug() ? '' : '.min';
}

/**
 * Conditional check to determine if we are in script debug mode.  This is generally used
 * to decide whether to load development versions of scripts/styles.
 *
 * @since  4.0.0
 * @access public
 * @return bool
 */
function is_script_debug() {

	return apply_filters( 'hybrid_is_script_debug', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
}

/**
 * Replaces `%1$s` and `%2$s` with the template and stylesheet directory paths.
 *
 * @since  4.0.0
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
 * @since  4.0.0
 * @access public
 * @param  string  $value
 * @return string
 */
function sprintf_theme_uri( $value ) {

	return sprintf( $value, get_template_directory_uri(), get_stylesheet_directory_uri() );
}

/**
 * Utility function for including a file if a theme feature is supported and the file exists.  Note
 * that this should not be used in place of the core `require_if_theme_supports()` function.  We need
 * this particular function for checking if the file exists first, which the core function does not
 * handle at the moment.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $feature
 * @param  string  $file
 * @return void
 */
function require_if_theme_supports( $feature, $file ) {

	if ( current_theme_supports( $feature ) && file_exists( $file ) )
		require_once( $file );
}

/**
 * Compatibility function that stores the old post template using the core WP
 * post template naming scheme added in WordPress 4.7.0.  Deletes the old
 * meta.
 *
 * @since  4.0.0
 * @access public
 * @param  int     $post_id
 * @param  string  $template
 * @return void
 */
function post_template_compat( $post_id, $template ) {

	update_post_meta( $post_id, '_wp_page_template', $template );

	delete_post_meta( $post_id, sprintf( '_wp_%s_template', get_post_type( $post_id ) ) );
}
