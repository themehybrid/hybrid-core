<?php
/**
 * Container class.
 *
 * This file maintains the `Container` class, which handles storing objects for
 * later use. It's primarily designed for handling single instances to avoid
 * globals or singletons. This is just a basic container for the purposes of
 * WordPress theme dev and isn't as powerful as some of the more robust
 * containers available in the larger PHP world.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2021, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Container;

use ArrayAccess;
use Closure;
use ReflectionClass;
use Hybrid\Contracts\Container\Container as ContainerContract;

/**
 * A simple container for objects.
 *
 * @since  5.0.0
 * @access public
 */
class Container implements ContainerContract, ArrayAccess {

	/**
	* Stored definitions of objects.
	*
	* @since  5.0.0
	* @access protected
	* @var    array
	*/
	protected $bindings = [];

	/**
	 * Array of aliases for bindings.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $aliases = [];

	/**
	* Array of single instance objects.
	*
	* @since  5.0.0
	* @access protected
	* @var    array
	*/
	protected $instances = [];

	/**
	* Array of object extensions.
	*
	* @since  5.0.0
	* @access protected
	* @var    array
	*/
	protected $extensions = [];

	/**
	* Set up a new container.
	*
	* @since  5.0.0
	* @access public
	* @param  array  $definitions
	* @return void
	*/
	public function __construct( array $definitions = [] ) {

		foreach ( $definitions as $abstract => $concrete ) {

			$this->add( $abstract, $concrete );
		}
	}

	/**
	 * Add a binding. The abstract should be a key, abstract class name, or
	 * interface name. The concrete should be the concrete implementation of
	 * the abstract. If no concrete is given, its assumed the abstract
	 * handles the concrete implementation.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  mixed   $concrete
	 * @param  bool    $shared
	 * @return void
	 */
	public function bind( $abstract, $concrete = null, $shared = false ) {

		unset( $this->instances[ $abstract ] );

		if ( is_null( $concrete ) ) {
			$concrete = $abstract;
		}

		$this->bindings[ $abstract ]   = compact( 'concrete', 'shared' );
		$this->extensions[ $abstract ] = [];
	}

	/**
	* Alias for `bind()`.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $abstract
	* @param  mixed   $concrete
	* @param  bool    $shared
	* @return void
	*/
	public function add( $abstract, $concrete = null, $shared = false ) {

		$this->bind( $abstract, $concrete, $shared );
	}

	/**
	 * Remove a binding.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @return void
	 */
	public function remove( $abstract ) {

		if ( $this->has( $abstract ) ) {

			unset( $this->bindings[ $abstract ], $this->instances[ $abstract ] );
		}
	}

	/**
	 * Resolve and return the binding.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function resolve( $abstract, array $parameters = [] ) {

		// Get the true abstract name.
		$abstract = $this->getAbstract( $abstract );

		// If this is being managed as an instance and we already have
		// the instance, return it now.
		if ( isset( $this->instances[ $abstract ] ) ) {

			return $this->instances[ $abstract ];
		}

		// Get the concrete implementation.
		$concrete = $this->getConcrete( $abstract );

		// If we can't build an object, assume we should return the value.
		if ( ! $this->isBuildable( $concrete ) ) {

			// If we don't actually have this, return false.
			if ( ! $this->has( $abstract ) ) {
				return false;
			}

			return $concrete;
		}

		// Build the object.
		$object = $this->build( $concrete, $parameters );

		if ( ! $this->has( $abstract ) ) {
			return $object;
		}

		// If shared instance, make sure to store it in the instances
		// array so that we're not creating new objects later.
		if ( $this->bindings[ $abstract ]['shared'] && ! isset( $this->instances[ $abstract ] ) ) {

			$this->instances[ $abstract ] = $object;
		}

		// Run through each of the extensions for the object.
		foreach ( $this->extensions[ $abstract ] as $extension ) {

			$object = new $extension( $object, $this );
		}

		// Return the object.
		return $object;
	}

	/**
	 * Creates an alias for an abstract. This allows you to add names that
	 * are easy to access without remembering more complex class names.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  string  $alias
	 * @return void
	 */
	public function alias( $abstract, $alias ) {

		$this->aliases[ $alias ] = $abstract;
	}

	/**
	* Alias for `resolve()`.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $abstract
	* @return object
	*/
	public function get( $abstract ) {

		return $this->resolve( $abstract );
	}

	/**
	* Check if a binding exists.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $abstract
	* @return bool
	*/
	public function has( $abstract ) {

		return isset( $this->bindings[ $abstract ] ) || isset( $this->instances[ $abstract ] );
	}

	/**
	 * Add a shared binding.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  object  $concrete
	 * @return void
	 */
	public function singleton( $abstract, $concrete = null ) {

		$this->add( $abstract, $concrete, true );
	}

