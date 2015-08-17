<?php
/**
 * Layout factory class.  This is a singleton factory class for handling the registering and
 * storing of `Hybrid_Layout` objects.  Theme authors should utilize the API functions found
 * in `inc/layouts.php`.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Hybrid Layout Factory class. This is the backbone of the Layouts API.  Theme authors should
 * utilize the appropriate functions for accessing the `Hybrid_Layout_Factory` object.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Layout_Factory {

	/**
	 * Array of layout objects.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $layouts = array();

	/**
	 * Constructor method.
	 *
	 * @since  3.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Register a new layout object
	 *
	 * @see    Hybrid_Layout::__construct()
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_layout( $name, $args = array() ) {

		if ( ! $this->layout_exists( $name ) ) {

			$layout = new Hybrid_Layout( $name, $args );

			$this->layouts[ $layout->name ] = $layout;
		}
	}

	/**
	 * Unregisters a layout object.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister_layout( $name ) {

		if ( $this->layout_exists( $name ) && false === $this->get( $name )->_internal )
			unset( $this->layouts[ $name ] );
	}

	/**
	 * Checks if a layout exists.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function layout_exists( $name ) {

		return isset( $this->layouts[ $name ] );
	}

	/**
	 * Gets a layout object.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @return object|bool
	 */
	public function get_layout( $name ) {

		return $this->layout_exists( $name ) ? $this->layouts[ $name ] : false;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new Hybrid_Layout_Factory;

		return $instance;
	}
}
