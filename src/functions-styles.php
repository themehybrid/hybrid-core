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

# Register scripts.
add_action( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\customize_controls_register_styles', 0 );

# Remove the default emoji styles. We'll handle this in the stylesheet.
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Registers styles for use with customize controls.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function customize_controls_register_styles() {

	$suffix = get_min_suffix();

	wp_register_style(
		'hybrid-customize-controls',
		uri( "resources/styles/customize-controls{$suffix}.css" ),
		[],
		version()
	);
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

	$styles = array_map( function( $hier ) use ( $slug ) {

		return $slug ? "{$slug}-{$hier}.css" : "{$hier}.css";

	}, get_lang_hierarchy() );

	return locate_file_uri( $styles );
}
