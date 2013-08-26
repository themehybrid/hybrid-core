<?php
/**
 * Functions for loading template parts.  These functions are helper functions or more flexible functions 
 * than what core WordPress currently offers with template part loading.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Loads a post content template based off the post type and/or the post format.  This functionality is 
 * not feasible with the WordPress get_template_part() function, so we have to rely on some custom logic 
 * and locate_template().
 *
 * Note that using this function assumes that you're creating a content template to handle attachments. 
 * This filter must be removed since we're bypassing the WP template hierarchy and focusing on templates 
 * specific to the content.
 *
 * @since  1.6.0
 * @access public
 * @return string
 */
function hybrid_get_content_template() {

	/* Set up an empty array and get the post type. */
	$templates = array();
	$post_type = get_post_type();

	/* Assume the theme developer is creating an attachment template. */
	if ( 'attachment' == $post_type )
		remove_filter( 'the_content', 'prepend_attachment' );

	/* If the post type supports 'post-formats', get the template based on the format. */
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		/* Get the post format. */
		$post_format = get_post_format() ? get_post_format() : 'standard';

		/* Template based off post type and post format. */
		$templates[] = "content-{$post_type}-{$post_format}.php";

		/* Template based off the post format. */
		$templates[] = "content-{$post_format}.php";
	}

	/* Template based off the post type. */
	$templates[] = "content-{$post_type}.php";

	/* Fallback 'content.php' template. */
	$templates[] = 'content.php';

	/* Apply filters and return the found content template. */
	include( apply_atomic( 'content_template', locate_template( $templates, false, false ) ) );
}

?>