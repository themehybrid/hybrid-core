<?php
/**
 * Custom Field Series - A script for creating a series of posts by custom field.
 *
 * Custom Field Series was created to allow users to add individual posts to a larger series of posts.  It was 
 * created before WordPress made it easy for developers to create new taxonomies.  Ideally, one would use a 
 * taxonomy to handle this functionality.  However, this method is lighter, provides an extremely simple 
 * method for adding posts to a series, and offers backwards compatibility for people that have used this 
 * method before.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CustomFieldSeries
 * @version 0.4.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2007 - 2012, Justin Tadlock
 * @link http://justintadlock.com/archives/2007/11/01/wordpress-custom-fields-listing-a-series-of-posts
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add support for the 'custom-field-series' extension to posts. */
add_action( 'init', 'custom_field_series_post_type_support' );

/* Register metadata for the custom field series. */
add_action( 'init', 'custom_field_series_register_meta' );

/* Create the meta box on the 'admin_menu' hook. */
add_action( 'admin_menu', 'custom_field_series_admin_setup' );

/**
 * Checks for a series of posts by the current post's metadata.  The function grabs the meta value for the 
 * 'Series' meta key and checks if any posts have been given the same value.  If posts are found with this 
 * meta key/value pair, the function adds them to an unordered list.
 *
 * @since 0.1.0
 * @access public
 * @param array $args Array of arguments.
 */
function custom_field_series( $args = array() ) {

	/* Set $series to an empty string. */
	$series = '';

	/* Get the current post ID. */
	$post_id = get_the_ID();

	/* Get the series meta value for the post. */
	$meta_value = get_post_meta( $post_id, custom_field_series_meta_key(), true );

	/* If a meta value was found, create a list of posts in the series. */
	if ( !empty( $meta_value ) ) {

		/* Set up the default post query arguments. */
		$defaults = array(
			'order' => 	'DESC',
			'orderby' => 	'ID',
			'include' => 	'',
			'exclude' => 	'',
			'post_type' => 	'any',
			'numberposts' => 	-1,
			'meta_key' => 	custom_field_series_meta_key(),
			'meta_value' => 	$meta_value,
			'echo' => 	true
		);

		/* Allow developers to override the arguments used. */
		$args = apply_filters( 'custom_field_series_args', $args );
		$args = wp_parse_args( $args, $defaults );

		/* Get all posts in the current series. */
		$series_posts = get_posts( $args );

		/* If posts were found, display them. */
		if ( !empty( $series_posts ) ) {

			/* Format the series class with the name of the series. */
			$class = sanitize_html_class( sanitize_title_with_dashes( $meta_value ) );

			/* Create the opening wrapper div, title, and list element. */
			$series = '<div class="series series-' . esc_attr( $class ) . '">';
			$series .= '<h4 class="series-title">' . apply_filters( 'custom_field_series_title', __( 'Articles in this series', 'custom-field-series' ) ) . '</h4>';
			$series .= '<ul>';

			/* Loop through the posts. */
			foreach ( $series_posts as $serial ) {

				/* If the current post in the loop matches the post we're viewing, don't link to it. */
				if ( $serial->ID == $post_id )
					$series .= '<li class="current-post">' . $serial->post_title . '</li>';

				/* Display a link to the post. */
				else
					$series .= '<li><a href="' . get_permalink( $serial->ID ) . '" title="' . esc_attr( $serial->post_title ) . '">' . $serial->post_title . '</a></li>';
			}

			/* Close the unordered list and wrapper div. */
			$series .= '</ul></div>';
		}
	}

	/* Allow developers to overwrite the HTML of the series. */
	$series = apply_filters( 'custom_field_series', $series );

	/* If $echo is set to true, display the series. */
	if ( !empty( $args['echo'] ) )
		echo $series;

	/* If $echo is not set to true, return the series. */
	else
		return $series;
}

