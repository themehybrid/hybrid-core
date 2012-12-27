<?php
/**
 * Get the Image - An advanced post image script for WordPress.
 *
 * Get the Image was created to be a highly-intuitive image script that displays post-specific images (an 
 * image-based representation of a post).  The script handles old-style post images via custom fields for 
 * backwards compatibility.  It also supports WordPress' built-in featured image functionality.  On top of 
 * those things, it can automatically set attachment images as the post image or scan the post content for 
 * the first image element used.  It can also fall back to a given default image.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   GetTheImage
 * @version   0.8.1
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link      http://justintadlock.com/archives/2008/05/27/get-the-image-wordpress-plugin
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Adds theme support for WordPress 'featured images'. */
add_theme_support( 'post-thumbnails' );

/* Delete the cache when a post or post metadata is updated. */
add_action( 'save_post', 'get_the_image_delete_cache_by_post' );
add_action( 'deleted_post_meta', 'get_the_image_delete_cache_by_meta', 10, 2 );
add_action( 'updated_post_meta', 'get_the_image_delete_cache_by_meta', 10, 2 );
add_action( 'added_post_meta', 'get_the_image_delete_cache_by_meta', 10, 2 );

/**
 * The main image function for displaying an image.  It supports several arguments that allow developers to
 * customize how the script outputs the image.
 *
 * The image check order is important to note here.  If an image is found by any specific check, the script
 * will no longer look for images.  The check order is 'meta_key', 'the_post_thumbnail', 'attachment', 
 * 'image_scan', 'callback', and 'default_image'.
 *
 * @since 0.1.0
 * @access public
 * @global $post The current post's database object.
 * @param array $args Arguments for how to load and display the image.
 * @return string|array The HTML for the image. | Image attributes in an array.
 */
