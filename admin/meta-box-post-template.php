<?php

add_action( 'admin_menu', 'hybrid_create_post_meta_box_template' );

function hybrid_create_post_meta_box_template() {

	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	foreach ( $post_types as $type ) {

		if ( 'page' !== $type->name )
			add_meta_box( 'hybrid-core-post-template', sprintf( __( '%s Template', $domain ), $type->labels->singular_name ), 'hybrid_post_meta_box_template', $type->name, 'side', 'default' );
	}

	/* Saves the post meta box data. */
	add_action( 'save_post', 'hybrid_save_post_meta_box_template', 10, 2 );
}

function hybrid_post_meta_box_template( $object, $box ) {

	$post_type_object = get_post_type_object( $object->post_type );

		/* If the post type object returns a singular name or name. */
		if ( !empty( $post_type_object->labels->singular_name ) || !empty( $post_type_object->name ) ) {

			/* Get a list of available custom templates for the post type. */
			$templates = hybrid_get_post_templates( array( 'label' => array( "{$post_type_object->labels->singular_name} Template", "{$post_type_object->name} Template" ) ) );
		}

	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain(); ?>

	<input type="hidden" name="<?php echo "{$prefix}_template_meta_box_nonce"; ?>" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

	<p>
		<?php if ( 0 != count( $templates ) ) { ?>
			<select name="hybrid-post-template" id="hybrid-post-template">
				<option value=""></option>
				<?php foreach ( $templates as $label => $template ) { ?>
					<option value="<?php echo esc_attr( $template ); ?>" <?php selected( esc_attr( get_post_meta( $object->ID, "_wp_{$post_type_object->name}_template", true ) ), esc_attr( $template ) ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<?php _e( 'No templates exist for this post type.', $domain ); ?>
		<?php } ?>
	</p>
<?php
}

function hybrid_save_post_meta_box_template( $post_id, $post ) {

	$prefix = hybrid_get_prefix();

	/* Verify that the post type supports the meta box and the nonce before preceding. */
	if ( !isset( $_POST["{$prefix}_template_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["{$prefix}_template_meta_box_nonce"], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$new_meta_value = strip_tags( $_POST['hybrid-post-template'] );
	$meta_key = "_wp_{$post->post_type}_template";

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