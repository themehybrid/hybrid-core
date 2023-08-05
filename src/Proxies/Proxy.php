<?php
/**
 * Static proxy class.
 *
 * The base static proxy class. This allows us to create easy-to-use, static
 * classes around shared objects in the container.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Proxies;

use Hybrid\Contracts\Container\Container;

/**
 * Base static proxy class.
 *
 * @since  5.0.0
 *
 * @access public
 */
class Proxy {

    /**
     * The container object.
     *
     * @since  5.0.0
     * @var    \Hybrid\Contracts\Container\Container
     *
     * @access protected
     */
    protected static $container;

    /**
     * Returns the name of the accessor for object registered in the container.
     *
     * @since  5.0.0
     * @return string
     *
     * @access protected
     */
    protected static function accessor() {
        return '';
    }

    /**
     * Sets the container object.
     *
     * @since  5.0.0
     * @return void
     *
     * @access public
     */
    public static function setContainer( Container $container ) {
        static::$container = $container;
    }

    /**
     * Returns the instance from the container.
     *
     * @since  5.0.0
     * @return object
     *
     * @access protected
     */
    protected static function instance() {
        return static::$container->resolve( static::accessor() );
    }

    /**
     * Calls the requested method from the object registered with the
     * container statically.
     *
     * @since  5.0.0
     * @param  string $method
     * @param  array  $args
     * @return mixed
     *
     * @access public
     */
    public static function __callStatic( $method, $args ) {

        $instance = static::instance();

        return $instance ? $instance->$method( ...$args ) : null;
    }

}
