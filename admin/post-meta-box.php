<?php
/**
 * Creates the theme post meta box functionality, which can be extended, changed, or removed through 
 * themes or plugins. The goal is to make it easier for the average end user to update post metadata without 
 * having to understand how custom fields work.  The framework adds some default fields based on which
 * framework features the theme uses.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Add the post meta box creation function to the 'admin_menu' hook. */
add_action( 'admin_menu', 'hybrid_create_post_meta_box' );

/**
 * Creates a meta box on the post (page, other post types) editing screen for allowing the easy input of 
 * commonly-used post metadata.  The function uses the get_post_types() function for grabbing a list of 
 * available post types and adding a new meta box for each post type.
 *
 * @since 0.7.0
 * @uses get_post_types() Gets an array of post type objects.
 * @uses add_meta_box() Adds a meta box to the post editing screen.
 */
function hybrid_create_post_meta_box() {

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type ) {
		if ( post_type_supports( $type->name, "{$prefix}-post-settings" ) ) {

			/* Add the meta box. */
			add_meta_box( "{$prefix}-{$type->name}-meta-box", sprintf( __( '%1$s Settings', $domain ), $type->labels->singular_name ), 'hybrid_post_meta_box', $type->name, 'normal', 'high' );
		}
	}

	/* Saves the post meta box data. */
	add_action( 'save_post', 'hybrid_save_post_meta_box', 10, 2 );
}

/**
 * Creates the settings for the post meta box depending on some things in how the theme are set up.  Most
 * of the available options depend on theme-supported features of the framework.
 *
 * @since 0.7.0
 * @param string $type The post type of the current post in the post editor.
 */
function hybrid_post_meta_box_args( $type = '' ) {

	/* Set up some default variables. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$meta = array();

	/* If no post type is given, default to 'post'. */
	if ( empty( $type ) )
		$type = 'post';

	/* If the current theme supports the 'hybrid-core-seo' feature. */
	if ( current_theme_supports( 'hybrid-core-seo' ) ) {
		$meta['title'] = array( 'name' => 'Title', 'title' => sprintf( __( 'Document Title: %s', $domain ), '<code>&lt;title></code>' ), 'type' => 'text' );
		$meta['description'] = array( 'name' => 'Description', 'title' => sprintf( __( 'Meta Description: %s', $domain ), '<code>&lt;meta></code>' ), 'type' => 'textarea' );
		$meta['keywords'] = array( 'name' => 'Keywords', 'title' => sprintf( __( 'Meta Keywords: %s', $domain ), '<code>&lt;meta></code>' ), 'type' => 'text' );
	}

	/* If the current theme supports the 'custom-field-series' extension. */
	if ( current_theme_supports( 'custom-field-series' ) )
		$meta['series'] = array( 'name' => 'Series', 'title' => __( 'Series:', $domain ), 'type' => 'text' );

	/* If the current theme supports the 'get-the-image' extension. */
	if ( current_theme_supports( 'get-the-image' ) )
		$meta['thumbnail'] = array( 'name' => 'Thumbnail', 'title' => __( 'Thumbnail:', $domain ), 'type' => 'text' );

	/* If the current theme supports the 'post-stylesheets' extension. */
	if ( current_theme_supports( 'post-stylesheets' ) )
		$meta['stylesheet'] = array( 'name' => 'Stylesheet', 'title' => __( 'Stylesheet:', $domain ), 'type' => 'text' );

	/* If the current theme supports the 'hybrid-core-template-hierarchy' and is not a page or attachment. */
	if ( current_theme_supports( 'hybrid-core-template-hierarchy' ) && 'page' != $type && 'attachment' != $type ) {

		/* Get the post type object. */
		$post_type_object = get_post_type_object( $type );

		/* If the post type object returns a singular name or name. */
		if ( !empty( $post_type_object->labels->singular_name ) || !empty( $post_type_object->name ) ) {

			/* Get a list of available custom templates for the post type. */
			$templates = hybrid_get_post_templates( array( 'label' => array( "{$post_type_object->labels->singular_name} Template", "{$post_type_object->name} Template" ) ) );

			/* If templates found, allow user to select one. */
			if ( 0 != count( $templates ) )
				$meta['template'] = array( 'name' => "_wp_{$type}_template", 'title' => __( 'Template:', $domain ), 'type' => 'select', 'options' => $templates, 'use_key_and_value' => true );
		}
	}

	/* $prefix_$type_meta_boxes filter is deprecated. Use $prefix_$type_meta_box_args instead. */
	$meta = apply_filters( "{$prefix}_{$type}_meta_boxes", $meta );

	/* Allow per-post_type filtering of the meta box arguments. */
	return apply_filters( "{$prefix}_{$type}_meta_box_args", $meta );
}

