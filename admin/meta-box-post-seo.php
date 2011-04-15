<?php

add_action( 'admin_menu', 'hybrid_create_post_meta_box_seo' );

function hybrid_create_post_meta_box_seo() {

	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	foreach ( $post_types as $type )
		add_meta_box( 'hybrid-core-post-seo', sprintf( __( '%s SEO', $domain ), $type->labels->singular_name ), 'hybrid_post_meta_box_seo', $type->name, 'normal', 'high' );

	/* Saves the post meta box data. */
	add_action( 'save_post', 'hybrid_save_post_meta_box_seo', 10, 2 );
}

function hybrid_post_meta_box_seo( $object, $box ) {

	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain(); ?>

	<input type="hidden" name="<?php echo "{$prefix}_seo_meta_box_nonce"; ?>" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

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

function hybrid_save_post_meta_box_seo( $post_id, $post ) {

	$prefix = hybrid_get_prefix();

	/* Verify that the post type supports the meta box and the nonce before preceding. */
	if ( !isset( $_POST["{$prefix}_seo_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["{$prefix}_seo_meta_box_nonce"], basename( __FILE__ ) ) )
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