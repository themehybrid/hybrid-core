<?php
/**
 * Layout class.  This class is for creating new layout objects.  Theme authors should utilize
 * the API functions in `inc/functions-layouts.php`.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
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
	 * Whether to show as an option in the customizer.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_global_layout = true;

	/**
	 * Whether to show as an option in the post meta box.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_post_layout = true;

	/**
	 * Whether to show as an option on taxonomy term pages.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_term_layout = true;

	/**
	 * Whether to show as an option in user profile (not implemented).
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_user_layout = true;

	/**
	 * Internationalized text label.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $label = '';

	/**
	 * Image URL of the layout design.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $image = '';

	/**
	 * Array of post types layout works with.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $post_types = array();

	/**
	 * Array of taxonomies layout works with.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $taxonomies = array();

	/**
	 * Internal use only! Whether the layout is built in.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $_builtin = false;

	/**
	 * Internal use only! Whether the layout is internal (cannot be unregistered).
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    bool
	 */
	public $_internal = false;

	/* ====== Magic Methods ====== */

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

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );

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
