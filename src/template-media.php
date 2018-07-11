<?php
/**
 * Media template tags.
 *
 * Media template functions. These functions are meant to handle various features
 * needed in theme templates for media and attachments.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Splits the attachment mime type into two distinct parts: type / subtype
 * (e.g., image / png). Returns an array of the parts.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return array
 */
function get_attachment_types( $post_id = 0 ) {

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$mime    = get_post_mime_type( $post_id );

	list( $type, $subtype ) = false !== strpos( $mime, '/' ) ? explode( '/', $mime ) : [ $mime, '' ];

	return (object) [ 'type' => $type, 'subtype' => $subtype ];
}

/**
 * Returns the main attachment mime type.  For example, `image` when the file
 * has an `image / jpeg` mime type.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return string
 */
function get_attachment_type( $post_id = 0 ) {

	return get_attachment_types( $post_id )->type;
}

/**
 * Returns the attachment mime subtype.  For example, `jpeg` when the file has
 * an `image / jpeg` mime type.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return string
 */
function get_attachment_subtype( $post_id = 0 ) {

	return get_attachment_types( $post_id )->subtype;
}

/**
 * Returns a set of image attachment links based on size.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_image_size_links() {

	// If not viewing an image attachment page, return.
	if ( ! wp_attachment_is_image( get_the_ID() ) ) {
		return;
	}

	// Set up an empty array for the links.
	$links = [];

	// Get the intermediate image sizes and add the full size to the array.
	$sizes   = get_intermediate_image_sizes();
	$sizes[] = 'full';

	// Loop through each of the image sizes.
	foreach ( $sizes as $size ) {

		// Get the image source, width, height, and whether it's intermediate.
		$image = wp_get_attachment_image_src( get_the_ID(), $size );

		// Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size.
		if ( ! empty( $image ) && ( true === $image[3] || 'full' == $size ) ) {

			$label = sprintf(
				// Translators: Media dimensions - 1 is width and 2 is height.
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $image[1] ) ),
				number_format_i18n( absint( $image[2] ) )
			);

			$links[] = sprintf(
				'<a href="%s" class="image-size-link">%s</a>',
				esc_url( $image[0] ),
				$label
			);
		}
	}

	// Join the links in a string and return.
	return join( ' <span class="sep">/</span> ', $links );
}

/**
 * Gets the "transcript" for an audio attachment.  This is typically saved as
 * "unsynchronised_lyric", which is the ID3 tag sanitized by WordPress.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $post_id
 * @return string
 */
function get_audio_transcript( $post_id = 0 ) {

	return get_media_meta( 'lyrics', [
		'wrap'    => '',
		'post_id' => $post_id ?: get_the_ID()
	] );
}

/**
 * Loads the correct function for handling attachments.  Checks the attachment
 * mime type to call correct function. Image attachments are not loaded with
 * this function.  The functionality for them should be handled by the theme's
 * attachment or image attachment file.
 *
 * Ideally, all attachments would be appropriately handled within their
 * templates. However, this could lead to messy template files.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function attachment() {

	$type = get_attachment_type();
	$mime = get_post_mime_type();
	$url  = wp_get_attachment_url();
	$func = __NAMESPACE__ . "\\{$type}_attachment";

	$attachment = function_exists( $func ) ? call_user_func( $func, $mime, $url ) : '';

	echo apply_filters(
		'hybrid/attachment',
		apply_filters( "hybrid/{$type}_attachment", $attachment )
	);
}

/**
 * Handles application attachments on their attachment pages.  Uses the `<object>`
 * tag to embed media on those pages.
 *
 * @since  5.0.0
 * @access public
 * @param  string $mime attachment mime type
 * @param  string $file attachment file URL
 * @return string
 */
function application_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();

	return sprintf(
		'<object type="%1$s" data="%2$s" width="%3$s" height="%4$s"><param name="src" value="%2$s" /></object>',
		esc_attr( $mime ),
		esc_url( $file ),
		absint( $embed_defaults['width'] ),
		absint( $embed_defaults['height'] )
	);
}

/**
 * Handles text attachments on their attachment pages.  Uses the `<object>`
 * element to embed media in the pages.
 *
 * @since  5.0.0
 * @access public
 * @param  string $mime attachment mime type
 * @param  string $file attachment file URL
 * @return string
 */
function text_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();

	return sprintf(
		'<object type="%1$s" data="%2$s" width="%3$s" height="%4$s"><param name="src" value="%2$s" /></object>',
		esc_attr( $mime ),
		esc_url( $file ),
		absint( $embed_defaults['width'] ),
		absint( $embed_defaults['height'] )
	);
}
