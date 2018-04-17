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
	 * Add an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  object  $concrete
	 * @param  bool    $shared
	 * @return void
	 */
	public function add( $abstract, $concrete = null, $shared = false ) {

		if ( isset( $this->bindings[ $abstract ] ) ) {
			return;
		}

		$this->bindings[ $abstract ] = compact( 'concrete', 'shared' );
	}

	/**
	 * Remove an object.
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
	 * Resolve and return the definition.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function resolve( $abstract, $parameters = [] ) {

 		if ( ! $this->has( $abstract ) ) {
 			return false;
 		}

 		// If this is being managed as an instance and we already have
 		// the instance, return it now.
 		if ( isset( $this->instances[ $abstract ] ) ) {

 			return $this->instances[ $abstract ];
 		}

		// Get the definition.
 		$definition = $this->bindings[ $abstract ]['concrete'];

 		// If this is not a closure, return the definition.
 		if ( ! is_object( $definition ) || ! method_exists( $definition, '__invoke' ) ) {

 			return $definition;
 		}

 		// Return a single instance.
 		if ( $this->bindings[ $abstract ]['shared'] ) {

 			// If the instance isn't set yet, get it.
 			if ( ! isset( $this->instances[ $abstract ] ) ) {

 				$this->instances[ $abstract ] = $definition( $this, $parameters );
 			}

 			return $this->instances[ $abstract ];
 		}

 		// Return the instance.
 		return $definition( $this, $parameters );
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
	 * Check if an object exists.
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
	 * Add a shared object.
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
	 * Add an instance of an object.
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
