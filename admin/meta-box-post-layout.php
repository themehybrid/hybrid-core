<?php
/**
 * Adds the layout meta box to the post editing screen for post types that support `theme-layouts`.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add the layout meta box on the 'add_meta_boxes' hook.
add_action( 'add_meta_boxes', 'hybrid_add_post_layout_meta_box', 10, 2 );

# Saves the post layout on the post editing page.
add_action( 'save_post',       'hybrid_save_post_layout', 10, 2 );
add_action( 'add_attachment',  'hybrid_save_post_layout'        );
add_action( 'edit_attachment', 'hybrid_save_post_layout'        );

/**
 * Adds the layout meta box.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $post_type
 * @param  object  $post
 * @return void
 */
function hybrid_add_post_layout_meta_box( $post_type ) {

	if ( current_theme_supports( 'theme-layouts', 'post_meta' ) && post_type_supports( $post_type, 'theme-layouts' ) && current_user_can( 'edit_theme_options' ) ) {

		// Add meta box.
		add_meta_box( 'hybrid-post-layout', esc_html__( 'Layout', 'hybrid-core' ), 'hybrid_post_layout_meta_box', $post_type, 'side', 'default' );

		// Load scripts/styles.
		add_action( 'admin_enqueue_scripts', 'hybrid_post_layout_enqueue', 5 );
	}
}

/**
 * Loads the scripts/styles for the layout meta box.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_post_layout_enqueue() {
	wp_enqueue_style( 'hybrid-admin' );
}

/**
 * Callback function for displaying the layout meta box.
 *
 * @since  3.0.0
 * @access public
 * @param  object  $object
 * @param  array   $box
 * @return void
 */
function hybrid_post_layout_meta_box( $post, $box ) {

	// Get the current post's layout.
	$post_layout = hybrid_get_post_layout( $post->ID );

	$post_layout = $post_layout ? $post_layout : 'default';

	wp_nonce_field( basename( __FILE__ ), 'hybrid-post-layout-nonce' ); ?>

	<?php foreach ( hybrid_get_layouts() as $layout ) : ?>

		<?php if ( true === $layout->is_post_layout && $layout->image && ! ( ! empty( $layout->post_types ) && ! in_array( $post->post_type, $layout->post_types ) ) ) : ?>

			<label class="has-img">
				<input type="radio" value="<?php echo esc_attr( $layout->name ); ?>" name="hybrid-post-layout" <?php checked( $post_layout, $layout->name ); ?> />

				<span class="screen-reader-text"><?php echo esc_html( $layout->label ); ?></span>

				<img src="<?php echo esc_url( sprintf( $layout->image, get_template_directory_uri(), get_stylesheet_directory_uri() ) ); ?>" alt="<?php echo esc_attr( $layout->label ); ?>" />
			</label>

		<?php endif; ?>

	<?php endforeach; ?>

	<script type="text/javascript">
	jQuery( document ).ready( function( $ ) {

		// Add the `.checked` class to whichever radio is checked.
		$( '#hybrid-post-layout input:checked' ).addClass( 'checked' );

		// When a radio is clicked.
		$( "#hybrid-post-layout input" ).click( function() {

			// If the radio has the `.checked` class, remove it and uncheck the radio.
			if ( $( this ).hasClass( 'checked' ) ) {

				$( "#hybrid-post-layout input" ).removeClass( 'checked' );
				$( this ).prop( 'checked', false );

			// If the radio is not checked, add the `.checked` class and check it.
			} else {

				$( "#hybrid-post-layout input" ).removeClass( 'checked' );
				$( this ).addClass( 'checked' );
			}
		} );
	} );
	</script>
<?php
}

/**
 * Saves the post layout when submitted via the layout meta box.
 *
 * @since  3.0.0
 * @access public
 * @param  int      $post_id The ID of the current post being saved.
 * @param  object   $post    The post object currently being saved.
 * @return void|int
 */
function hybrid_save_post_layout( $post_id, $post = '' ) {

	// Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963
	if ( ! is_object( $post ) )
		$post = get_post();

	// Verify the nonce for the post formats meta box.
	if ( ! isset( $_POST['hybrid-post-layout-nonce'] ) || ! wp_verify_nonce( $_POST['hybrid-post-layout-nonce'], basename( __FILE__ ) ) )
		return $post_id;

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
