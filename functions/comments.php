<?php
/**
 * Functions for handling how comments are displayed and used on the site. This allows more precise 
 * control over their display and makes more filter and action hooks available to developers to use in their 
 * customizations.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2012, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filter the comment form defaults. */
add_filter( 'comment_form_defaults', 'hybrid_comment_form_args' );

/* Add a few comment types to the allowed avatar comment types list. */
add_filter( 'get_avatar_comment_types', 'hybrid_avatar_comment_types' );

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
		$templates = array( "comment-{$comment_type}.php" );

		/* If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'. */
		if ( 'pingback' == $comment_type || 'trackback' == $comment_type )
			$templates[] = 'comment-ping.php';

		/* Add the fallback 'comment.php' template. */
		$templates[] = 'comment.php';

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
 * @access public
 * @global $comment The current comment's DB object.
 * @global $hybrid The global Hybrid object.
 * @return void
 */
function hybrid_avatar() {
	global $comment, $hybrid;

	/* Make sure avatars are allowed before proceeding. */
	if ( !get_option( 'show_avatars' ) )
		return false;

	/* Get/set some comment variables. */
	$comment_type = get_comment_type( $comment->comment_ID );
	$author = get_comment_author( $comment->comment_ID );
	$url = get_comment_author_url( $comment->comment_ID );
	$avatar = '';
	$default_avatar = '';

	/* Get comment types that are allowed to have an avatar. */
	$avatar_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );

	/* If comment type is in the allowed list, check if it's a pingback or trackback. */
	if ( in_array( $comment_type, $avatar_comment_types ) ) {

		/* Set a default avatar for pingbacks and trackbacks. */
		$default_avatar = ( ( 'pingback' == $comment_type || 'trackback' == $comment_type ) ? trailingslashit( HYBRID_IMAGES ) . "{$comment_type}.png" : '' );

		/* Allow the default avatar to be filtered by comment type. */
		$default_avatar = apply_filters( "{$hybrid->prefix}_{$comment_type}_avatar", $default_avatar );
	}

	/* Set up the avatar size. */
	$comment_list_args = hybrid_list_comments_args();
	$size = ( ( $comment_list_args['avatar_size'] ) ? $comment_list_args['avatar_size'] : 80 );

	/* Get the avatar provided by the get_avatar() function. */
	$avatar = get_avatar( $comment, absint( $size ), $default_avatar, $author );

	/* If URL input, wrap avatar in hyperlink. */
	if ( !empty( $url ) && !empty( $avatar ) )
		$avatar = '<a href="' . esc_url( $url ) . '" rel="external nofollow" title="' . esc_attr( $author ) . '">' . $avatar . '</a>';

	/* Display the avatar and allow it to be filtered. Note: Use the get_avatar filter hook where possible. */
	echo apply_filters( "{$hybrid->prefix}_avatar", $avatar );
}

/**
 * Filters the WordPress comment_form() function that was added in WordPress 3.0.  This allows
 * the theme to preserve some backwards compatibility with its old comment form.  It also allows 
 * users to build custom comment forms by filtering 'comment_form_defaults' in their child theme.
 *
 * @since 0.8.0
 * @access public
 * @param array $args The default comment form arguments.
 * @return array $args The filtered comment form arguments.
 */
function hybrid_comment_form_args( $args ) {
	global $user_identity;

	/* Get the current commenter. */
	$commenter = wp_get_current_commenter();

	/* Create the required <span> and <input> element class. */
	$req = ( ( get_option( 'require_name_email' ) ) ? ' <span class="required">' . __( '*', 'hybrid-core' ) . '</span> ' : '' );
	$input_class = ( ( get_option( 'require_name_email' ) ) ? ' req' : '' );

	/* Sets up the default comment form fields. */
	$fields = array(
		'author' => '<p class="form-author' . esc_attr( $input_class ) . '"><label for="author">' . __( 'Name', 'hybrid-core' ) . $req . '</label> <input type="text" class="text-input" name="author" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="40" /></p>',
		'email'  => '<p class="form-email' . esc_attr( $input_class ) . '"><label for="email">' . __( 'Email', 'hybrid-core' ) . $req . '</label> <input type="text" class="text-input" name="email" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="40" /></p>',
		'url'    => '<p class="form-url"><label for="url">' . __( 'Website', 'hybrid-core' ) . '</label><input type="text" class="text-input" name="url" id="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="40" /></p>'
	);

	/* Sets the default arguments for displaying the comment form. */
	$args = array(
		'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field'        => '<p class="form-textarea req"><label for="comment">' . __( 'Comment', 'hybrid-core' ) . '</label><textarea name="comment" id="comment" cols="60" rows="10"></textarea></p>',
		'must_log_in'          => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', 'hybrid-core' ), wp_login_url( get_permalink() ) ) . '</p><!-- .alert -->',
		'logged_in_as'         => '<p class="log-in-out">' . sprintf( __( 'Logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'hybrid-core' ), admin_url( 'profile.php' ), esc_attr( $user_identity ) ) . ' <a href="' . wp_logout_url( get_permalink() ) . '" title="' . esc_attr__( 'Log out of this account', 'hybrid-core' ) . '">' . __( 'Log out &raquo;', 'hybrid-core' ) . '</a></p><!-- .log-in-out -->',
		'comment_notes_before' => '',
		'comment_notes_after'  => '',
		'id_form'              => 'commentform',
		'id_submit'            => 'submit',
		'title_reply'          => __( 'Leave a Reply', 'hybrid-core' ),
		'title_reply_to'       => __( 'Leave a Reply to %s', 'hybrid-core' ),
		'cancel_reply_link'    => __( 'Click here to cancel reply.', 'hybrid-core' ),
		'label_submit'         => __( 'Post Comment', 'hybrid-core' ),
	);

	/* Return the arguments for displaying the comment form. */
	return $args;
}

/**
 * Adds the 'pingback' and 'trackback' comment types to the allowed list of avatar comment types.  By
 * default, WordPress only allows the 'comment' comment type to have an avatar.
 *
 * @since 1.2.0
 * @access public
 * @param array $types List of all comment types allowed to have avatars.
 * @return array $types
 */
function hybrid_avatar_comment_types( $types ) {

	/* Add the 'pingback' comment type. */
	$types[] = 'pingback';

	/* Add the 'trackback' comment type. */
	$types[] = 'trackback';

	/* Return the array of comment types. */
	return $types;
}

?>