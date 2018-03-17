<?php
/**
 * Internationalization and translation functions. This file provides a few functions for use by theme
 * authors.  It also handles properly loading translation files for both the parent and child themes.  Part
 * of the functionality below handles consolidating the framework's textdomains with the textdomain of the
 * parent theme to avoid having multiple translation files.
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
 * Gets the language for the currently-viewed page.  It strips the region from the locale if needed
 * and just returns the language code.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function get_language( $locale = '' ) {

	if ( ! $locale )
		$locale = get_locale();

	return sanitize_key( preg_replace( '/(.*?)_.*?$/i', '$1', $locale ) );
}

/**
 * Gets the region for the currently viewed page.  It strips the language from the locale if needed.  Note that
 * not all locales will have a region, so this might actually return the same thing as `hybrid_get_language()`.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function get_region( $locale = '' ) {

	if ( ! $locale )
		$locale = get_locale();

	return sanitize_key( preg_replace( '/.*?_(.*?)$/i', '$1', $locale ) );
}