	/**
	 * Add an existing instance. This can be an instance of an object or a
	 * single value that should be stored.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * @return mixed
	 */
	public function instance( $abstract, $instance ) {

		$this->instances[ $abstract ] = $instance;

		return $instance;
	}

	/**
	 * Extend a binding with something like a decorator class. Cannot
	 * extend resolved instances.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  Closure $closure
	 * @return void
	 */
	public function extend( $abstract, Closure $closure ) {

		$abstract = $this->getAbstract( $abstract );

		$this->extensions[ $abstract ][] = $closure;
	}

	/**
	 * Checks if we're dealing with an alias and returns the abstract. If
	 * not an alias, return the abstract passed in.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $abstract
	 * @return string
	 */
	protected function getAbstract( $abstract ) {

		if ( isset( $this->aliases[ $abstract ] ) ) {
			return $this->aliases[ $abstract ];
		}

		return $abstract;
	}

	/**
	 * Gets the concrete of an abstract.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $abstract
	 * @return mixed
	 */
	protected function getConcrete( $abstract ) {

		$concrete = false;
		$abstract = $this->getAbstract( $abstract );

		if ( $this->has( $abstract ) ) {
			$concrete = $this->bindings[ $abstract ]['concrete'];
		}

		return $concrete ?: $abstract;
	}

	/**
	 * Determines if a concrete is buildable. It should either be a closure
	 * or a concrete class.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  mixed    $concrete
	 * @return bool
	 */
	protected function isBuildable( $concrete ) {

		return $concrete instanceof Closure
		       || ( is_string( $concrete ) && class_exists( $concrete ) );
	}

	/**
	 * Builds the concrete implementation. If a closure, we'll simply return
	 * the closure and pass the included parameters. Otherwise, we'll resolve
	 * the dependencies for the class and return a new object.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  mixed  $concrete
	 * @param  array  $parameters
	 * @return object
	 */
	protected function build( $concrete, array $parameters = [] ) {

		if ( $concrete instanceof Closure ) {
			return $concrete( $this, $parameters );
		}

		$reflect = new ReflectionClass( $concrete );

		$constructor = $reflect->getConstructor();

		if ( ! $constructor ) {
			return new $concrete();
		}

		return $reflect->newInstanceArgs(
			$this->resolveDependencies( $constructor->getParameters(), $parameters )
		);
	}

	/**
	 * Resolves the dependencies for a method's parameters.
	 *
	 * @todo Handle errors when we can't solve a dependency.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array     $dependencies
	 * @param  array     $parameters
	 * @return array
	 */
	protected function resolveDependencies( array $dependencies, array $parameters ) {

		$args = [];

		foreach ( $dependencies as $dependency ) {

			// If a dependency is set via the parameters passed in, use it.
			if ( isset( $parameters[ $dependency->getName() ] ) ) {

				$args[] = $parameters[ $dependency->getName() ];

			// If the parameter is a class, resolve it.
			} elseif ( ! is_null( $dependency->getType() ) && class_exists( $dependency->getType()->getName() ) ) {

				$args[] = $this->resolve( $dependency->getType()->getName() );

			// Else, use the default parameter value.
			} elseif ( $dependency->isDefaultValueAvailable() ) {

				$args[] = $dependency->getDefaultValue();
			}
		}

		return $args;
	}

	/**
	* Sets a property via `ArrayAccess`.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @param  mixed   $value
	* @return void
	*/
	public function offsetSet( $name, $value ) {

		$this->add( $name, $value );
	}

	/**
	* Unsets a property via `ArrayAccess`.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @return void
	*/
	public function offsetUnset( $name ) {

		$this->remove( $name );
	}

	/**
	* Checks if a property exists via `ArrayAccess`.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @return bool
	*/
	public function offsetExists( $name ) {

		return $this->has( $name );
	}

	/**
	* Returns a property via `ArrayAccess`.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @return mixed
	*/
	public function offsetGet( $name ) {

		return $this->get( $name );
	}


	/**
	* Magic method when trying to set a property.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @param  mixed   $value
	* @return void
	*/
	public function __set( $name, $value ) {

		$this->add( $name, $value );
	}

	/**
	* Magic method when trying to unset a property.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @return void
	*/
	public function __unset( $name ) {

		$this->remove( $name );
	}

	/**
	* Magic method when trying to check if a property exists.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @return bool
	*/
	public function __isset( $name ) {

		return $this->has( $name );
	}

	/**
	* Magic method when trying to get a property.
	*
	* @since  5.0.0
	* @access public
	* @param  string  $name
	* @return mixed
	*/
	public function __get( $name ) {

		return $this->get( $name );
	}
}
