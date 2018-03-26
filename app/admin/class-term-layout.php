<?php
/**
 * Term layout field class.
 *
 * Adds a layout selector to the create and edit term admin screen.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Admin;

/**
 * Class for handling the term layout feature.
 *
 * @since  5.0.0
 * @access public
 */
 class TermLayout {

	/**
	 * Sets up initial actions.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if ( ! current_theme_supports( 'theme-layouts', 'term_meta' ) ) {
			return;
		}

		// Load on the edit tags screen.
		add_action( 'load-tags.php',      [ $this, 'load' ] );
		add_action( 'load-edit-tags.php', [ $this, 'load' ] );

		// Update term meta.
		add_action( 'create_term', [ $this, 'save' ] );
		add_action( 'edit_term',   [ $this, 'save' ] );
	}

	/**
	 * Runs on the load hook and sets up what we need.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$screen = get_current_screen();

		// Add the form fields.
		add_action( "{$screen->taxonomy}_add_form_fields",  [ $this, 'addFormFields'  ] );
		add_action( "{$screen->taxonomy}_edit_form_fields", [ $this, 'editFormFields' ] );

		// Enqueue scripts/styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
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
	 * Displays the layout selector in the new term form.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function addFormFields() { ?>

		<div class="form-field hybrid-term-layout-wrap">

			<p class="hybrid-term-layout-header"><?php esc_html_e( 'Layout', 'hybrid-core' ); ?></p>

			<?php $this->display_field(); ?>

		</div>
	<?php }

	/**
	 * Displays the layout selector on the edit term screen.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function editFormFields( $term ) { ?>

		<tr class="form-field hybrid-term-layout-wrap">

			<th scope="row"><?php esc_html_e( 'Layout', 'hybrid-core' ); ?></th>

			<td><?php $this->display_field( $term ); ?></td>
		</tr>
	<?php }

	/**
	 * Function for outputting the radio image input fields.
	 *
	 * Note that this will most likely be deprecated in the future in favor
	 * of building an all-purpose field to be used in any form.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  object  $term
	 * @return void
	 */
	public function display_field( $term = '' ) {

		$term_layout = 'default';
		$taxonomy    = get_current_screen()->taxonomy;

		// Get only the term layouts.
		$layouts = wp_list_filter( \Hybrid\get_layouts(), [
			'is_term_layout' => true,
			'image'          => true
		] );

		// Remove unwanted layouts.
		foreach ( $layouts as $layout ) {

			if ( $layout->taxonomies && ! in_array( $taxonomy, $layout->taxonomies ) ) {
				unset( $layouts[ $layout->name ] );
			}
		}

		// If we have a term, get its layout.
		if ( $term ) {
			$term_layout = \Hybrid\get_term_layout( $term->term_id );
		}

		// Output the nonce field.
		wp_nonce_field( basename( __FILE__ ), 'hybrid_term_layout_nonce' );

		// Output the layout field.
		hybrid_form_field_layout( [
			'layouts'    => $layouts,
			'selected'   => $term_layout ? $term_layout : 'default',
			'field_name' => 'hybrid-term-layout'
		] );
	}

	/**
	 * Saves the term meta.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  int     $term_id
	 * @return void
	 */
	public function save( $term_id ) {

		if ( ! verify_nonce_post( basename( __FILE__ ), 'hybrid_term_layout_nonce' ) ) {
			return;
		}

		$old_layout = \Hybrid\get_term_layout( $term_id );
		$new_layout = isset( $_POST['hybrid-term-layout'] ) ? sanitize_key( $_POST['hybrid-term-layout'] ) : '';

		if ( $old_layout && '' === $new_layout ) {

			\Hybrid\delete_term_layout( $term_id );

		} elseif ( $old_layout !== $new_layout ) {

			\Hybrid\set_term_layout( $term_id, $new_layout );
		}
	}
}
