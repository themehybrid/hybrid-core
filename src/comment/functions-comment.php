<?php

namespace Hybrid\Comment;

/**
 * Returns a hierarchy for the current comment.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function hierarchy() {

	$hier = [];
	$type = get_comment_type() ?: 'comment';

	$hier[] = $type;

	if ( in_array( $type, [ 'pingback', 'trackback'] ) ) {

		$hier[] = 'ping';
	}

	return apply_filters( 'hybrid/comment/hierarchy', $hier );
}

/**
 * Renders the comment reply link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function render_reply_link( array $args = [] ) {

	echo fetch_reply_link( $args );
}

/**
 * Returns the comment reply link HTML.  Note that WP's `comment_reply_link()`
 * doesn't work outside of `wp_list_comments()` without passing in the proper
 * arguments (it isn't meant to).  This function is just a wrapper for
 * `get_comment_reply_link()`, which adds in the arguments automatically.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_reply_link( array $args = [] ) {

	// Array of comment types that are not allowed to have replies.
	$disallowed = [
		'pingback',
		'trackback'
	];

	if ( ! get_option( 'thread_comments' ) || in_array( get_comment_type(), $disallowed ) ) {
		return '';
	}

	$args = wp_parse_args( $args, [
		'depth'     => intval( $GLOBALS['comment_depth'] ),
		'max_depth' => get_option( 'thread_comments_depth' )
	] );

	return get_comment_reply_link( $args );
}

/**
 * Renders the comment parent link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function render_parent_link( array $args = [] ) {

	echo fetch_parent_link( $args );
}

/**
 * Returns the comment parent link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function fetch_parent_link( $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s', // Defaults to parent comment author.
		'depth'  => 2,    // At what level should the link show.
		'before' => '',
		'after'  => ''
	] );

	$html = '';

	// Only display the link if the current comment is greater than or equal
	// to the depth requested.
	if ( $args['depth'] <= $GLOBALS['comment_depth'] ) {

		$parent = get_comment()->comment_parent;

		if ( 0 < $parent ) {

			$url  = get_comment_link( $parent );
			$text = sprintf( $args['text'], get_comment_author( $parent ) );

			$html = sprintf(
				'<a class="comment__parent-link" href="%s">%s</a>',
				esc_url( $url ),
				esc_html( $text )
			);

			$html = $args['before'] . $html . $args['after'];
		}
	}

	return apply_filters( 'hybrid/comment/parent_link', $html, $args );
}
