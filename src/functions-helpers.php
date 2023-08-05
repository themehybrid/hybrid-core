<?php
/**
 * Helper functions.
 *
 * Helpers are functions designed for quickly accessing data from the container
 * that we need throughout the framework.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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
     * @param  string $abstract
     * @param  array  $params
     * @return mixed
     *
     * @access public
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
     * @return bool
     *
     * @access public
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
     * @param  string $file
     * @return string
     *
     * @access public
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
     * @return string
     *
     * @access public
     */
    function version() {
        return App::resolve( 'version' );
    }
}
