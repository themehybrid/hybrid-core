<?php
/**
 * Layout class.  This class is for creating new layout objects.  Layout registration is handled via
 * the `Hybrid_Layout_Factory` class in `inc/class-layout-factory.php`.  Theme authors should utilize
 * the API functions in `inc/layouts.php`.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Creates new layout objects.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Layout {

	/**
	 * Arguments for creating the layout object.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    array
	 */
	protected $args = array();

	/* ====== Magic Methods ====== */

	/**
	 * Magic method for getting layout object properties.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return mixed
	 */
	public function __get( $property ) {

		return isset( $this->$property ) ? $this->args[ $property ] : null;
	}

	/**
	 * Magic method for setting layout object properties.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set( $property, $value ) {

		if ( isset( $this->$property ) )
			$this->args[ $property ] = $value;
	}

	/**
	 * Magic method for checking if a layout property is set.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return bool
	 */
	public function __isset( $property ) {

		return isset( $this->args[ $property ] );
	}

	/**
	 * Don't allow properties to be unset.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return void
	 */
	public function __unset( $property ) {}

	/**
	 * Magic method to use in case someone tries to output the layout object as a string.
	 * We'll just return the layout name.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new layout object
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type bool    $is_global_layout
	 *     @type bool    $is_post_layout
	 *     @type bool    $is_user_layout
	 *     @type string  $label
	 *     @type string  $image
	 *     @type bool    $_builtin
	 *     @type bool    $_internal
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		$name = sanitize_key( $name );

		$defaults = array(
			'is_global_layout' => true,    // Whether to show as an option in the customizer.
			'is_post_layout'   => true,    // Whether to show as an option in the meta box.
			'is_user_layout'   => true,    // Whether to show as an option in user profile (not implemented).
			'label'            => $name,   // Internationalized text label.
			'image'            => '',      // Image URL of the layout design.
			'post_types'       => array(), // Array of post types layout works with.
			'_builtin'         => false,   // Internal use only! Whether the layout is built in.
			'_internal'        => false,   // Internal use only! Whether the layout is internal (cannot be unregistered).
		);

		$this->args = wp_parse_args( $args, $defaults );

		$this->args['name'] = $name;

		$this->add_post_type_support();
	}

	/* ====== Protected Methods ====== */

	/**
	 * Adds post type support for `theme-layouts` in the event that the layout has been
	 * explicitly set for one or more post types.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @return void
	 */
	protected function add_post_type_support() {

		if ( ! empty( $this->post_types ) ) {

			foreach ( $this->post_types as $post_type ) {

				if ( ! post_type_supports( $post_type, 'theme-layouts' ) )
					add_post_type_support( $post_type, 'theme-layouts' );
			}
		}
	}
}
