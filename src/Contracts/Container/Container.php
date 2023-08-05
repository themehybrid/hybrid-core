<?php
/**
 * Container contract.
 *
 * Container classes should be used for storing, retrieving, and resolving
 * classes/objects passed into them.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Container;

use Closure;

/**
 * Container interface.
 *
 * @since  5.0.0
 *
 * @access public
 */
interface Container {

    /**
     * Add a binding. The abstract should be a key, abstract class name, or
     * interface name. The concrete should be the concrete implementation of
     * the abstract.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  mixed  $concrete
     * @param  bool   $shared
     * @return void
     *
     * @access public
     */
    public function bind( $abstract, $concrete = null, $shared = false );

    /**
     * Alias for `bind()`.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  mixed  $concrete
     * @param  bool   $shared
     * @return void
     *
     * @access public
     */
    public function add( $abstract, $concrete = null, $shared = false );

    /**
     * Remove a binding.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @return void
     *
     * @access public
     */
    public function remove( $abstract );

    /**
     * Resolve and return the binding.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  array  $parameters
     * @return mixed
     *
     * @access public
     */
    public function resolve( $abstract, array $parameters = [] );

    /**
     * Alias for `resolve()`.
     *
     * Follows the PSR-11 standard. Do not alter.
     *
     * @since  5.0.0
     * @link https://www.php-fig.org/psr/psr-11/
     * @param  string $abstract
     * @return object
     *
     * @access public
     */
    public function get( $abstract );

    /**
     * Check if a binding exists.
     *
     * Follows the PSR-11 standard. Do not alter.
     *
     * @since  5.0.0
     * @link https://www.php-fig.org/psr/psr-11/
     * @param  string $abstract
     * @return bool
     *
     * @access public
     */
    public function has( $abstract );

    /**
     * Add a shared binding.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  object $concrete
     * @return void
     *
     * @access public
     */
    public function singleton( $abstract, $concrete = null );

    /**
     * Add an existing instance.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  mixed  $instance
     * @return mixed
     *
     * @access public
     */
    public function instance( $abstract, $instance );

    /**
     * Extend a binding.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @return void
     *
     * @access public
     */
    public function extend( $abstract, Closure $closure );

    /**
     * Create an alias for an abstract type.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  string $alias
     * @return void
     *
     * @access public
     */
    public function alias( $abstract, $alias );

}
