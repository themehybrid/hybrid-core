<?php
/**
 * Functions for handling how comments are displayed and used on the site. This allows more precise 
 * control over their display and makes more filter and action hooks available to developers to use in their 
 * customizations.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Arguments for the wp_list_comments_function() used in comments.php. Users can set up a 
 * custom comments callback function by changing $callback to the custom function.  Note that 
 * $style should remain 'ol' since this is hardcoded into the theme and is the semantically correct
 * element to use for listing comments.
 *
 * @since 0.7.0
 * @access public
 * @return array $args Arguments for listing comments.
 */
function hybrid_list_comments_args() {

	/* Set the default arguments for listing comments. */
	$args = array(
		'style'        => 'ol',
		'type'         => 'all',
		'avatar_size'  => 80,
		'callback'     => 'hybrid_comments_callback',
		'end-callback' => 'hybrid_comments_end_callback'
	);

	/* Return the arguments and allow devs to overwrite them. */
	return apply_atomic( 'list_comments_args', $args );
}

/**
 * Uses the $comment_type to determine which comment template should be used. Once the 
 * template is located, it is loaded for use. Child themes can create custom templates based off
 * the $comment_type. The comment template hierarchy is comment-$comment_type.php, 
 * comment.php.
 *
 * The templates are saved in $hybrid->comment_template[$comment_type], so each comment template
 * is only located once if it is needed. Following comments will use the saved template.
 *
 * @since 0.2.3
 * @access public
 * @param $comment The comment object.
 * @param $args Array of arguments passed from wp_list_comments().
 * @param $depth What level the particular comment is.
 * @return void
 */
function hybrid_comments_callback( $comment, $args, $depth ) {
	global $hybrid;
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $depth;

	/* Get the comment type of the current comment. */
	$comment_type = get_comment_type( $comment->comment_ID );

	/* Create an empty array if the comment template array is not set. */
	if ( !isset( $hybrid->comment_template) || !is_array( $hybrid->comment_template ) )
		$hybrid->comment_template = array();

	/* Check if a template has been provided for the specific comment type.  If not, get the template. */
	if ( !isset( $hybrid->comment_template[$comment_type] ) ) {

		/* Create an array of template files to look for. */
		$templates = array( "comment-{$comment_type}.php", "comment/{$comment_type}.php" );

		/* If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'. */
		if ( 'pingback' == $comment_type || 'trackback' == $comment_type ) {
			$templates[] = 'comment-ping.php';
			$templates[] = 'comment/ping.php';
		}

		/* Add the fallback 'comment.php' template. */
		$templates[] = 'comment/comment.php';
		$templates[] = 'comment.php';

		/* Allow devs to filter the template hierarchy. */
		$templates = apply_filters( 'hybrid_comment_template_hierarchy', $templates, $comment_type );

		/* Locate the comment template. */
		$template = locate_template( $templates );

		/* Set the template in the comment template array. */
		$hybrid->comment_template[ $comment_type ] = $template;
	}

	/* If a template was found, load the template. */
	if ( !empty( $hybrid->comment_template[ $comment_type ] ) )
		require( $hybrid->comment_template[ $comment_type ] );
}

/**
 * Ends the display of individual comments. Uses the callback parameter for wp_list_comments(). 
 * Needs to be used in conjunction with hybrid_comments_callback(). Not needed but used just in 
 * case something is changed.
 *
 * @since 0.2.3
 * @access public
 * @return void
 */
function hybrid_comments_end_callback() {
	echo '</li><!-- .comment -->';
}

/**
 * Displays the avatar for the comment author and wraps it in the comment author's URL if it is
 * available.  Adds a call to HYBRID_IMAGES . "/{$comment_type}.png" for the default avatars for
 * trackbacks and pingbacks.
 *
 * @since 0.2.0
 * @deprecated 2.0.0
 * @access public
 * @global $comment The current comment's DB object.
 * @global $hybrid The global Hybrid object.
 * @return void
 */
function hybrid_avatar() {
	global $comment, $hybrid;

	_deprecated_function( __FUNCTION__, '2.0.0', 'get_avatar' );

	/* Make sure avatars are allowed before proceeding. */
	if ( !get_option( 'show_avatars' ) )
		return false;

	/* Get the avatar provided by the get_avatar() function. */
	$avatar = get_avatar( $comment, 80, '', get_comment_author( $comment->comment_ID ) );

	/* Display the avatar and allow it to be filtered. Note: Use the get_avatar filter hook where possible. */
	echo apply_filters( "{$hybrid->prefix}_avatar", $avatar );
}

?>