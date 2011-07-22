<?php
/**
 * Adds the SEO meta box to the post editing screen for public post types.  This feature allows the post author 
 * to set a custom title, description, and keywords for the post, which will be viewed on the singular post page.  
 * To use this feature, the theme must support the 'hybrid-core-seo' feature.  The functions in this file create
 * the SEO meta box and save the settings chosen by the user when the post is saved.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Add the post SEO meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'hybrid_meta_box_post_add_seo' );

/* Save the post SEO meta box data on the 'save_post' hook. */
add_action( 'save_post', 'hybrid_meta_box_post_save_seo', 10, 2 );

/**
 * Adds the post SEO meta box for all public post types.
 *
 * @since 1.2.0
 */
function hybrid_meta_box_post_add_seo() {

	/* Get all available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* Loop through each post type, adding the meta box for each type's post editor screen. */
	foreach ( $post_types as $type )
		add_meta_box( 'hybrid-core-post-seo', sprintf( __( '%s SEO', hybrid_get_textdomain() ), $type->labels->singular_name ), 'hybrid_meta_box_post_display_seo', $type->name, 'normal', 'high' );
}

/**
 * Displays the post SEO meta box.
 *
 * @since 1.2.0
 */
function hybrid_meta_box_post_display_seo( $object, $box ) {

	$domain = hybrid_get_textdomain(); ?>

	<input type="hidden" name="hybrid-core-post-meta-box-seo" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

	<div class="hybrid-post-settings">

	<p>
		<label for="hybrid-document-title"><?php _e( 'Document Title:', $domain ); ?></label>
		<br />
		<input type="text" name="hybrid-document-title" id="hybrid-document-title" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Title', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>

	<p>
		<label for="hybrid-meta-description"><?php _e( 'Meta Description:', $domain ); ?></label>
		<br />
		<textarea name="hybrid-meta-description" id="hybrid-meta-description" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_textarea( get_post_meta( $object->ID, 'Description', true ) ); ?></textarea>
	</p>

	<p>
		<label for="hybrid-meta-keywords"><?php _e( 'Meta Keywords:', $domain ); ?></label>
		<br />
		<input type="text" name="hybrid-meta-keywords" id="hybrid-meta-keywords" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Keywords', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>

	</div><!-- .form-table --><?php
}

/**
 * Saves the post SEO meta box settings as post metadata.
 *
 * @since 1.2.0
 * @param int $post_id The ID of the current post being saved.
 * @param int $post The post object currently being saved.
 */
function hybrid_meta_box_post_save_seo( $post_id, $post ) {

	$prefix = hybrid_get_prefix();

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['hybrid-core-post-meta-box-seo'] ) || !wp_verify_nonce( $_POST['hybrid-core-post-meta-box-seo'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$meta = array(
		'Title' => strip_tags( $_POST['hybrid-document-title'] ),
		'Description' => strip_tags( $_POST['hybrid-meta-description'] ),
		'Keywords' => strip_tags( $_POST['hybrid-meta-keywords'] )
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

?>