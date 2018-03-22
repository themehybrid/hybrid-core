<?php
/**
 * Language-related functions.
 *
 * Internationalization and translation functions that are mostly useful in the
 * framework but might be needed for themes.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Gets the language for the currently-viewed page.  It strips the region from
 * the locale if needed and just returns the language code.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function get_language( $locale = '' ) {

	if ( ! $locale ) {
		$locale = is_admin() ? get_user_locale() : get_locale();
	}

	return sanitize_key( preg_replace( '/(.*?)_.*?$/i', '$1', $locale ) );
}

/**
 * Gets the region for the currently viewed page.  It strips the language from
 * the locale if needed.  Note that not all locales will have a region, so this
 * might actually return the same thing as `get_language()`.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function get_region( $locale = '' ) {

	if ( ! $locale ) {
		$locale = is_admin() ? get_user_locale() : get_locale();
	}

	return sanitize_key( preg_replace( '/.*?_(.*?)$/i', '$1', $locale ) );
}
