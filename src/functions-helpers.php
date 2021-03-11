<?php
/**
 * Helper functions.
 *
 * Helpers are functions designed for quickly accessing data from the container
 * that we need throughout the framework.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2021, Justin Tadlock
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
