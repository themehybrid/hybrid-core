<?php
/**
 * Adds the post style meta box to the edit post screen.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add the post stylesheets meta box on the 'add_meta_boxes' hook.
add_action( 'add_meta_boxes', 'hybrid_add_post_style_meta_box', 10, 2 );

# Saves the post meta box data.
add_action( 'save_post',       'hybrid_save_post_style', 10, 2 );
add_action( 'add_attachment',  'hybrid_save_post_style'        );
add_action( 'edit_attachment', 'hybrid_save_post_style'        );

/**
 * Adds the style meta box.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $post_type
 * @param  object  $post
 * @return void
 */
function hybrid_add_post_style_meta_box( $post_type, $post ) {

	$styles = hybrid_get_post_styles( $post_type );

	if ( ! empty( $styles ) && current_user_can( 'edit_theme_options' ) )
		add_meta_box( 'hybrid-post-style', esc_html__( 'Style', 'hybrid-core' ), 'hybrid_post_style_meta_box', $post_type, 'side', 'default' );
}

/**
 * Callback function for displaying the style meta box.
 *
 * @since  3.0.0
 * @access public
 * @param  object  $object
 * @param  array   $box
 * @return void
 */
function hybrid_post_style_meta_box( $post, $box ) {

	$styles     = hybrid_get_post_styles( $post->post_type );
	$post_style = hybrid_get_post_style( $post->ID );

	wp_nonce_field( basename( __FILE__ ), 'hybrid-post-style-nonce' ); ?>

	<p>
		<select name="hybrid-post-style" class="widefat">

			<option value=""></option>

			<?php foreach ( $styles as $label => $file ) : ?>
				<option value="<?php echo esc_attr( $file ); ?>" <?php selected( $post_style, $file ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>

		</select>
	</p>
<?php }

/**
 * Saves the post style when submitted via the style meta box.
 *
 * @since  3.0.0
 * @access public
 * @param  int      $post_id The ID of the current post being saved.
 * @param  object   $post    The post object currently being saved.
 * @return void|int
 */
function hybrid_save_post_style( $post_id, $post = '' ) {

	// Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963
	if ( ! is_object( $post ) )
		$post = get_post();

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['hybrid-post-style-nonce'] ) || ! wp_verify_nonce( $_POST['hybrid-post-style-nonce'], basename( __FILE__ ) ) )
		return;

	// Get the previous post style.
	$meta_value = hybrid_get_post_style( $post_id );

	// Get the submitted post style.
	$new_meta_value = isset( $_POST['hybrid-post-style'] ) ? sanitize_text_field( $_POST['hybrid-post-style'] ) : '';

	// If there is no new meta value but an old value exists, delete it.
	if ( '' == $new_meta_value && $meta_value )
		hybrid_delete_post_style( $post_id );

	// If a new meta value was added and there was no previous value, add it.
	elseif ( $meta_value !== $new_meta_value )
		hybrid_set_post_style( $post_id, $new_meta_value );
}
