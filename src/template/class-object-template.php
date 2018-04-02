<?php
/**
 * Object template class.
 *
 * This class allows for templates for any object type, which includes `post`,
 * `term`, and `user`.  When viewing a particular single post, term archive, or
 * user/author archive page, the template can be used.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Template;

/**
 * Creates a new object template.
 *
 * @since  5.0.0
 * @access public
 */
class ObjectTemplate {

	/**
	 * Name/ID of the template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * Internationalized text label.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    string
	 */
	public $label = '';

	/**
	 * The theme filename for the template. Use the relative path and not
	 * the full absolute path if dealing with folders.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    string
	 */
	public $filename = '';

	/**
	 * Whether template can be used as a single post template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_post_template = true;

	/**
	 * Whether template can be used as a term archive template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_term_template = false;

	/**
	 * Whether template can be used as a user archive template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_user_template = false;

	/**
	 * Array of post types template works with.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    array
	 */
	public $post_types = [];

	/**
	 * Array of taxonomies the template works with.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    array
	 */
	public $taxonomies = [];

	/* ====== Magic Methods ====== */

	/**
	 * Don't allow properties to be unset.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $property
	 * @return void
	 */
	public function __unset( $property ) {}

	/**
	 * Magic method to use in case someone tries to output the layout object
	 * as a string. We'll just return the layout name.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {

		return $this->name;
	}

	/**
	 * Register a new template object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type bool    $is_post_template
	 *     @type bool    $is_term_template
	 *     @type bool    $is_user_template
	 *     @type string  $label
	 *     @type string  $filename
	 *     @type array   $post_types
	 *     @type array   $taxonomies
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->name = sanitize_key( $name );
	}
}
