<?php
/**
 * View class.
 *
 * This file maintains the `View` class.  It's used for setting up and rendering
 * theme template files.  Views are a bit like a suped-up version of the core
 * WordPress `get_template_part()` function.  However, it allows you to build a
 * hierarchy of potential templates as well as pass in any arbitrary data to your
 * templates for use.
 *
 * Every effort has been made to make this compliant with WordPress.org theme
 * directory guidelines by utilizing the core `locate_template()` function as
 * well as providing compatible action hooks with `get_template_part()` and
 * other `get_*()` functions for templates.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * View class.
 *
 * @since  1.0.0
 * @access public
 */
class View {

	/**
	 * Name of the view. This is primarily used as the folder name. However,
	 * it can also be the filename as the finall fallback if no folder exists.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	protected $name = '';

	/**
	 * Array of slugs to look for. This creates the hierarchy based on the
	 * `$name` property (e.g., `{$name}/{$slug}.php`). Slugs are used in
	 * the order that they are set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	protected $slugs = [];

	/**
	 * An array of data that is passed into the view template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	protected $data = [];

	/**
	 * The template filename.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	protected $template = '';

	/**
	 * Sets up the view properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $slugs
	 * @param  object  $data
	 * @return object
	 */
	public function __construct( $name, $slugs = [], Collection $data = null ) {

		$this->name  = $name;
		$this->slugs = (array) $slugs;
		$this->data  = $data;

		// Apply filters after all the properties have been assigned.
		// This way, the full object is available to filters.
		$this->slugs = apply_filters( app()->namespace . "/view_slugs_{$this->name}", $this->slugs, $this );
		$this->data  = apply_filters( app()->namespace . "/view_data_{$this->name}",  $this->data,  $this );
	}

	/**
	 * When attempting to use the object as a string, return the template output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	 public function __toString() {

		 return $this->fetch();
	 }

	/**
	 * Locates the template using the core WordPress `locate_template()` function.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function locate() {

		$this->template = locate_template( $this->get_hierarchy(), false, false );
	}

	/**
	 * Uses the array of template slugs to build a hierarchy of potential
	 * templates that can be used.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_hierarchy() {

		// Uses the slugs to build a hierarchy.
		foreach ( $this->slugs as $slug ) {

			$templates[] = "{$this->name}/{$slug}.php";
		}

		// Add in a `default.php` template.
		if ( ! in_array( 'default', $this->slugs ) ) {

			$templates[] = "{$this->name}/default.php";
		}

		// Fallback to `{$name}.php` as a last resort.
		$templates[] = "{$this->name}.php";

		// Allow developers to overwrite the hierarchy.
		return apply_filters(
			app()->namespace . "/view_hierarchy_{$this->name}",
			$templates,
			$this
		);
	}

	/**
	 * Sets up data to be passed to the template and renders it.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function render() {

		// Compatibility with core WP's template parts.
		$this->template_part_compat();

		// Locate the template.
		$this->locate();

		if ( $this->template ) {

			// Maybe remove core WP's `prepend_attachment`.
			$this->maybe_shift_attachment();

			// Make `$data` available to the template.
			${ config( 'view' )->name } = $this->data;

			// Extract the data into individual variables if set.
			if ( config( 'view' )->extract && $this->data instanceof Collection ) {

				extract( $this->data->get_items() );
			}

			// Load the template.
			include( $this->template );
		}
	}

	/**
	 * Returns the template output as a string.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function fetch() {

		ob_start();
		$this->render();
		return ob_get_clean();
	}

	/**
	 * Fires the core WP action hooks for template parts.
	 *
	 * Note that WP refers to `$name` and `$slug` differently than we do.
	 * They're the opposite of what we use in our function.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function template_part_compat() {

		// The slug is a string in WP and we have an array. So, we're
		// just going to use the first item of the array in this case.
		$slug = $this->slugs ? reset( $this->slugs ) : null;

		// Compat with `get_header|footer|sidebar()`.
		if ( in_array( $this->name, [ 'header', 'footer', 'sidebar' ] ) ) {

			do_action( "get_{$this->name}", $slug );

		// Compat with `get_template_part()`.
		} else {

			do_action( "get_template_part_{$this->name}", $this->name, $slug );
		}
	}

	/**
	 * Removes core WP's `prepend_attachment` filter whenever a
	 * theme is building custom attachment templates. We'll assume
	 * that the theme author will handle the appropriate output in
	 * the template itself.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function maybe_shift_attachment() {

		if ( ! in_the_loop() || 'attachment' !== get_post_type() ) {
			return;
		}

		if ( in_array( $this->name, [ 'entry', 'post'] ) ) {

			remove_filter( 'the_content', 'prepend_attachment' );

		} elseif ( 'embed' === $this->name ) {

			remove_filter( 'the_content',       'prepend_attachment'          );
			remove_filter( 'the_excerpt_embed', 'wp_embed_excerpt_attachment' );
		}
	}
}
