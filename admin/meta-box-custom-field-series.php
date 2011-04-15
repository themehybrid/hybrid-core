<?php

add_action( 'admin_menu', 'custom_field_series_create_meta_box' );

function custom_field_series_create_meta_box() {

	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();

	add_meta_box( "custom-field-series", __( 'Series', $domain ), 'custom_field_series_meta_box', 'post', 'side', 'default' );

	/* Saves the post meta box data. */
	add_action( 'save_post', 'custom_field_series_meta_box_save', 10, 2 );
}

function custom_field_series_meta_box( $object, $box ) {

	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain(); ?>

	<input type="hidden" name="<?php echo "custom_field_series_meta_box_nonce"; ?>" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

	<p>
		<input type="text" name="custom-field-series" id="custom-field-series" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Series', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>
<?php
}

function custom_field_series_meta_box_save( $post_id, $post ) {

	/* Verify that the post type supports the meta box and the nonce before preceding. */
	if ( !isset( $_POST["custom_field_series_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["custom_field_series_meta_box_nonce"], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$new_meta_value = strip_tags( $_POST['custom-field-series'] );
	$meta_key = "Series";

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

?>