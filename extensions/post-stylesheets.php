<?php
/**
 * Post Stylesheets - A WordPress script for post-specific stylesheets.
 *
 * Post Stylesheets allows users and developers to add unique, per-post stylesheets.  This script was 
 * created so that custom stylesheet files could be dropped into a theme's '/css' folder and loaded for 
 * individual posts using the 'Stylesheet' post meta key and the stylesheet name as the post meta value.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostStylesheets
 * @version 0.2.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2011, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filters stylesheet_uri with a function for adding a new style. */
add_filter( 'stylesheet_uri', 'post_stylesheets_stylesheet_uri', 10, 2 );

/* Create the post stylesheets meta box on the 'admin_menu' hook. */
add_action( 'admin_menu', 'post_stylesheets_create_meta_box' );

/**
 * Checks if a post (or any post type) has the given meta key of 'Stylesheet' when on the singular view of 
 * the post on the front of the site.  If found, the function checks within the '/css' folder of the stylesheet 
 * directory (child theme) and the template directory (parent theme).  If the file exists, it is used rather 
 * than the typical style.css file.
 *
 * @since 0.1.0
 */
function post_stylesheets_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
	global $wp_query;

	/* Check if viewing a singular post. */
	if ( is_singular() ) {

		/* Allow plugin/theme developers to override the default meta key. */
		$meta_key = apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' );

		/* Get the post ID. */
		$post_id = $wp_query->get_queried_object_id();

		/* Check if the user has set a value for the post stylesheet. */
		$stylesheet = get_post_meta( $post_id, $meta_key, true );

		/* If a meta value was given and the file exists, set $stylesheet_uri to the new file. */
		if ( !empty( $stylesheet ) ) {

			/* If the stylesheet is found in the child theme '/css' folder, use it. */
			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . "css/{$stylesheet}" ) )
				$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . "css/{$stylesheet}";

			/* Else, if the stylesheet is found in the parent theme '/css' folder, use it. */
			elseif ( file_exists( trailingslashit( get_template_directory() ) . "css/{$stylesheet}" ) )
				$stylesheet_uri = trailingslashit( get_template_directory_uri() ) . "css/{$stylesheet}";
		}
	}

	/* Return the stylesheet URI. */
	return $stylesheet_uri;
}

/**
 * Creates the post stylesheets meta box.
 *
 * @since 0.2.0
 */
function post_stylesheets_create_meta_box() {

	/* Set up a default textdomain. */
	$textdomain = apply_filters( 'post_stylesheets_textdomain', 'post-stylesheets' );

	/* Get all available 'public' post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* Loop through each of the public post types and add the meta box to it. */
	foreach ( $post_types as $type )
		add_meta_box( "post-stylesheets", sprintf( __( '%s Stylesheet', $textdomain ), $type->labels->singular_name ), 'post_stylesheets_meta_box', $type->name, 'side', 'default' );

	/* Saves the post meta box data. */
	add_action( 'save_post', 'post_stylesheets_meta_box_save', 10, 2 );
}

/**
 * Displays the input field for entering a custom stylesheet.
 *
 * @since 0.2.0
 */
function post_stylesheets_meta_box( $object, $box ) { ?>

	<p>
		<input type="hidden" name="post_stylesheets_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
		<input type="text" name="post-stylesheets" id="post-stylesheets" value="<?php echo esc_attr( get_post_meta( $object->ID, apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' ), true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>
<?php
}

/**
 * Saves the user-selected post stylesheet on the 'save_post' hook.
 *
 * @since 0.2.0
 */
function post_stylesheets_meta_box_save( $post_id, $post ) {

	/* Verify that the post type supports the meta box and the nonce before preceding. */
	if ( !isset( $_POST["post_stylesheets_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["post_stylesheets_meta_box_nonce"], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted stylesheet name and strip any tags from it. */
	$new_meta_value = ( isset( $_POST['post-stylesheets'] ) ? strip_tags( $_POST['post-stylesheets'] ) : '' );

	/* Get the meta key. */
	$meta_key = apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' );

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