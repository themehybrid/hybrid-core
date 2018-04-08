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

use Hybrid\Core\Application;
use Hybrid\Common\Collection;

/**
 * The single instance of the app. Use this function for quickly working with
 * data.  Returns an instance of the `Application` class.
 *
 * @since  5.0.0
 * @access public
 * @return object
 */
function app() {

	static $app = null;

	if ( is_null( $app ) ) {
		$app = new Application();
	}

	return $app;
}

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

	// Remove the namespace.
	$class = str_replace( $args['namespace'], '', $class );

	// Explode the full class name into an array of items by sub-namespace
	// and class name.
	$pieces = explode( '\\', $class );

	foreach ( $pieces as $piece ) {

		// Split pieces by uppercase letter.  Assume sub-namespaces and
		// classes are in "PascalCase".
		$pascal = preg_split( '/(?=[A-Z])/', $piece,  -1, PREG_SPLIT_NO_EMPTY );

		// Lowercase and hyphenate the word pieces within a string.
		$new_pieces[] = strtolower( join( '-', $pascal ) );
	}

	// Pop the last item off the array and re-add it with the `class-` prefix
	// and the `.php` file extension.  This is our class file.
	$new_pieces[] = sprintf( 'class-%s.php', array_pop( $new_pieces ) );

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
 * This is a wrapper function for core WP's `get_theme_mod()` function.  Core
 * doesn't provide a filter hook for the default value (useful for child themes).
 * The purpose of this function is to provide that additional filter hook.  To
 * filter the final theme mod, use the core `theme_mod_{$name}` filter hook.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @param  mixed   $default
 * @return mixed
 */
function get_theme_mod( $name, $default = false ) {

	return \get_theme_mod(
		$name,
		apply_filters( "hybrid/theme_mod_{$name}_default", $default )
	);
}

/**
 * Function for setting the content width of a theme.  This does not check if a
 * content width has been set; it simply overwrites whatever the content width is.
 *
 * @since  5.0.0
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
 * @since  5.0.0
 * @access public
 * @return int
 */
function get_content_width() {

	return absint( $GLOBALS['content_width'] );
}

/**
 * Filters an array of templates and prefixes them with the view path.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $templates
 * @return array
 */
function filter_templates( $templates ) {

	array_walk( $templates, function( &$template, $key ) {

		$path = config( 'view' )->path;

		$template = ltrim( str_replace( $path, '', $template ), '/' );

		$template = "{$path}/{$template}";
	} );

	return $templates;
}

/**
 * Returns an array of locations to look for templates.
 *
 * Note that this won't work with the core WP template hierarchy due to an
 * issue that hasn't been addressed since 2010.
 *
 * @link   https://core.trac.wordpress.org/ticket/13239
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_template_locations() {

	$path = config( 'view' )->path ? '/' . config( 'view' )->path : '';

	$locations = [ get_stylesheet_directory() . $path ];

	if ( is_child_theme() ) {
		$locations[] = get_template_directory() . $path;
	}

	return apply_filters( 'hybrid/template_locations', $locations );
}

/**
 * A better `locate_template()` function than what core WP provides. Note that
 * this function merely locates templates and does no loading. Use the core
 * `load_template()` function for actually loading the template.
 *
 * @since  5.0.0
 * @access public
 * @param  array|string  $template_names
 * @return string
 */
