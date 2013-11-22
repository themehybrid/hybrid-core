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
 * Adds theme "post-thumbnail" size plus an internationalized version of the image size name to the 
 * "add media" modal.  This allows users to insert the image within their post content editor.
 *
 * @since  1.3.0
 * @access private
 * @param  array   $sizes  Selectable image sizes.
 * @return array
 */
function hybrid_image_size_names_choose( $sizes ) {
	global $_wp_additional_image_sizes;

	/* If the theme as set a custom post thumbnail size, give it a nice name. */
	if ( isset( $_wp_additional_image_sizes['post-thumbnail'] ) )
		$sizes['post-thumbnail'] = __( 'Post Thumbnail', 'hybrid-core' );

	/* Return the image size names. */
	return $sizes;
}
