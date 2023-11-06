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
use Psr\Container\ContainerInterface;

/**
 * Container interface.
 *
 * @since  5.0.0
 *
 * @access public
 */
interface Container extends ContainerInterface {

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string $abstract
     * @return bool
     */
    public function bound( $abstract );

    /**
     * Register a binding with the container.
     *
     * @since  5.0.0
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     * @return void
     *
     * @access public
     */
    public function bind( $abstract, $concrete = null, $shared = false );

    /**
     * Bind a callback to resolve with Container::call.
     *
     * @param  array|string $method
     * @param  \Closure     $callback
     * @return void
     */
    public function bindMethod( $method, $callback );

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @since  5.0.0
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     * @return void
     *
     * @access public
     */
    public function bindIf( $abstract, $concrete = null, $shared = false );

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
     * Resolve the given type from the container.
     *
     * @param  string $abstract
     * @param  array  $parameters
     * @return mixed
     * @throws \Hybrid\Contracts\Container\BindingResolutionException
     */
    public function make( $abstract, array $parameters = [] );

    /**
     * Register a shared binding in the container.
     *
     * @since  5.0.0
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @return void
     *
     * @access public
     */
    public function singleton( $abstract, $concrete = null );

    /**
     * Register a shared binding if it hasn't already been registered.
     *
     * @since  5.0.0
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @return void
     *
     * @access public
     */
    public function singletonIf( $abstract, $concrete = null );

    /**
     * Register a scoped binding in the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @return void
     */
    public function scoped( $abstract, $concrete = null );

    /**
     * Register a scoped binding if it hasn't already been registered.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @return void
     */
    public function scopedIf( $abstract, $concrete = null );

    /**
     * Register an existing instance as shared in the container.
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
     * "Extend" an abstract type in the container.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @return void
     * @throws \InvalidArgumentException
     *
     * @access public
     */
    public function extend( $abstract, Closure $closure );

    /**
     * Alias a type to a different name.
     *
     * @since  5.0.0
     * @param  string $abstract
     * @param  string $alias
     * @return void
     * @throws \LogicException
     *
     * @access public
     */
    public function alias( $abstract, $alias );

    /**
     * Assign a set of tags to a given binding.
     *
     * @param  array|string $abstracts
     * @param  array|mixed  ...$tags
     * @return void
     */
    public function tag( $abstracts, $tags );

    /**
     * Resolve all of the bindings for a given tag.
     *
     * @param  string $tag
     * @return iterable
     */
    public function tagged( $tag );

    /**
     * Add a contextual binding to the container.
     *
     * @param  string          $concrete
     * @param  string          $abstract
     * @param  \Closure|string $implementation
     * @return void
     */
    public function addContextualBinding( $concrete, $abstract, $implementation );

    /**
     * Define a contextual binding.
     *
     * @param  string|array $concrete
     * @return \Hybrid\Contracts\Container\ContextualBindingBuilder
     */
    public function when( $concrete );

    /**
     * Get a closure to resolve the given type from the container.
     *
     * @param  string $abstract
     * @return \Closure
     */
    public function factory( $abstract );

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush();

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string $callback
     * @param  array           $parameters
     * @param  string|null     $defaultMethod
     * @return mixed
     */
    public function call( $callback, array $parameters = [], $defaultMethod = null );

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string $abstract
     * @return bool
     */
    public function resolved( $abstract );

    /**
     * Register a new before resolving callback.
     *
     * @param  \Closure|string $abstract
     * @return void
     */
    public function beforeResolving( $abstract, ?Closure $callback = null );

    /**
     * Register a new resolving callback.
     *
     * @param  \Closure|string $abstract
     * @return void
     */
    public function resolving( $abstract, ?Closure $callback = null );

    /**
     * Register a new after resolving callback.
     *
     * @param  \Closure|string $abstract
     * @return void
     */
    public function afterResolving( $abstract, ?Closure $callback = null );

}
