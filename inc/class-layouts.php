<?php
/**
 * Class for handling layouts. See `inc/layouts.php` for API functions.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Hybrid Layouts class. This is the backbone of the Layouts API.  Theme authors should utilize the 
 * appropriate functions for accessing the `Hybrid_Layouts` object.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Layouts {

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
	public function register_layout( $name, $args = array() ) {

		if ( ! $this->layout_exists( $name ) ) {

			$name = sanitize_html_class( $name );

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

			$args = wp_parse_args( $args, $defaults );

			$args['name'] = $name;

			$this->layouts[ $name ] = (object) $args;

			// If layout has post types, make sure the post type supports `theme-layouts`.
			if ( !empty( $args['post_types'] ) ) {

				foreach ( $args['post_types'] as $post_type ) {

					if ( !post_type_supports( $post_type, 'theme-layouts' ) )
						add_post_type_support( $post_type, 'theme-layouts' );
				}
			}
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
			$instance = new Hybrid_Layouts;

		return $instance;
	}
}