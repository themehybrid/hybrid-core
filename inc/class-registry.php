<?php
/**
 * Registry class for storing collections of data.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-4.0.html
 */

/**
 * Base registry class.
 *
 * Note that I've made this class `final` while we're still supporting PHP 5.2. Once we drop
 * support and move to at least 5.3.0, we can drop the `final`.  This is so that we can make
 * use of late-static binding.
 *
 * @since  4.0.0
 * @access public
 */
final class Hybrid_Registry {

	/**
	 * Registry instances.
	 *
	 * @since  4.0.0
	 * @access private
	 * @var    array
	 */
	private static $instances = array();

	/**
	 * Array of items in the collection.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    array
	 */
	protected $collection = array();

	/**
	 * Constructor method.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Lock down `__clone()`.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Lock down `__wakeup()`.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function __wakeup() {}

	/**
	 * Register an item.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @param  mixed   $value
	 * @return void
	 */
	public function register( $name, $value ) {

		if ( ! $this->exists( $name ) )
			$this->collection[ $name ] = $value;
	}

	/**
	 * Unregisters an item.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister( $name ) {

		if ( $this->exists( $name ) )
			unset( $this->collection[ $name ] );
	}

	/**
	 * Checks if an item exists.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function exists( $name ) {

		return isset( $this->collection[ $name ] );
	}

	/**
	 * Returns an item.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @return mixed
	 */
	public function get( $name ) {

		return $this->exists( $name ) ? $this->collection[ $name ] : false;
	}

	/**
	 * Returns the entire collection.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return array
	 */
	public function get_collection() {

		return $this->collection;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return object
	 */
	final public static function get_instance( $name = '' ) {

		if ( ! isset( self::$instances[ $name ] ) )
			self::$instances[ $name ] = new self; // @todo - PHP 5.3.0 - change this to `new static();`.

		return self::$instances[ $name ];
	}
}