function get_the_image( $args = array() ) {

	/* Set the default arguments. */
	$defaults = array(
		'meta_key'           => array( 'Thumbnail', 'thumbnail' ), // array|string
		'post_id'            => get_the_ID(),
		'attachment'         => true,
		'the_post_thumbnail' => true, // WP 2.9+ image function
		'size'               => 'thumbnail',
		'default_image'      => false,
		'order_of_image'     => 1,
		'link_to_post'       => true,
		'image_class'        => false,
		'image_scan'         => false,
		'width'              => false,
		'height'             => false,
		'format'             => 'img',
		'meta_key_save'      => false,
		'thumbnail_id_save'  => false, // Set 'featured image'.
		'callback'           => null,
		'cache'              => true,
		'before'             => '',
		'after'              => '',
		'echo'               => true,
		'custom_key'         => null, // @deprecated 0.6. Use 'meta_key'.
		'default_size'       => null, // @deprecated 0.5.  Use 'size'.
	);

	/* Allow plugins/themes to filter the arguments. */
	$args = apply_filters( 'get_the_image_args', $args );

	/* Merge the input arguments and the defaults. */
	$args = wp_parse_args( $args, $defaults );

	/* If $default_size is given, overwrite $size. */
	if ( !is_null( $args['default_size'] ) )
		$args['size'] = $args['default_size']; // Deprecated 0.5 in favor of $size

	/* If $custom_key is set, overwrite $meta_key. */
	if ( !is_null( $args['custom_key'] ) )
		$args['meta_key'] = $args['custom_key']; // Deprecated 0.6 in favor of $meta_key

	/* If $format is set to 'array', don't link to the post. */
	if ( 'array' == $args['format'] )
		$args['link_to_post'] = false;

	/* Extract the array to allow easy use of variables. */
	extract( $args );

	/* Get cache key based on $args. */
	$key = md5( serialize( compact( array_keys( $args ) ) ) );

	/* Check for a cached image. */
	$image_cache = wp_cache_get( $post_id, 'get_the_image' );

	if ( !is_array( $image_cache ) )
		$image_cache = array();

	/* Set up a default, empty $image_html variable. */
	$image_html = '';

	/* If there is no cached image, let's see if one exists. */
	if ( !isset( $image_cache[ $key ] ) || empty( $cache ) ) {

		/* If a custom field key (array) is defined, check for images by custom field. */
		if ( !empty( $meta_key ) )
			$image = get_the_image_by_meta_key( $args );

		/* If no image found and $the_post_thumbnail is set to true, check for a post image (WP feature). */
		if ( empty( $image ) && !empty( $the_post_thumbnail ) )
			$image = get_the_image_by_post_thumbnail( $args );

		/* If no image found and $attachment is set to true, check for an image by attachment. */
		if ( empty( $image ) && !empty( $attachment ) )
			$image = get_the_image_by_attachment( $args );

		/* If no image found and $image_scan is set to true, scan the post for images. */
		if ( empty( $image ) && !empty( $image_scan ) )
			$image = get_the_image_by_scan( $args );

		/* If no image found and a callback function was given. Callback function must pass back array of <img> attributes. */
		if ( empty( $image ) && !is_null( $callback ) && function_exists( $callback ) )
			$image = call_user_func( $callback, $args );

		/* If no image found and a $default_image is set, get the default image. */
		if ( empty( $image ) && !empty( $default_image ) )
			$image = get_the_image_by_default( $args );

		/* If an image was found. */
		if ( !empty( $image ) ) {

			/* If $meta_key_save was set, save the image to a custom field. */
			if ( !empty( $meta_key_save ) )
				get_the_image_meta_key_save( $args, $image );

			/* Format the image HTML. */
			$image_html = get_the_image_format( $args, $image );

			/* Set the image cache for the specific post. */
			$image_cache[ $key ] = $image_html;
			wp_cache_set( $post_id, $image_cache, 'get_the_image' );
		}
	}

	/* If an image was already cached for the post and arguments, use it. */
	else {
		$image_html = $image_cache[$key];
	}

	/* Allow plugins/theme to override the final output. */
	$image_html = apply_filters( 'get_the_image', $image_html );

	/* If $format is set to 'array', return an array of image attributes. */
	if ( 'array' == $format ) {

		/* Set up a default empty array. */
		$out = array();

		/* Get the image attributes. */
		$atts = wp_kses_hair( $image_html, array( 'http' ) );

		/* Loop through the image attributes and add them in key/value pairs for the return array. */
		foreach ( $atts as $att )
			$out[ $att['name'] ] = $att['value'];

		$out['url'] = $out['src']; // @deprecated 0.5 Use 'src' instead of 'url'.

		/* Return the array of attributes. */
		return $out;
	}

	/* Or, if $echo is set to false, return the formatted image. */
	elseif ( false === $echo ) {
		return !empty( $image_html ) ? $args['before'] . $image_html . $args['after'] : $image_html;
	}

	/* If there is a $post_thumbnail_id, do the actions associated with get_the_post_thumbnail(). */
	if ( isset( $image['post_thumbnail_id'] ) )
		do_action( 'begin_fetch_post_thumbnail_html', $post_id, $image['post_thumbnail_id'], $size );

	/* Display the image if we get to this point. */
	echo !empty( $image_html ) ? $args['before'] . $image_html . $args['after'] : $image_html;

	/* If there is a $post_thumbnail_id, do the actions associated with get_the_post_thumbnail(). */
	if ( isset( $image['post_thumbnail_id'] ) )
		do_action( 'end_fetch_post_thumbnail_html', $post_id, $image['post_thumbnail_id'], $size );
}

/* Internal Functions */

/**
 * Calls images by custom field key.  Script loops through multiple custom field keys.  If that particular 
 * key is found, $image is set and the loop breaks.  If an image is found, it is returned.
 *
 * @since 0.7.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function get_the_image_by_meta_key( $args = array() ) {

	/* If $meta_key is not an array. */
	if ( !is_array( $args['meta_key'] ) )
		$args['meta_key'] = array( $args['meta_key'] );

	/* Loop through each of the given meta keys. */
	foreach ( $args['meta_key'] as $meta_key ) {

		/* Get the image URL by the current meta key in the loop. */
		$image = get_post_meta( $args['post_id'], $meta_key, true );

		/* If an image was found, break out of the loop. */
		if ( !empty( $image ) )
			break;
	}

	/* If a custom key value has been given for one of the keys, return the image URL. */
	if ( !empty( $image ) )
		return array( 'src' => $image );

	return false;
}

/**
 * Checks for images using a custom version of the WordPress 2.9+ get_the_post_thumbnail() function.  
 * If an image is found, return it and the $post_thumbnail_id.  The WordPress function's other filters are 
 * later added in the display_the_image() function.
 *
 * @since 0.7.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function get_the_image_by_post_thumbnail( $args = array() ) {

	/* Check for a post image ID (set by WP as a custom field). */
	$post_thumbnail_id = get_post_thumbnail_id( $args['post_id'] );

	/* If no post image ID is found, return false. */
	if ( empty( $post_thumbnail_id ) )
		return false;

	/* Apply filters on post_thumbnail_size because this is a default WP filter used with its image feature. */
	$size = apply_filters( 'post_thumbnail_size', $args['size'] );

	/* Get the attachment image source.  This should return an array. */
	$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );

	/* Get the attachment excerpt to use as alt text. */
	$alt = trim( strip_tags( get_post_field( 'post_excerpt', $post_thumbnail_id ) ) );

	/* Return both the image URL and the post thumbnail ID. */
	return array( 'src' => $image[0], 'post_thumbnail_id' => $post_thumbnail_id, 'alt' => $alt );
}

