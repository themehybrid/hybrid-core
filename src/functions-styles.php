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

# Remove the default emoji styles. We'll handle this in the stylesheet.
remove_action( 'wp_print_styles', 'print_emoji_styles' );

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