/**
 * Displays the post meta box on the edit post page. The function gets the various metadata elements
 * from the hybrid_post_meta_box_args() function. It then loops through each item in the array and
 * displays a form element based on the type of setting it should be.
 *
 * @since 0.7.0
 * @parameter object $object Post object that holds all the post information.
 * @parameter array $box The particular meta box being shown and its information.
 */
function hybrid_post_meta_box( $object, $box ) {

	$prefix = hybrid_get_prefix();

	$meta_box_options = hybrid_post_meta_box_args( $object->post_type ); ?>

	<input type="hidden" name="<?php echo "{$prefix}_{$object->post_type}_meta_box_nonce"; ?>" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

	<div class="hybrid-post-settings">

		<?php foreach ( $meta_box_options as $option ) {
			if ( function_exists( "hybrid_post_meta_box_{$option['type']}" ) )
				call_user_func( "hybrid_post_meta_box_{$option['type']}", $option, get_post_meta( $object->ID, $option['name'], true ) );
		} ?>

	</div><!-- .form-table --><?php
}

/**
 * Outputs a text input box with the given arguments for use with the post meta box.
 *
 * @since 0.7.0
 * @param array $args 
 * @param string|bool $value Custom field value.
 */
function hybrid_post_meta_box_text( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<label for="<?php echo $name; ?>"><?php echo $args['title']; ?></label>
		<br />
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" size="30" tabindex="30" style="width: 99%;" />
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * Outputs a select box with the given arguments for use with the post meta box.
 *
 * @since 0.7.0
 * @param array $args
 * @param string|bool $value Custom field value.
 */
function hybrid_post_meta_box_select( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<label for="<?php echo $name; ?>"><?php echo $args['title']; ?></label>
		<br />
		<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<option value=""></option>
			<?php foreach ( $args['options'] as $option => $val ) { ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( esc_attr( $value ), esc_attr( $val ) ); ?>><?php echo ( !empty( $args['use_key_and_value'] ) ? $option : $val ); ?></option>
			<?php } ?>
		</select>
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * Outputs a textarea with the given arguments for use with the post meta box.
 *
 * @since 0.7.0
 * @param array $args
 * @param string|bool $value Custom field value.
 */
function hybrid_post_meta_box_textarea( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<label for="<?php echo $name; ?>"><?php echo $args['title']; ?></label>
		<br />
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_html( $value ); ?></textarea>
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * Outputs radio inputs with the given arguments for use with the post meta box.
 *
 * @since 0.8.0
 * @param array $args
 * @param string|bool $value Custom field value.
 */
function hybrid_post_meta_box_radio( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<?php echo $args['title']; ?>
		<?php foreach ( $args['options'] as $option => $val ) { ?>
			<br />
			<input type="radio" name="<?php echo $name; ?>" value="<?php echo esc_attr( $val ); ?> <?php checked( esc_attr( $value ), esc_attr( $val ) ); ?> />
		<?php } ?>
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * The function for saving the theme's post meta box settings. It loops through each of the meta box 
 * arguments for that particular post type and adds, updates, or deletes the metadata.
 *
 * @since 0.7.0
 * @param int $post_id
 */
function hybrid_save_post_meta_box( $post_id, $post ) {

	$prefix = hybrid_get_prefix();

	/* Verify that the post type supports the meta box and the nonce before preceding. */
	if ( !post_type_supports( $post->post_type, "{$prefix}-post-settings" ) || !isset( $_POST["{$prefix}_{$post->post_type}_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["{$prefix}_{$post->post_type}_meta_box_nonce"], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the post meta box arguments. */
	$metadata = hybrid_post_meta_box_args( $_POST['post_type'] );

	/* Loop through all of post meta box arguments. */
	foreach ( $metadata as $meta ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta['name'], true );

		/* Get the meta value the user input. */
		$new_meta_value = stripslashes( $_POST[ preg_replace( "/[^A-Za-z_-]/", '-', $meta['name'] ) ] );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta['name'], $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta['name'], $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta['name'], $meta_value );
	}
}

?>