<?php
/**
 * Functions that I've found useful that I wish WordPress had equivalents for baked right into core.  If 
 * there's a relevant Trac ticket, it'll be listed in with the function.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Retrieves the file with the highest priority that exists.  The function searches both the stylesheet 
 * and template directories.  This function is similar to the locate_template() function in WordPress 
 * but returns the file name with the URI path instead of the directory path.
 *
 * @since  1.5.0
 * @access public
 * @link   http://core.trac.wordpress.org/ticket/18302
 * @param  array  $file_names The files to search for.
 * @return string
 */
function hybrid_locate_theme_file( $file_names ) {

	$located = '';

	/* Loops through each of the given file names. */
	foreach ( (array) $file_names as $file ) {

		/* If the file exists in the stylesheet (child theme) directory. */
		if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$located = trailingslashit( get_stylesheet_directory_uri() ) . $file;
			break;
		}

		/* If the file exists in the template (parent theme) directory. */
		elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$located = trailingslashit( get_template_directory_uri() ) . $file;
			break;
		}
	}

	return $located;
}

/**
 * Checks if a post has any content. Useful if you need to check if the user has written any content 
 * before performing any actions.
 *
 * @since  1.6.0
 * @access public
 * @param  int    $id  The ID of the post.
 * @return bool
 */
function hybrid_post_has_content( $id = 0 ) {
	$post = get_post( $id );
	return !empty( $post->post_content ) ? true : false;
}

/**
 * Checks if the current post has a mime type of 'audio'.
 *
 * @since  1.6.0
 * @access public
 * @param  int    $post_id
 * @return bool
 */
function hybrid_attachment_is_audio( $post_id = 0 ) {

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$mime = get_post_mime_type( $post_id );
	$mime_type = explode( '/', $mime );

	return 'audio' == array_shift( $mime_type ) ? true : false;
}

/**
 * Checks if the current post has a mime type of 'video'.
 *
 * @since  1.6.0
 * @access public
 * @param  int    $post_id
 * @return bool
 */
function hybrid_attachment_is_video( $post_id = 0 ) {

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$mime = get_post_mime_type( $post_id );
	$mime_type = explode( '/', $mime );

	return 'video' == array_shift( $mime_type ) ? true : false;
}

?>