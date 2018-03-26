<?php
/**
 * Post layout field class.
 *
 * Adds a layout selector to the create and edit post admin screen.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Admin;

/**
 * Post layout admin class.
 *
 * @since  5.0.0
 * @access public
 */
class PostLayout {

	/**
	 * Sets up initial actions.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if ( ! current_theme_supports( 'theme-layouts', 'post_meta' ) ) {
			return;
		}

		// Load on the edit tags screen.
		add_action( 'add_meta_boxes', [ $this, 'addMetaBoxes' ] );

		// Update post meta.
		add_action( 'save_post',       [ $this, 'save' ], 10, 2 );
		add_action( 'add_attachment',  [ $this, 'save' ]        );
		add_action( 'edit_attachment', [ $this, 'save' ]        );
	}

	/**
	 * Runs on the load hook and sets up what we need.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $post_type
	 * @return void
	 */
	public function addMetaBoxes( $post_type ) {

		if ( post_type_supports( $post_type, 'theme-layouts' ) && current_user_can( 'edit_theme_options' ) ) {

			// Add meta box.
			add_meta_box(
				'hybrid-post-layout',
				esc_html__( 'Layout', 'hybrid-core' ),
				[ $this, 'meta_box' ],
				$post_type,
				'side',
				'default'
			 );

			// Enqueue scripts/styles.
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		}
	}

	/**
	 * Enqueues scripts/styles.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_style( 'hybrid-admin' );

		add_action( 'admin_footer', __NAMESPACE__ . '\layout_field_inline_script' );
	}

	/**
	 * Callback function for displaying the layout meta box.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  object  $object
	 * @param  array   $box
	 * @return void
	 */
	public function meta_box( $post, $box ) {

		// Get only the post layouts.
		$layouts = wp_list_filter( \Hybrid\get_layouts(), [
			'is_post_layout' => true,
			'image'          => true
		] );

		// Remove unwanted layouts.
		foreach ( $layouts as $layout ) {

			if ( $layout->post_types && ! in_array( $post->post_type, $layout->post_types ) ) {
				unset( $layouts[ $layout->name ] );
			}
		}

		// Get the current post's layout.
		$post_layout = \Hybrid\get_post_layout( $post->ID );

		// Output the nonce field.
		wp_nonce_field( basename( __FILE__ ), 'hybrid_post_layout_nonce' );

		// Output the layout field.
		form_field_layout( [
			'layouts'    => $layouts,
			'selected'   => $post_layout ?: 'default',
			'field_name' => 'hybrid-post-layout'
		] );
	}

	/**
	 * Saves the post layout when submitted via the layout meta box.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  int      $post_id
	 * @param  object   $post
	 * @return void
	 */
	public function save( $post_id, $post ) {

		// Verify the nonce for the post formats meta box.
		if ( ! verify_nonce_post( basename( __FILE__ ), 'hybrid_post_layout_nonce' ) ) {
			return;
		}

		// Get the previous post layout.
		$meta_value = \Hybrid\get_post_layout( $post_id );

		// Get the submitted post layout.
		$new_meta_value = isset( $_POST['hybrid-post-layout'] ) ? sanitize_key( $_POST['hybrid-post-layout'] ) : '';

		// If there is no new meta value but an old value exists, delete it.
		if ( '' == $new_meta_value && $meta_value ) {

			\Hybrid\delete_post_layout( $post_id );

		// If a new meta value was added and there was no previous value, add it.
		} elseif ( $meta_value !== $new_meta_value ) {

			\Hybrid\set_post_layout( $post_id, $new_meta_value );
		}
	}
}
