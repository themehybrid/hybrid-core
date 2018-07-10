<?php
/**
 * Post template tags.
 *
 * Template functions related to posts.  The functions in this file are for
 * handling template tags or features of template tags that WordPress core does
 * not currently handle.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Checks if a post has any content. Useful if you need to check if the user has
 * written any content before performing any actions.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return bool
 */
function post_has_content( $post_id = 0 ) {
	$post = get_post( $post_id );

	return ! empty( $post->post_content );
}

/* === Galleries === */

/**
 * Gets the gallery *item* count.  This is different from getting the gallery
 * *image* count.  By default, WordPress only allows attachments with the 'image'
 * mime type in galleries.  However, some scripts such as Cleaner Gallery allow
 * for other mime types.  This is a more accurate count than the
 * `get_gallery_image_count()` function since it will count all gallery items
 * regardless of mime type.
 *
 * @todo Check for the [gallery] shortcode with 'mime_type' parameter and use in get_posts().
 *
 * @since  5.0.0
 * @access public
 * @return int
 */
function get_gallery_item_count() {

	// Check the post content for galleries.
	$galleries = get_post_galleries( get_the_ID(), true );

	// If galleries were found in the content, get the gallery item count.
	if ( ! empty( $galleries ) ) {
		$items = '';

		foreach ( $galleries as $gallery => $gallery_items ) {
			$items .= $gallery_items;
		}

		preg_match_all( '#src=([\'"])(.+?)\1#is', $items, $sources, PREG_SET_ORDER );

		if ( ! empty( $sources ) ) {
			return count( $sources );
		}
	}

	// If an item count wasn't returned, get the post attachments.
	$attachments = get_posts( [
		'fields'      => 'ids',
		'post_parent' => get_the_ID(),
		'post_type'   => 'attachment',
		'numberposts' => -1
	] );

	// Return the attachment count if items were found.
	return ! empty( $attachments ) ? count( $attachments ) : 0;
}

/**
 * Returns the number of images displayed by the gallery or galleries in a post.
 *
 * @since  5.0.0
 * @access public
 * @return int
 */
function get_gallery_image_count() {

	// Set up an empty array for images.
	$images = [];

	// Get the images from all post galleries.
	$galleries = get_post_galleries_images();

	// Merge each gallery image into a single array.
	foreach ( $galleries as $gallery_images ) {
		$images = array_merge( $images, $gallery_images );
	}

	// If there are no images in the array, just grab the attached images.
	if ( empty( $images ) ) {

		$images = get_posts( [
			'fields'         => 'ids',
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => -1
		] );
	}

	// Return the count of the images.
	return count( $images );
}

/* === Links === */

/**
 * Gets a URL from the content, even if it's not wrapped in an <a> tag.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $content
 * @return string
 */
function get_content_url( $content ) {

	// Catch links that are not wrapped in an '<a>' tag.
	preg_match(
		'/<a\s[^>]*?href=[\'"](.+?)[\'"]/is',
		make_clickable( $content ),
		$matches
	);

	return ! empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
}

/**
 * Looks for a URL in the post. If none is found, return the post permalink.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $url
 * @param  object  $post
 * @return string
 */
function get_the_post_format_url( $url = '', $post = null ) {

	if ( ! $url ) {

		$post = is_null( $post ) ? get_post() : $post;

		$content_url = get_content_url( $post->post_content );

		$url = $content_url ? esc_url( $content_url ) : esc_url( get_permalink( $post->ID ) );
	}

	return $url;
}
