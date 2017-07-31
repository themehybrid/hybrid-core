<?php
/**
 * Adds a layout selector to the create and edit post admin screen.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Singleton class for handling the post layout feature.
 *
 * @since  4.0.0
 * @access public
 */
final class Hybrid_Admin_Post_Layout {

	/**
	 * Returns the instance.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		if ( ! current_theme_supports( 'theme-layouts', 'post_meta' ) )
			return;

		// Load on the edit tags screen.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Update post meta.
		add_action( 'save_post',       array( $this, 'save' ), 10, 2 );
		add_action( 'add_attachment',  array( $this, 'save' )        );
		add_action( 'edit_attachment', array( $this, 'save' )        );
	}

	/**
	 * Runs on the load hook and sets up what we need.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  string  $post_type
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {

		if ( post_type_supports( $post_type, 'theme-layouts' ) && current_user_can( 'edit_theme_options' ) ) {

			// Add meta box.
			add_meta_box( 'hybrid-post-layout', esc_html__( 'Layout', 'hybrid-core' ), array( $this, 'meta_box' ), $post_type, 'side', 'default' );

			// Enqueue scripts/styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	/**
	 * Enqueues scripts/styles.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_style( 'hybrid-admin' );

		add_action( 'admin_footer', 'hybrid_layout_field_inline_script' );
	}

	/**
	 * Callback function for displaying the layout meta box.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  object  $object
	 * @param  array   $box
	 * @return void
	 */
	public function meta_box( $post, $box ) {

		// Get only the post layouts.
		$layouts = wp_list_filter( hybrid_get_layouts(), array( 'is_post_layout' => true, 'image' => true ) );

		// Remove unwanted layouts.
		foreach ( $layouts as $layout ) {

			if ( $layout->post_types && ! in_array( $post->post_type, $layout->post_types ) )
				unset( $layouts[ $layout->name ] );
		}

		// Get the current post's layout.
		$post_layout = hybrid_get_post_layout( $post->ID );

		// Output the nonce field.
		wp_nonce_field( basename( __FILE__ ), 'hybrid_post_layout_nonce' );

		// Output the layout field.
		hybrid_form_field_layout(
			array(
				'layouts'    => $layouts,
				'selected'   => $post_layout ? $post_layout : 'default',
				'field_name' => 'hybrid-post-layout'
			)
		);
	}

	/**
	 * Saves the post layout when submitted via the layout meta box.
	 *
	 * @since  4.0.0
	 * @access public
	 * @param  int      $post_id The ID of the current post being saved.
	 * @param  object   $post    The post object currently being saved.
	 * @return void
	 */
	public function save( $post_id, $post = '' ) {

		// Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963
		if ( ! is_object( $post ) )
			$post = get_post();

		// Verify the nonce for the post formats meta box.
		if ( ! hybrid_verify_nonce_post( basename( __FILE__ ), 'hybrid_post_layout_nonce' ) )
			return;

		// Get the previous post layout.
		$meta_value = hybrid_get_post_layout( $post_id );

		// Get the submitted post layout.
		$new_meta_value = isset( $_POST['hybrid-post-layout'] ) ? sanitize_key( $_POST['hybrid-post-layout'] ) : '';

		// If there is no new meta value but an old value exists, delete it.
		if ( '' == $new_meta_value && $meta_value )
			hybrid_delete_post_layout( $post_id );

		// If a new meta value was added and there was no previous value, add it.
		elseif ( $meta_value !== $new_meta_value )
			hybrid_set_post_layout( $post_id, $new_meta_value );
	}
}

// Let's roll.
Hybrid_Admin_Post_Layout::get_instance();
