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
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use ArrayAccess;
use SplObjectStorage;
use Psr\Container\ContainerInterface;

/**
 * A simple container for objects.
 *
 * @since  5.0.0
 * @access public
 */
class Container implements ContainerInterface, ArrayAccess {

	/**
	 * Stored definitions of objects.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	 protected $bindings = [];

	/**
	 * Array of single instance objects.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $instances = [];

	/**
	 * Set to an instance of `SplObjectStorage`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    object
	 */
	protected $factories;

	/**
	 * Set up a new container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  array  $definitions
	 * @return void
	 */
	public function __construct( array $definitions = [] ) {

		$this->factories = new SplObjectStorage();

		foreach ( $definitions as $alias => $value ) {

			$this->add( $alias, $value );
		}
	}

	/**
	 * Add an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $alias
	 * @param  object  $value
	 * @param  bool    $shared
	 * @return void
	 */
	public function add( $alias, $value = null, $shared = false ) {

		if ( isset( $this->bindings[ $alias ] ) ) {
			return;
		}

		$this->bindings[ $alias ] = compact( 'value', 'shared' );
	}

	/**
	 * Remove an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $alias
	 * @return void
	 */
	public function remove( $alias ) {

		if ( $this->has( $alias ) ) {

			unset( $this->bindings[ $alias ], $this->instances[ $alias ] );
		}
	}

	/**
	 * Return an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $alias
	 * @return object
	 */
	public function get( $alias ) {

		if ( ! $this->has( $alias ) ) {
			return false;
		}

		$definition = $this->bindings[ $alias ]['value'];

		// If this is not a closure, return the definition.
		if ( ! is_object( $definition ) || ! method_exists( $definition, '__invoke' ) ) {

			return $definition;
		}

		// Return a single instance.
		if ( $this->bindings[ $alias ]['shared'] ) {

			// If the instance isn't set yet, get it.
			if ( ! isset( $this->instances[ $alias ] ) ) {

				$this->instances[ $alias ] = $definition( $this );
			}

			return $this->instances[ $alias ];
		}

		// If this is a factory, call it.
		if ( isset( $this->factories[ $definition ] ) ) {

			return $definition( $this );
		}

		// Return the instance.
		return $definition( $this );
	}

	/**
	 * Check if an object exists.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $alias
	 * @return bool
	 */
	public function has( $alias ) {

		return isset( $this->bindings[ $alias ] );
	}

	/**
	 * Add a shared object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $alias
	 * @param  object  $value
	 * @return void
	 */
	public function singleton( $alias, $value = null ) {

		$this->add( $alias, $value, true );
	}

	/**
	 * Adds a factory and returns the callable object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  callable  $callable
	 * @return callable
	 */
	public function factory( $callable ) {

		$this->factories->attach( $callabale );

		return $callable;
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