/**
 * Check for attachment images.  Uses get_children() to check if the post has images attached.  If image 
 * attachments are found, loop through each.  The loop only breaks once $order_of_image is reached.
 *
 * @since 0.7.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function get_the_image_by_attachment( $args = array() ) {

	/* Get the post type of the current post. */
	$post_type = get_post_type( $args['post_id'] );

	/* Check if the post itself is an image attachment. */
	if ( 'attachment' == $post_type && wp_attachment_is_image( $args['post_id'] ) ) {
		$attachment_id = $args['post_id'];
	}

	/* If the post is not an attachment, check if it has any image attachments. */
	elseif ( 'attachment' !== $post_type ) {

		/* Get attachments for the inputted $post_id. */
		$attachments = get_children(
			array(
				'post_parent'      => $args['post_id'],
				'post_status'      => 'inherit',
				'post_type'        => 'attachment',
				'post_mime_type'   => 'image',
				'order'            => 'ASC',
				'orderby'          => 'menu_order ID',
				'suppress_filters' => true
			)
		);

		/* Check if any attachments were found. */
		if ( !empty( $attachments ) ) {

			/* Set the default iterator to 0. */
			$i = 0;

			/* Loop through each attachment. */
			foreach ( $attachments as $id => $attachment ) {

				/* Set the attachment ID as the current ID in the loop. */
				$attachment_id = $id;

				/* Break if/when we hit 'order_of_image'. */
				if ( ++$i == $args['order_of_image'] )
					break;
			}
		}
	}

	/* Check if we have an attachment ID before proceeding. */
	if ( !empty( $attachment_id ) ) {

		/* Get the attachment image. */
		$image = wp_get_attachment_image_src( $attachment_id, $args['size'] );

		/* Get the attachment excerpt. */
		$alt = trim( strip_tags( get_post_field( 'post_excerpt', $attachment_id ) ) );

		/* Save the attachment as the 'featured image'. */
		if ( true === $args['thumbnail_id_save'] )
			set_post_thumbnail( $args['post_id'], $attachment_id );

		/* Return the image URL. */
		return array( 'src' => $image[0], 'alt' => $alt );
	}

	/* Return false for anything else. */
	return false;
}

/**
 * Scans the post for images within the content.  Not called by default with get_the_image().  Shouldn't use 
 * if using large images within posts, better to use the other options.
 *
 * @since 0.7.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function get_the_image_by_scan( $args = array() ) {

	/* Search the post's content for the <img /> tag and get its URL. */
	preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $args['post_id'] ), $matches );

	/* If there is a match for the image, return its URL. */
	if ( isset( $matches ) && !empty( $matches[1][0] ) )
		return array( 'src' => $matches[1][0] );

	return false;
}

/**
 * Used for setting a default image.  The function simply returns the image URL it was given in an array.  
 * Not used with get_the_image() by default.
 *
 * @since 0.7.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function get_the_image_by_default( $args = array() ) {
	return array( 'src' => $args['default_image'] );
}

/**
 * Formats an image with appropriate alt text and class.  Adds a link to the post if argument is set.  Should 
 * only be called if there is an image to display, but will handle it if not.
 *
 * @since 0.7.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @param array $image Array of image attributes ($image, $classes, $alt, $caption).
 * @return string $image Formatted image (w/link to post if the option is set).
 */