function locate_template( $template_names ) {
	$located = '';

	foreach ( (array) $template_names as $template ) {

		foreach ( (array) get_template_locations() as $location ) {

			$file = trailingslashit( $location ) . $template;

			if ( file_exists( $file ) ) {
				$located = $file;
				break 2;
			}
		}
	}

	return $located;
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
		if ( is_child_theme() && file_exists( get_child_file_path( $file ) ) ) {

			$located = get_child_file_path( $file );
			break;

		// If the file exists in the template (parent theme) directory.
		} elseif ( file_exists( get_parent_file_path( $file ) ) ) {

			$located = get_parent_file_path( $file );
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

		// If the file exists in the child theme directory.
		if ( is_child_theme() && file_exists( get_child_file_path( $file ) ) ) {

			$located = get_child_file_uri( $file );
			break;

		// If the file exists in the parent theme directory.
		} elseif ( file_exists( get_parent_file_path( $file ) ) ) {

			$located = get_parent_file_uri( $file );
			break;
		}
	}

	return $located;
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
 * Outputs the nav menu theme location name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return void
 */
 function menu_location_name( $location ) {

	 return esc_html( get_menu_location_name( $location ) );
 }

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function get_menu_location_name( $location ) {

	$locations = get_registered_nav_menus();

	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}

/**
 * Outputs the nav menu name by theme location.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return void
 */
 function menu_name( $location ) {

	 echo esc_html( get_menu_name( $location ) );
 }

/**
 * Function for grabbing a WP nav menu name based on theme location.
 *
 * @since  5.0.0
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
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_min_suffix() {

	return is_script_debug() ? '' : '.min';
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

	return apply_filters(
		'hybrid/is_script_debug',
		defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG
	);
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
 * Utility function for including a file if a theme feature is supported and the
 * file exists. Note that the core WP `require_if_theme_supports()` function
 * doesn't check if the file exists before loading.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $feature
 * @param  string  $file
 * @return void
 */
function require_if_theme_supports( $feature, $file ) {

	if ( current_theme_supports( $feature ) && file_exists( $file ) ) {
		require_once( $file );
	}
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
 * Compatibility function that stores the old post template using the core WP
 * post template naming scheme added in WordPress 4.7.0.  Deletes the old meta.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $post_id
 * @param  string  $template
 * @return void
 */
function post_template_compat( $post_id, $template ) {

	update_post_meta( $post_id, '_wp_page_template', $template );

	delete_post_meta( $post_id, sprintf( '_wp_%s_template', get_post_type( $post_id ) ) );
}

/**
 * Returns a configuration object.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @return object
 */
function config( $name = '' ) {

	return $name ? app()->config->$name : app()->config;
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

	return $file ? trailingslashit( app()->path ) . $file : app()->path;
}

/**
 * Returns the directory URI of the framework. If a file is passed in, it'll be
 * appended to the end of the URI.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function uri( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file ? trailingslashit( app()->uri ) . $file : app()->uri;
}

/**
 * Returns the directory path of the parent theme. If a file is passed in, it'll
 * be appended to the end of the path.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function get_parent_file_path( $file = '' ) {

	return \get_parent_theme_file_path( $file );
}

/**
 * Returns the directory path of the child theme. If a file is passed in, it'll
 * be appended to the end of the path.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function get_child_file_path( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file
	       ? trailingslashit( get_stylesheet_directory() ) . $file
	       : get_stylesheet_directory();
}

/**
 * Returns the directory URI of the parent theme. If a file is passed in, it'll
 * be appended to the end of the URI.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function get_parent_file_uri( $file = '' ) {

	return \get_parent_theme_file_uri( $file );
}

/**
 * Returns the directory URI of the child theme. If a file is passed in, it'll
 * be appended to the end of the URI.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function get_child_file_uri( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file
	       ? trailingslashit( get_stylesheet_directory_uri() ) . $file
	       : get_stylesheet_directory_uri();
}

/**
 * Wrapper function for `wp_verify_nonce()` with a posted value.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $action
 * @param  string  $arg
 * @return bool
 */
function verify_nonce_post( $action = '', $arg = '_wpnonce' ) {

	return isset( $_POST[ $arg ] )
	       ? wp_verify_nonce( sanitize_key( $_POST[ $arg ] ), $action )
	       : false;
}

/**
 * Wrapper function for `wp_verify_nonce()` with a request value.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $action
 * @param  string  $arg
 * @return bool
 */
function verify_nonce_request( $action = '', $arg = '_wpnonce' ) {

	return isset( $_REQUEST[ $arg ] )
	       ? wp_verify_nonce( sanitize_key( $_REQUEST[ $arg ] ), $action )
	       : false;
}
