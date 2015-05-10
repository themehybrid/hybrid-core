<?php

/* Add the layout meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'hybrid_add_post_layout_meta_box', 10, 2 );

/* Saves the post layout on the post editing page. */
add_action( 'save_post',       'hybrid_save_post_layout', 10, 2 );
add_action( 'add_attachment',  'hybrid_save_post_layout'        );
add_action( 'edit_attachment', 'hybrid_save_post_layout'        );

function hybrid_add_post_layout_meta_box( $post_type ) {

	if ( current_theme_supports( 'theme-layouts', 'post_meta' ) && post_type_supports( $post_type, 'theme-layouts' ) && current_user_can( 'edit_theme_options' ) )
		add_meta_box( 'hybrid-post-layout', __( 'Layout', 'hybrid-core' ), 'hybrid_post_layout_meta_box', $post_type, 'side', 'default' );
}

function hybrid_post_layout_meta_box( $post, $box ) {

	/* Get the current post's layout. */
	$post_layout = hybrid_get_post_layout( $post->ID );

	$post_layout = !empty( $post_layout ) ? $post_layout : 'default';

	$choices = array();

	/* Loop through each of the layouts and add it to the choices array with proper key/value pairs. */
	foreach ( hybrid_get_layout_objects() as $layout ) {

		if ( true === $layout->show_in_meta_box )
			$choices[ $layout->name ] = $layout->label;
	}

	wp_nonce_field( basename( __FILE__ ), 'hybrid-post-layout-nonce' ); ?>

	<ul>
		<?php foreach ( $choices as $value => $label ) : ?>
			<li>
				<label>
					<input type="radio" name="hybrid-post-layout" value="<?php echo esc_attr( $value ); ?>" <?php checked( $post_layout, $value ); ?> /> 
					<?php echo esc_html( $label ); ?>
				</label>
			</li>
		<?php endforeach; ?>
	</ul>
<?php }

function hybrid_save_post_layout( $post_id, $post = '' ) {

	/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
	if ( !is_object( $post ) )
		$post = get_post();

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['hybrid-post-layout-nonce'] ) || !wp_verify_nonce( $_POST['hybrid-post-layout-nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the previous post layout. */
	$meta_value = hybrid_get_post_layout( $post_id );

	/* Get the submitted post layout. */
	$new_meta_value = sanitize_key( $_POST['hybrid-post-layout'] );

	/* If there is no new meta value but an old value exists, delete it. */
	if ( '' == $new_meta_value && $meta_value )
		hybrid_delete_post_layout( $post_id );

	/* If a new meta value was added and there was no previous value, add it. */
	elseif ( $meta_value !== $new_meta_value )
		hybrid_set_post_layout( $post_id, $new_meta_value );
}