/**
 * Adds post type support of 'custom-field-series' to the 'post' post type.  Developers should register support 
 * for additional post types.
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function custom_field_series_post_type_support() {
	add_post_type_support( 'post', 'custom-field-series' );
}

/**
 * Registers the custom field series meta key 'Series' for for specific object types and provides a 
 * function to sanitize the metadata on update.
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function custom_field_series_register_meta() {
	register_meta( 'post', custom_field_series_meta_key(), 'custom_field_series_sanitize_meta' );
}

/**
 * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress. 
 * If a developer wants to set up a custom method for sanitizing the data, they should use the 
 * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
 *
 * @since 0.4.0
 * @param mixed $meta_value The value of the data to sanitize.
 * @param string $meta_key The meta key name.
 * @param string $meta_type The type of metadata (post, comment, user, etc.)
 * @return mixed $meta_value
 */
function custom_field_series_sanitize_meta( $meta_value, $meta_key, $meta_type ) {
	return strip_tags( $meta_value );
}

/**
 * Returns the meta key used for the 'Series' custom field so that developers can overwrite the key if they
 * need to for their project.
 *
 * @since 0.4.0
 * @access public
 * @return string The meta key used for the series metadata.
 */
function custom_field_series_meta_key() {
	return apply_filters( 'custom_field_series_meta_key', 'Series' );
}

/**
 * Admin setup for the custom field series script.
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function custom_field_series_admin_setup() {

	/* Load the post meta boxes on the new post and edit post screens. */
	add_action( 'load-post.php', 'custom_field_series_load_meta_boxes' );
	add_action( 'load-post-new.php', 'custom_field_series_load_meta_boxes' );
}

/**
 * Hooks into the 'add_meta_boxes' hook to add the custom field series meta box and the 'save_post' hook 
 * to save the metadata.
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function custom_field_series_load_meta_boxes() {

	/* Add the custom field series meta box on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'custom_field_series_create_meta_box', 10, 2 );

	/* Saves the post meta box data. */
	add_action( 'save_post', 'custom_field_series_meta_box_save', 10, 2 );
}

/**
 * Creates the meta box on the post editing screen for the 'post' post type.
 *
 * @since 0.3.0
 * @access private
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post object.
 * @return void
 */
function custom_field_series_create_meta_box( $post_type, $post ) {

	if ( post_type_supports( $post_type, 'custom-field-series' ) )
		add_meta_box( 'custom-field-series', __( 'Series', 'custom-field-series' ), 'custom_field_series_meta_box', $post_type, 'side', 'default' );
}

/**
 * Displays the input field with the meta box.
 *
 * @since 0.3.0
 * @access private
 * @param object $object The post object currently being edited.
 * @param array $box Specific information about the meta box being loaded.
 * @return void
 */
function custom_field_series_meta_box( $object, $box ) { ?>

	<p>
		<?php wp_nonce_field( basename( __FILE__ ), 'custom-field-series-nonce' ); ?>
		<input type="text" name="custom-field-series" id="custom-field-series" value="<?php echo esc_attr( get_post_meta( $object->ID, custom_field_series_meta_key(), true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>
<?php
}

/**
 * Saves the single value for the 'Series' meta key, which was set using the custom field series meta box.
 *
 * @since 0.3.0
 * @access private
 * @param int $post_id The ID of the current post being saved.
 * @param object $post The post object currently being saved.
 * @return void
 */
function custom_field_series_meta_box_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['custom-field-series-nonce'] ) || !wp_verify_nonce( $_POST['custom-field-series-nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the posted series title and strip all tags from it. */
	$new_meta_value = $_POST['custom-field-series'];

	/* Get the meta key. */
	$meta_key = custom_field_series_meta_key();

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If there is no new meta value but an old value exists, delete it. */
	if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );

	/* If a new meta value was added and there was no previous value, add it. */
	elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the old layout doesn't match the new layout, update the post layout meta. */
	elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $meta_value !== $new_meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );
}

?>