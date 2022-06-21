<?php
/**
 * Helper functions.
 *
 * Helpers are functions designed for quickly accessing data from the container
 * that we need throughout the framework.
 *
 * @package   HybridCore
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2022, Theme Hybrid
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

use Hybrid\Proxies\App;

if ( ! function_exists( __NAMESPACE__ . '\\app' ) ) {
	/**
	 * The single instance of the app. Use this function for quickly working
	 * with data.  Returns an instance of the `\Hybrid\Core\Application`
	 * class. If the `$abstract` parameter is passed in, it'll resolve and
	 * return the value from the container.
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
}

if ( ! function_exists( __NAMESPACE__ . '\\booted' ) ) {
	/**
	 * Conditional function for checking whether the application has been
	 * booted. Use before launching a new application. If booted, reference
	 * the `app()` instance directly.
	 *
	 * @since  6.0.0
	 * @access public
	 * @return bool
	 */
	function booted() {
		return defined( 'HYBRID_BOOTED' ) && true === HYBRID_BOOTED;
	}
}

if ( ! function_exists( __NAMESPACE__ . '\\path' ) ) {
	/**
	 * Returns the directory path of the framework. If a file is passed in,
	 * it'll be appended to the end of the path.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	function path( $file = '' ) {

		$file = ltrim( $file, '/' );

		return $file
		       ? App::resolve( 'path' ) . "/{$file}"
		       : App::resolve( 'path' );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\\version' ) ) {
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
}
