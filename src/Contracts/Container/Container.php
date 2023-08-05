<?php
/**
 * Container contract.
 *
 * Container classes should be used for storing, retrieving, and resolving
 * classes/objects passed into them.
 *
 * @package   HybridCore
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @link      https://github.com/themehybrid/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Container;

use Closure;

/**
 * Container interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Container {

	/**
	 * Add a binding. The abstract should be a key, abstract class name, or
	 * interface name. The concrete should be the concrete implementation of
	 * the abstract.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  mixed   $concrete
	 * @param  bool    $shared
	 * @return void
	 */
	public function bind( $abstract, $concrete = null, $shared = false );

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
	public function add( $abstract, $concrete = null, $shared = false );

	/**
	 * Remove a binding.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @return void
	 */
	public function remove( $abstract );

	/**
	 * Resolve and return the binding.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function resolve( $abstract, array $parameters = [] );

	/**
	 * Alias for `resolve()`.
	 *
	 * Follows the PSR-11 standard. Do not alter.
	 * @link https://www.php-fig.org/psr/psr-11/
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @return object
	 */
	public function get( $abstract );

	/**
	 * Check if a binding exists.
	 *
	 * Follows the PSR-11 standard. Do not alter.
	 * @link https://www.php-fig.org/psr/psr-11/
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @return bool
	 */
	public function has( $abstract );

	/**
	 * Add a shared binding.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $abstract
	 * @param  object  $concrete
	 * @return void
	 */
	public function singleton( $abstract, $concrete = null );

	/**
	 * Add an existing instance.
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

	 /**
	  * Create an alias for an abstract type.
	  *
	  * @since  5.0.0
	  * @access public
	  * @param  string  $abstract
	  * @param  string  $alias
	  * @return void
	  */
	 public function alias( $abstract, $alias );
}
