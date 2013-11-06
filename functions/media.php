<?php
/**
 * Functions for handling media (i.e., attachments) within themes.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2013, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add all image sizes to the image editor to insert into post. */
add_filter( 'image_size_names_choose', 'hybrid_image_size_names_choose' );

/**
 * Adds theme/plugin custom images sizes added with add_image_size() to the image uploader/editor.  This 
 * allows users to insert these images within their post content editor.
 *
 * @since 1.3.0
 * @access private
 * @param array $sizes Selectable image sizes.
 * @return array $sizes
 */
function hybrid_image_size_names_choose( $sizes ) {

	/* Get all intermediate image sizes. */
	$intermediate_sizes = get_intermediate_image_sizes();
	$add_sizes = array();

	/* Loop through each of the intermediate sizes, adding them to the $add_sizes array. */
	foreach ( $intermediate_sizes as $size )
		$add_sizes[ $size ] = 'post-thumbnail' === $size ? __( 'Post Thumbnail', 'hybrid-core' ) : $size;

	/* Merge the original array, keeping it intact, with the new array of image sizes. */
	$sizes = array_merge( $add_sizes, $sizes );

	/* Return the new sizes plus the old sizes back. */
	return $sizes;
}
