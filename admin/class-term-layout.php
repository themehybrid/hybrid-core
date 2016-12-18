<?php
/**
 * Adds a layout selector to the create and edit term admin screen.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Singleton class for handling the term layout feature.
 *
 * @since  3.1.0
 * @access public
 */
final class Hybrid_Admin_Term_Layout {

	/**
	 * Returns the instance.
	 *
	 * @since  3.1.0
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
	 * @since  3.1.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  3.1.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		if ( ! current_theme_supports( 'theme-layouts', 'term_meta' ) )
			return;

		// Load on the edit tags screen.
		add_action( 'load-tags.php',      array( $this, 'load' ) );
		add_action( 'load-edit-tags.php', array( $this, 'load' ) );

		// Update term meta.
		add_action( 'create_term', array( $this, 'save' ) );
		add_action( 'edit_term',   array( $this, 'save' ) );
	}

	/**
	 * Runs on the load hook and sets up what we need.
	 *
	 * @since  3.1.0
	 * @access public
	 * @return void
	 */
	public function load() {

		$screen = get_current_screen();

		// Add the form fields.
		add_action( "{$screen->taxonomy}_add_form_fields",  array( $this, 'add_form_fields'  ) );
		add_action( "{$screen->taxonomy}_edit_form_fields", array( $this, 'edit_form_fields' ) );

		// Enqueue scripts/styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueues scripts/styles.
	 *
	 * @since  3.1.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_style( 'hybrid-admin' );
	}

	/**
	 * Displays the layout selector in the new term form.
	 *
	 * @since  3.1.0
	 * @access public
	 * @return void
	 */
	public function add_form_fields() { ?>

		<div class="form-field hybrid-term-layout-wrap">

			<p class="hybrid-term-layout-header"><?php esc_html_e( 'Layout', 'hybrid-core' ); ?></p>

			<?php $this->display_field(); ?>

		</div>
	<?php }

	/**
	 * Displays the layout selector on the edit term screen.
	 *
	 * @since  3.1.0
	 * @access public
	 * @return void
	 */
	public function edit_form_fields( $term ) { ?>

		<tr class="form-field hybrid-term-layout-wrap">

			<th scope="row"><?php esc_html_e( 'Layout', 'hybrid-core' ); ?></th>

			<td><?php $this->display_field( $term ); ?></td>
		</tr>
	<?php }

	/**
	 * Function for outputting the radio image input fields.
	 *
	 * Note that this will most likely be deprecated in the future in favor of
	 * building an all-purpose field to be used in any form.
	 *
	 * @since  3.1.0
	 * @access public
	 * @param  object  $term
	 * @return void
	 */
	public function display_field( $term = '' ) {

		$term_layout = 'default';
		$taxonomy    = get_current_screen()->taxonomy;

		if ( $term ) {
			$term_layout = hybrid_get_term_layout( $term->term_id );

			$term_layout = $term_layout ? $term_layout : 'default';

			$taxonomy = $term->taxonomy;
		}

		wp_nonce_field( basename( __FILE__ ), 'hybrid_term_layout_nonce' );

		foreach ( hybrid_get_layouts() as $layout ) : ?>

			<?php if ( true === $layout->is_term_layout && $layout->image && ( ! $layout->taxonomies || in_array( $taxonomy, $layout->taxonomies ) ) ) : ?>

				<label class="has-img">
					<input type="radio" value="<?php echo esc_attr( $layout->name ); ?>" name="hybrid-term-layout" <?php checked( $term_layout, $layout->name ); ?> />

					<span class="screen-reader-text"><?php echo esc_html( $layout->label ); ?></span>

					<img src="<?php echo esc_url( hybrid_sprintf_theme_uri( $layout->image ) ); ?>" alt="<?php echo esc_attr( $layout->label ); ?>" />
				</label>

			<?php endif; ?>

		<?php endforeach; ?>

		<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {

			// Add the `.checked` class to whichever radio is checked.
			$( '.hybrid-term-layout-wrap input:checked' ).addClass( 'checked' );

			// When a radio is clicked.
			$( ".hybrid-term-layout-wrap input" ).click( function() {

				// If the radio has the `.checked` class, remove it and uncheck the radio.
				if ( $( this ).hasClass( 'checked' ) ) {

					$( ".hybrid-term-layout-wrap input" ).removeClass( 'checked' );
					$( this ).prop( 'checked', false );

				// If the radio is not checked, add the `.checked` class and check it.
				} else {

					$( ".hybrid-term-layout-wrap input" ).removeClass( 'checked' );
					$( this ).addClass( 'checked' );
				}
			} );
		} );
		</script>
	<?php }

	/**
	 * Saves the term meta.
	 *
	 * @since  3.1.0
	 * @access public
	 * @param  int     $term_id
	 * @return void
	 */
	public function save( $term_id ) {

		if ( ! isset( $_POST['hybrid_term_layout_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['hybrid_term_layout_nonce'] ), basename( __FILE__ ) ) )
			return;

		$old_layout = hybrid_get_term_layout( $term_id );
		$new_layout = isset( $_POST['hybrid-term-layout'] ) ? sanitize_key( wp_unslash( $_POST['hybrid-term-layout'] ) ) : '';

		if ( $old_layout && '' === $new_layout )
			hybrid_delete_term_layout( $term_id );

		else if ( $old_layout !== $new_layout )
			hybrid_set_term_layout( $term_id, $new_layout );
	}
}

// Let's roll.
Hybrid_Admin_Term_Layout::get_instance();
