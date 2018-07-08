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

namespace Hybrid\Templates;

use Hybrid\Contracts\Template as TemplateContract;

/**
 * Creates a new object template.
 *
 * @since  5.0.0
 * @access public
 */
class Template implements TemplateContract {

	/**
	 * Type of template. By default, we'll assume this is a post template,
	 * but theme authors can extend this to term or user templates, for
	 * example.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'post';

	/**
	 * Filename of the template.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $filename = '';

	/**
	 * Internationalized text label.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $label = '';

	/**
	 * Array of post types template works with.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $post_types = [ 'page' ];

	/**
	 * Magic method to use in case someone tries to output the object as a
	 * string. We'll just return the name.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {

		return $this->filename();
	}

	/**
	 * Register a new template object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $filename
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $filename, array $args = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->filename = $filename;
	}

	/**
	 * Returns the filename relative to the templates location.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function filename() {

		return $this->filename;
	}

	/**
	 * Returns the internationalized text label for the template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function label() {

		return $this->label;
	}

	/**
	 * Conditional function to check what type of template this is.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return bool
	 */
	public function isType( $type ) {

		return $type === $this->type;
	}

	/**
	 * Conditional function to check if the template is for a post type.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return bool
	 */
	public function forPostType( $type ) {

		return $this->isType( 'post' ) && in_array( $type, $this->post_types );
	}
}
