<?php
/**
 * Functions for handling how comments are displayed and used on the site.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Outputs the comment reply link.  Only use outside of `wp_list_comments()`.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function hybrid_comment_reply_link( $args = array() ) {
	echo hybrid_get_comment_reply_link( $args );
}

/**
 * Outputs the comment reply link.  Note that WP's `comment_reply_link()` doesn't work outside of
 * `wp_list_comments()` without passing in the proper arguments (it isn't meant to).  This function 
 * is just a wrapper for `get_comment_reply_link()`, which adds in the arguments automatically.
 *
 * @since  2.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function hybrid_get_comment_reply_link( $args = array() ) {

	if ( ! get_option( 'thread_comments' ) || in_array( get_comment_type(), array( 'pingback', 'trackback' ) ) )
		return '';

	$args = wp_parse_args(
		$args,
		array(
			'depth'     => intval( $GLOBALS['comment_depth'] ),
			'max_depth' => get_option( 'thread_comments_depth' ),
		)
	);

	return get_comment_reply_link( $args );
}

/**
 * Uses the `$comment_type` to determine which comment template should be used. Once the
 * template is located, it is loaded for use. Child themes can create custom templates based off
 * the `$comment_type`. The comment template hierarchy is `comment-$comment_type.php`,
 * `comment.php`.
 *
 * The templates are saved in `$hybrid->comment_template[$comment_type]`, so each comment template
 * is only located once if it is needed. Following comments will use the saved template.
 *
 * @since  0.2.3
 * @access public
 * @global object  $hybrid
 * @param  object  $comment
 * @return void
 */
function hybrid_comments_callback( $comment ) {
	global $hybrid;

	// Get the comment type of the current comment.
	$comment_type = get_comment_type( $comment->comment_ID );

	// Create an empty array if the comment template array is not set.
	if ( ! isset( $hybrid->comment_template ) || ! is_array( $hybrid->comment_template ) )
		$hybrid->comment_template = array();

	// Check if a template has been provided for the specific comment type.  If not, get the template.
	if ( ! isset( $hybrid->comment_template[ $comment_type ] ) ) {

		// Create an array of template files to look for.
		$templates = array( "comment-{$comment_type}.php", "comment/{$comment_type}.php" );

		// If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'.
		if ( 'pingback' == $comment_type || 'trackback' == $comment_type ) {
			$templates[] = 'comment-ping.php';
			$templates[] = 'comment/ping.php';
		}

		// Add the fallback 'comment.php' template.
		$templates[] = 'comment/comment.php';
		$templates[] = 'comment.php';

		// Allow devs to filter the template hierarchy.
		$templates = apply_filters( 'hybrid_comment_template_hierarchy', $templates, $comment_type );

		// Locate the comment template.
		$template = locate_template( $templates );

		// Set the template in the comment template array.
		$hybrid->comment_template[ $comment_type ] = $template;
	}

	// If a template was found, load the template.
	if ( ! empty( $hybrid->comment_template[ $comment_type ] ) )
		require( $hybrid->comment_template[ $comment_type ] );
}

/**
 * Ends the display of individual comments. Uses the callback parameter for `wp_list_comments()`.
 * Needs to be used in conjunction with `hybrid_comments_callback()`. Not needed but used just in
 * case something is changed.
 *
 * @since  0.2.3
 * @access public
 * @return void
 */
function hybrid_comments_end_callback() {
	echo '</li><!-- .comment -->';
}