function get_the_image_format( $args = array(), $image = false ) {

	/* If there is no image URL, return false. */
	if ( empty( $image['src'] ) )
		return false;

	/* Extract the arguments for easy-to-use variables. */
	extract( $args );

	/* If there is alt text, set it.  Otherwise, default to the post title. */
	$image_alt = ( ( !empty( $image['alt'] ) ) ? $image['alt'] : get_post_field( 'post_title', $post_id ) );

	/* If there is a width or height, set them as HMTL-ready attributes. */
	$width = ( ( $width ) ? ' width="' . esc_attr( $width ) . '"' : '' );
	$height = ( ( $height ) ? ' height="' . esc_attr( $height ) . '"' : '' );

	/* Loop through the custom field keys and add them as classes. */
	if ( is_array( $meta_key ) ) {
		foreach ( $meta_key as $key )
			$classes[] = sanitize_html_class( $key );
	}

	/* Add the $size and any user-added $image_class to the class. */
	$classes[] = sanitize_html_class( $size );
	$classes[] = sanitize_html_class( $image_class );

	/* Join all the classes into a single string and make sure there are no duplicates. */
	$class = join( ' ', array_unique( $classes ) );

	/* Add the image attributes to the <img /> element. */
	$html = '<img src="' . $image['src'] . '" alt="' . esc_attr( strip_tags( $image_alt ) ) . '" class="' . esc_attr( $class ) . '"' . $width . $height . ' />';

	/* If $link_to_post is set to true, link the image to its post. */
	if ( $link_to_post )
		$html = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_post_field( 'post_title', $post_id ) ) . '">' . $html . '</a>';

	/* If there is a $post_thumbnail_id, apply the WP filters normally associated with get_the_post_thumbnail(). */
	if ( !empty( $image['post_thumbnail_id'] ) )
		$html = apply_filters( 'post_thumbnail_html', $html, $post_id, $image['post_thumbnail_id'], $size, '' );

	return $html;
}

/**
 * Saves the image URL as the value of the meta key provided.  This allows users to set a custom meta key 
 * for their image.  By doing this, users can trim off database queries when grabbing attachments or get rid 
 * of expensive scans of the content when using the image scan feature.
 *
 * @since 0.6.0
 * @access private
 * @param array $args Arguments for how to load and display the image.
 * @param array $image Array of image attributes ($image, $classes, $alt, $caption).
 */
function get_the_image_meta_key_save( $args = array(), $image = array() ) {

	/* If the $meta_key_save argument is empty or there is no image $url given, return. */
	if ( empty( $args['meta_key_save'] ) || empty( $image['src'] ) )
		return;

	/* Get the current value of the meta key. */
	$meta = get_post_meta( $args['post_id'], $args['meta_key_save'], true );

	/* If there is no value for the meta key, set a new value with the image $url. */
	if ( empty( $meta ) )
		add_post_meta( $args['post_id'], $args['meta_key_save'], $image['src'] );

	/* If the current value doesn't match the image $url, update it. */
	elseif ( $meta !== $image['src'] )
		update_post_meta( $args['post_id'], $args['meta_key_save'], $image['src'], $meta );
}

/**
 * Deletes the image cache for the specific post when the 'save_post' hook is fired.
 *
 * @since 0.7.0
 * @access private
 * @param int $post_id The ID of the post to delete the cache for.
 * @return void
 */
function get_the_image_delete_cache_by_post( $post_id ) {
	wp_cache_delete( $post_id, 'get_the_image' );
}

/**
 * Deletes the image cache for a specific post when the 'added_post_meta', 'deleted_post_meta', 
 * or 'updated_post_meta' hooks are called.
 *
 * @since 0.7.0
 * @access private
 * @param int $meta_id The ID of the metadata being updated.
 * @param int $post_id The ID of the post to delete the cache for.
 * @return void
 */
function get_the_image_delete_cache_by_meta( $meta_id, $post_id ) {
	wp_cache_delete( $post_id, 'get_the_image' );
}

/**
 * @since 0.1.0
 * @deprecated 0.3.0
 */
function get_the_image_link( $deprecated = '', $deprecated_2 = '', $deprecated_3 = '' ) {
	get_the_image();
}

/**
 * @since 0.3.0
 * @deprecated 0.7.0
 */
function image_by_custom_field( $args = array() ) {
	return get_the_image_by_meta_key( $args );
}

/**
 * @since 0.4.0
 * @deprecated 0.7.0
 */
function image_by_the_post_thumbnail( $args = array() ) {
	return get_the_image_by_post_thumbnail( $args );
}

/**
 * @since 0.3.0
 * @deprecated 0.7.0
 */
function image_by_attachment( $args = array() ) {
	return get_the_image_by_attachment( $args );
}

/**
 * @since 0.3.0
 * @deprecated 0.7.0
 */
function image_by_scan( $args = array() ) {
	return get_the_image_by_scan( $args );
}

/**
 * @since 0.3.0
 * @deprecated 0.7.0
 */
function image_by_default( $args = array() ) {
	return get_the_image_by_default( $args );
}

/**
 * @since 0.1.0
 * @deprecated 0.7.0
 */
function display_the_image( $args = array(), $image = false ) {
	return get_the_image_format( $args, $image );
}

/**
 * @since 0.5.0
 * @deprecated 0.7.0 Replaced by cache delete functions specifically for the post ID.
 */
function get_the_image_delete_cache() {
	return;
}

?>