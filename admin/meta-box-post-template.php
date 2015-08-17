<?php
/**
 * Adds the template meta box to the post editing screen for public post types.  This feature allows users and
 * devs to create custom templates for any post type, not just pages as default in WordPress core.  The
 * functions in this file create the template meta box and save the template chosen by the user when the
 * post is saved.  This file is only used if the theme supports the 'hybrid-core-template-hierarchy' feature.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add the post template meta box on the 'add_meta_boxes' hook.
add_action( 'add_meta_boxes', 'hybrid_meta_box_post_add_template',    10, 2 );

# Save the post template meta box data on the 'save_post' hook.
add_action( 'save_post',       'hybrid_meta_box_post_save_template', 10, 2 );
add_action( 'add_attachment',  'hybrid_meta_box_post_save_template'        );
add_action( 'edit_attachment', 'hybrid_meta_box_post_save_template'        );

/**
 * Adds the post template meta box for all public post types, excluding the 'page' post type since WordPress
 * core already handles page templates.
 *
 * @since  1.2.0
 * @access public
 * @param  string  $post_type
 * @param  object  $post
 * @return void
 */
function hybrid_meta_box_post_add_template( $post_type, $post ) {

	// Get the post templates.
	$templates = hybrid_get_post_templates( $post_type );

	// If there's templates, add the meta box.
	if ( ! empty( $templates ) && 'page' !== $post_type )
		add_meta_box( 'hybrid-post-template', esc_html__( 'Template', 'hybrid-core' ), 'hybrid_meta_box_post_display_template', $post_type, 'side', 'default' );
}

/**
 * Displays the post template meta box.
 *
 * @since  1.2.0
 * @access public
 * @param  object  $object
 * @param  array   $box
 * @return void
 */
function hybrid_meta_box_post_display_template( $post, $box ) {

	$templates     = hybrid_get_post_templates( $post->post_type );
	$post_template = hybrid_get_post_template( $post->ID );

	wp_nonce_field( basename( __FILE__ ), 'hybrid-post-template-nonce' ); ?>

	<p>
		<select name="hybrid-post-template" class="widefat">

			<option value=""></option>

			<?php foreach ( $templates as $label => $template ) : ?>
				<option value="<?php echo esc_attr( $template ); ?>" <?php selected( $post_template, $template ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>

		</select>
	</p>
<?php }

/**
 * Saves the post template meta box settings as post metadata. Note that this meta is sanitized using the
 * hybrid_sanitize_meta() callback function prior to being saved.
 *
 * @since  1.2.0
 * @access public
 * @param  int      $post_id The ID of the current post being saved.
 * @param  object   $post    The post object currently being saved.
 * @return void|int
 */
function hybrid_meta_box_post_save_template( $post_id, $post = '' ) {

	// Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963
	if ( ! is_object( $post ) )
		$post = get_post();

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['hybrid-post-template-nonce'] ) || ! wp_verify_nonce( $_POST['hybrid-post-template-nonce'], basename( __FILE__ ) ) )
		return $post_id;

	// Return here if the template is not set. There's a chance it won't be if the post type doesn't have any templates.
	if ( ! isset( $_POST['hybrid-post-template'] ) || ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	// Get the posted meta value.
	$new_meta_value = sanitize_text_field( $_POST['hybrid-post-template'] );

	// Get the meta value of the meta key.
	$meta_value = hybrid_get_post_template( $post_id );

	// If there is no new meta value but an old value exists, delete it.
	if ( '' == $new_meta_value && $meta_value )
		hybrid_delete_post_template( $post_id );

	// If the new meta value does not match the old value, update it.
	elseif ( $new_meta_value != $meta_value )
		hybrid_set_post_template( $post_id, $new_meta_value );
}
