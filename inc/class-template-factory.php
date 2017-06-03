<?php
/**
 * Template factory class.  This is a singleton factory class for handling the registering and
 * storing of `Hybrid_Template` objects.  Theme authors should utilize the API functions found
 * in `inc/functions-templates.php`.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Hybrid Template Factory class. This is the backbone of the Templates API.  Theme authors should
 * utilize the appropriate functions for accessing the `Hybrid_Template_Factory` object.
 *
 * @since  4.0.0
 * @access public
 */
class Hybrid_Template_Factory {

	/**
	 * Array of template objects.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    array
	 */
	public $templates = array();

	/**
	 * Array of filenames.  While we're storing templates by name, we also
	 * can't have duplicate file names, so we're going to store the template
	 * objects via filename as well.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var    array
	 */
	public $filenames = array();

	/**
	 * Constructor method.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Register a new template object
	 *
	 * @see    Hybrid_Template::__construct()
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_template( $name, $args = array() ) {

		if ( ! $this->template_exists( $name ) && $args['filename'] && ! $this->filename_exists( $args['filename'] ) ) {

			$template = new Hybrid_Template( $name, $args );

			$this->templates[ $template->name ]     = $template;
			$this->filenames[ $template->filename ] = $template;
		}
	}

	/**
	 * Unregisters a template object.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister_template( $name ) {

		if ( $this->template_exists( $name ) ) {

			$template = $this->get_template( $name );

			unset( $templates[ $template->name ]     );
			unset( $filenames[ $template->filename ] );
		}
	}

	/**
	 * Checks if a template exists.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function template_exists( $name ) {

		return isset( $this->templates[ $name ] ) || $this->filename_exists( $name );
	}

	/**
	 * Gets a template object.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $name
	 * @return object|bool
	 */
	public function get_template( $name ) {

		if ( $this->template_exists( $name ) )
			return $this->templates[ $name ];

		return $this->get_filename( $name );
	}

	/**
	 * Checks if a template exists by filename.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $filename
	 * @return bool
	 */
	public function filename_exists( $filename ) {

		return isset( $this->filenames[ $filename ] );
	}

	/**
	 * Gets a template object by filename.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $filename
	 * @return object|bool
	 */
	public function get_filename( $filename ) {

		return $this->filename_exists( $filename ) ? $this->filenames[ $filename ] : false;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}
}
