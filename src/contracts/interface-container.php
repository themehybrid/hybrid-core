<?php
/**
 * Container interface.
 *
 * Defines the interface that containers must use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts;

use Closure;
use Psr\Container\ContainerInterface;

/**
 * Container interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Container extends ContainerInterface {

	/**
	 * Bind an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  object  $concrete
	 * @param  bool    $shared
	 * @return void
	 */
	public function bind( $abstract, $concrete = null, $shared = false );

	/**
	 * Remove an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @return void
	 */
	public function remove( $abstract );

	/**
	 * Resolve and return the definition.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function resolve( $abstract, $parameters = [] );

	/**
	 * Add a shared object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  object  $concrete
	 * @return void
	 */
	public function singleton( $abstract, $concrete = null );

	/**
	 * Add an instance of an object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * @return mixed
	 */
	 public function instance( $abstract, $instance );

	 /**
	  * Extend a binding.
	  *
	  * @since  5.0.0
	  * @access public
	  * @param  string  $abstract
	  * @param  Closure $closure
	  * @return void
	  */
	 public function extend( $abstract, Closure $closure );
}
