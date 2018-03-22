<?php
/**
 * Functions for handling styles in the framework.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Active theme style filters.
add_filter( 'stylesheet_uri', __NAMESPACE__ . '\min_stylesheet_uri', 5, 2 );

# Filters the WP locale stylesheet.
add_filter( 'locale_stylesheet_uri', __NAMESPACE__ . '\locale_stylesheet_uri', 5 );

# Remove the default emoji styles. We'll handle this in the stylesheet.
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Returns the parent theme stylesheet URI.  Will return the active theme's
 * stylesheet URI if no child theme is active. Be sure to check `is_child_theme()`
 * when using.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_parent_stylesheet_uri() {

	// Get the minified suffix.
	$suffix = get_min_suffix();

	// Get the parent theme stylesheet.
	$stylesheet_uri = app()->parent_uri . 'style.css';

	// If a '.min' version of the parent theme stylesheet exists, use it.
	if ( $suffix && file_exists( app()->parent_dir . "style{$suffix}.css" ) ) {
		$stylesheet_uri = app()->parent_uri . "style{$suffix}.css";
	}

	return apply_filters(
		app()->namespace . '/get_parent_stylesheet_uri',
		$stylesheet_uri
	);
}

/**
 * Filters the 'stylesheet_uri' to allow theme developers to offer a minimized
 * version of their main `style.css` file.  It will detect if a `style.min.css`
 * file is available and use it if `SCRIPT_DEBUG` is disabled.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $stylesheet_uri      The URI of the active theme's stylesheet.
 * @param  string  $stylesheet_dir_uri  The directory URI of the active theme's stylesheet.
 * @return string
 */
function min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	// Get the minified suffix.
	$suffix = get_min_suffix();

	// Use the .min stylesheet if available.
	if ( $suffix ) {

		// Remove the stylesheet directory URI from the file name.
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		// Change the stylesheet name to 'style.min.css'.
		$stylesheet = str_replace( '.css', "{$suffix}.css", $stylesheet );

		// If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet.
		if ( file_exists( app()->child_dir . $stylesheet ) ) {
			$stylesheet_uri = esc_url( trailingslashit( $stylesheet_dir_uri ) . $stylesheet );
		}
	}

	// Return the theme stylesheet.
	return $stylesheet_uri;
}

/**
 * Filters `locale_stylesheet_uri` with a more robust version for checking
 * locale/language/region/direction stylesheets.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $stylesheet_uri
 * @return string
 */
function locale_stylesheet_uri( $stylesheet_uri ) {

	$locale_style = get_locale_style();

	return $locale_style ? esc_url( $locale_style ) : $stylesheet_uri;
}

/**
 * Searches for a locale stylesheet.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $slug
 * @return string
 */
function get_locale_style( $slug = '' ) {

	$styles = array_map( function( $hier ) {

		return $slug ? "{$slug}-{$hier}.css" : "{$hier}.css";

	}, get_lang_hierarchy() );

	return locate_file_uri( $styles );
}
