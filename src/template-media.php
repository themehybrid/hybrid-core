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
