<?php
/**
 * Template functions.
 *
 * Functions for loading template parts.  These functions are helper functions
 * or more flexible functions than what core WordPress currently offers with
 * template part loading.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Creates a hierarchy based on the current post.  For use with content-specific templates.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_post_hierarchy() {

	// Set up an empty array and get the post type.
	$hierarchy = array();
	$post_type = get_post_type();

	// If attachment, add attachment type templates.
	if ( 'attachment' === $post_type ) {

		$type    = get_attachment_type();
		$subtype = get_attachment_subtype();

		if ( $subtype ) {
			$hierarchy[] = "attachment-{$type}-{$subtype}";
			$hierarchy[] = "attachment-{$subtype}";
		}

		$hierarchy[] = "attachment-{$type}";
	}

	// If the post type supports 'post-formats', get the template based on the format.
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// Get the post format.
		$post_format = get_post_format() ? get_post_format() : 'standard';

		// Template based off post type and post format.
		$hierarchy[] = "{$post_type}-{$post_format}";

		// Template based off the post format.
		$hierarchy[] = $post_format;
	}

	// Template based off the post type.
	$hierarchy[] = $post_type;

	return $hierarchy;
}
