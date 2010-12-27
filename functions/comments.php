<?php
/**
 * Functions for handling how comments are displayed and used on the site. This allows more precise 
 * control over their display and makes more filter and action hooks available to developers to use in their 
 * customizations.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Filter the comment form defaults.
 * @since 0.8.0
 */
add_filter( 'comment_form_defaults', 'hybrid_comment_form_args' );

/**
 * Arguments for the wp_list_comments_function() used in comments.php. Users can set up a 
 * custom comments callback function by changing $callback to the custom function.  Note that 
 * $style should remain 'ol' since this is hardcoded into the theme and is the semantically correct
 * element to use for listing comments.
 *
 * @since 0.7.0
 * @return array $args Arguments for listing comments.
 */
function hybrid_list_comments_args() {
	$args = array( 'style' => 'ol', 'type' => 'all', 'avatar_size' => 80, 'callback' => 'hybrid_comments_callback', 'end-callback' => 'hybrid_comments_end_callback' );
	return apply_atomic( 'list_comments_args', $args );
}

/**
 * Uses the $comment_type to determine which comment template should be used. Once the 
 * template is located, it is loaded for use. Child themes can create custom templates based off
 * the $comment_type. The comment template hierarchy is comment-$comment_type.php, 
 * comment.php.
 *
 * The templates are saved in $hybrid->templates[comment_template], so each comment template
 * is only located once if it is needed. Following comments will use the saved template.
 *
 * @since 0.2.3
 * @param $comment The comment variable
 * @param $args Array of arguments passed from wp_list_comments()
 * @param $depth What level the particular comment is
 */
function hybrid_comments_callback( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $depth;

	$comment_type = get_comment_type( $comment->comment_ID );

	$cache = wp_cache_get( 'comment_template' );

	if ( !is_array( $cache ) )
		$cache = array();

	if ( !isset( $cache[$comment_type] ) ) {
		$template = locate_template( array( "comment-{$comment_type}.php", 'comment.php' ) );

		$cache[$comment_type] = $template;
		wp_cache_set( 'comment_template', $cache );
	}

	if ( !empty( $cache[$comment_type] ) )
		require( $cache[$comment_type] );
}

/**
 * Ends the display of individual comments. Uses the callback parameter for wp_list_comments(). 
 * Needs to be used in conjunction with hybrid_comments_callback(). Not needed but used just in 
 * case something is changed.
 *
 * @since 0.2.3
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
 * @global $comment The current comment's DB object.
 * @global $hybrid The global Hybrid object.
 */
function hybrid_avatar() {
	global $comment, $hybrid;

	/* Make sure avatars are allowed before proceeding. */
	if ( !get_option( 'show_avatars' ) )
		return false;

	/* Get/set some comment variables. */
	$comment_type = get_comment_type( $comment->comment_ID );
	$author = esc_html( get_comment_author( $comment->comment_ID ) );
	$url = esc_url( get_comment_author_url( $comment->comment_ID ) );

	/* Set a default avatar for pingbacks and trackbacks. */
	$default_avatar = ( ( 'pingback' == $comment_type || 'trackback' == $comment_type ) ? trailingslashit( HYBRID_IMAGES ) . "{$comment_type}.png" : '' );

	/* Allow the default avatar to be filtered by comment type. */
	$default_avatar = apply_filters( "{$hybrid->prefix}_{$comment_type}_avatar", $default_avatar );

	/* Set up the avatar size. */
	$comment_list_args = hybrid_list_comments_args();
	$size = ( ( $comment_list_args['avatar_size'] ) ? $comment_list_args['avatar_size'] : 80 );

	/* Get the avatar provided by the get_avatar() function. */
	$avatar = get_avatar( get_comment_author_email( $comment->comment_ID ), absint( $size ), $default_avatar, $author );

	/* If URL input, wrap avatar in hyperlink. */
	if ( !empty( $url ) )
		$avatar = '<a href="' . $url . '" rel="external nofollow" title="' . $author . '">' . $avatar . '</a>';

	/* Display the avatar and allow it to be filtered. Note: Use the get_avatar filter hook where possible. */
	echo apply_filters( "{$hybrid->prefix}_avatar", $avatar );
}

/**
 * Filters the WordPress comment_form() function that was added in WordPress 3.0.  This allows
 * the theme to preserve some backwards compatibility with its old comment form.  It also allows 
 * users to build custom comment forms by filtering 'comment_form_defaults' in their child theme.
 *
 * @since 0.8.0
 * @param array $args The default comment form arguments.
 * @return array $args The filtered comment form arguments.
 */
function hybrid_comment_form_args( $args ) {
	global $user_identity;

	$domain = hybrid_get_textdomain();
	$commenter = wp_get_current_commenter();
	$req = ( ( get_option( 'require_name_email' ) ) ? ' <span class="required">' . __( '*', $domain ) . '</span> ' : '' );
	$input_class = ( ( get_option( 'require_name_email' ) ) ? ' req' : '' );

	$fields = array(
		'author' => '<p class="form-author' . $input_class . '"><label for="author">' . __( 'Name', $domain ) . $req . '</label> <input type="text" class="text-input" name="author" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="40" /></p>',
		'email' => '<p class="form-email' . $input_class . '"><label for="email">' . __( 'Email', $domain ) . $req . '</label> <input type="text" class="text-input" name="email" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="40" /></p>',
		'url' => '<p class="form-url"><label for="url">' . __( 'Website', $domain ) . '</label><input type="text" class="text-input" name="url" id="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="40" /></p>'
	);

	$args = array(
		'fields' => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field' => '<p class="form-textarea req"><label for="comment">' . __( 'Comment', $domain ) . '</label><textarea name="comment" id="comment" cols="60" rows="10"></textarea></p>',
		'must_log_in' => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', $domain ), wp_login_url( get_permalink() ) ) . '</p><!-- .alert -->',
		'logged_in_as' => '<p class="log-in-out">' . sprintf( __( 'Logged in as <a href="%1$s" title="%2$s">%2$s</a>.', $domain ), admin_url( 'profile.php' ), esc_attr( $user_identity ) ) . ' <a href="' . wp_logout_url( get_permalink() ) . '" title="' . esc_attr__( 'Log out of this account', $domain ) . '">' . __( 'Log out &raquo;', $domain ) . '</a></p><!-- .log-in-out -->',
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'id_form' => 'commentform',
		'id_submit' => 'submit',
		'title_reply' => __( 'Leave a Reply', $domain ),
		'title_reply_to' => __( 'Leave a Reply to %s', $domain ),
		'cancel_reply_link' => __( 'Click here to cancel reply.', $domain ),
		'label_submit' => __( 'Post Comment', $domain ),
	);

	return $args;
}

?>