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

	/* Check if viewing a singular post. */
	if ( is_singular() ) {

		/* Check if the user has set a value for the post stylesheet. */
		$stylesheet = get_post_stylesheet( get_queried_object_id() );

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
 * Returns the post stylesheet if one is saved as post metadata.
 *
 * @since 0.3.0
 * @param int $post_id The ID of the post to get the stylesheet for.
 * @return string|bool Stylesheet name if given.  False for no stylesheet.
 */
function get_post_stylesheet( $post_id ) {
	return get_post_meta( $post_id, apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' ), true );
}

/**
 * Adds/updates the post stylesheet for a specific post.
 *
 * @since 0.3.0
 * @param int $post_id The ID of the post to set the stylesheet for.
 * @param string $stylesheet The filename of the stylesheet.
 */
function set_post_stylesheet( $post_id, $stylesheet ) {
	return update_post_meta( $post_id, apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' ), $stylesheet );
}

/**
 * Checks if a post has a specific post stylesheet.
 *
 * @since 0.3.0
 * @param string $stylesheet The filename of the stylesheet.
 * @param int $post_id The ID of the post to check.
 * @return bool True|False depending on whether the post has the stylesheet.
 */
function has_post_stylesheet( $stylesheet, $post_id = '' ) {

	/* If no post ID is given, use WP's get_the_ID() to get it and assume we're in the post loop. */
	if ( empty( $post_id ) )
		$post_id = get_the_ID();

	/* Return true/false based on whether the stylesheet matches. */
	return ( $stylesheet == get_post_stylesheet( $post_id ) ? true : false );
}

/**
 * Creates the post stylesheets meta box.
 *
 * @since 0.2.0
 */
function post_stylesheets_create_meta_box() {

	/* Get all available 'public' post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* Loop through each of the public post types and add the meta box to it. */
	foreach ( $post_types as $type )
		add_meta_box( "post-stylesheets", sprintf( __( '%s Stylesheet', apply_filters( 'post_stylesheets_textdomain', 'post-stylesheets' ) ), $type->labels->singular_name ), 'post_stylesheets_meta_box', $type->name, 'side', 'default' );

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
		<input type="text" class="widefat" name="post-stylesheets" id="post-stylesheets" value="<?php echo esc_attr( get_post_stylesheet( $object->ID ) ); ?>" />
	</p>
<?php
}

/**
 * Saves the user-selected post stylesheet on the 'save_post' hook.
 *
 * @since 0.2.0
 */
function post_stylesheets_meta_box_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST["post_stylesheets_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["post_stylesheets_meta_box_nonce"], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the previous post stylesheet. */
	$old_stylesheet = get_post_stylesheet( $post_id );

	/* Get the submitted post stylesheet. */
	$new_stylesheet = esc_attr( strip_tags( $_POST['post-stylesheets'] ) );

	/* If the old stylesheet doesn't match the new stylesheet, update the post stylesheet meta. */
	if ( $old_stylesheet !== $new_stylesheet )
		set_post_stylesheet( $post_id, $new_stylesheet );
}

?>