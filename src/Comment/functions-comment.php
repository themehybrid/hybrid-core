<?php
/**
 * Comment functions.
 *
 * Helper functions and template tags related to comments.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

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
 * Outputs the comment author HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_author( array $args = [] ) {

	echo render_author( $args );
}

/**
 * Returns the comment author HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function render_author( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'comment__author',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<span class="%s">%s</span>',
		esc_attr( $args['class'] ),
		get_comment_author_link()
	);

	return apply_filters( 'hybrid/comment/author', $args['before'] . $html . $args['after'] );
}

/**
 * Outputs the comment permalink HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_permalink( array $args = [] ) {

	echo render_permalink( $args );
}

/**
 * Returns the comment permalink HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function render_permalink( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'comment__permalink',
		'before' => '',
		'after'  => ''
	] );

	$url = get_comment_link();

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $url ),
		sprintf( $args['text'], esc_url( $url ) )
	);

	return apply_filters( 'hybrid/comment/permalink', $args['before'] . $html . $args['after'] );
}

/**
 * Outputs the comment date HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_date( array $args = [] ) {

	echo render_date( $args );
}

/**
 * Returns the comment date HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function render_date( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'format' => '',
		'class'  => 'comment__date',
		'before' => '',
		'after'  => ''
	] );

	$url = get_comment_link();

	$html = sprintf(
		'<time class="%s" datetime="%s">%s</time>',
		esc_attr( $args['class'] ),
		esc_attr( get_comment_date( DATE_W3C ) ),
		sprintf( $args['text'], esc_html( get_comment_date( $args['format'] ) ) )
	);

	return apply_filters( 'hybrid/comment/date', $args['before'] . $html . $args['after'] );
}

/**
 * Outputs the comment time HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_time( array $args = [] ) {

	echo render_time( $args );
}

/**
 * Returns the comment time HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function render_time( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'format' => '',
		'class'  => 'comment__time',
		'before' => '',
		'after'  => ''
	] );

	$url = get_comment_link();

	$html = sprintf(
		'<time class="%s" datetime="%s">%s</time>',
		esc_attr( $args['class'] ),
		esc_attr( get_comment_date( DATE_W3C ) ),
		sprintf( $args['text'], esc_html( get_comment_time( $args['format'] ) ) )
	);

	return apply_filters( 'hybrid/comment/time', $args['before'] . $html . $args['after'] );
}

/**
 * Outputs the comment edit link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_edit_link( array $args = [] ) {

	echo render_edit_link( $args );
}

/**
 * Returns the comment edit link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function render_edit_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => __( 'Edit', 'hybrid-core' ),
		'class'  => 'comment__edit',
		'before' => '',
		'after'  => ''
	] );

	$html = '';
	$url  = get_edit_comment_link();

	if ( $url ) {

		$html = sprintf(
			'<a class="%s" href="%s">%s</a>',
			esc_attr( $args['class'] ),
			esc_url( $url ),
			$args['text']
		);

		$html = $args['before'] . $html . $args['after'];
	}

	return apply_filters( 'hybrid/comment/edit_link', $html );
}

/**
 * Outputs the comment reply link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_reply_link( array $args = [] ) {

	echo render_reply_link( $args );
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
function render_reply_link( array $args = [] ) {

	// Array of comment types that are not allowed to have replies.
	$disallowed = [
		'pingback',
		'trackback'
	];

	if ( ! get_option( 'thread_comments' ) || in_array( get_comment_type(), $disallowed ) ) {
		return '';
	}

	$args = wp_parse_args( $args, [
		'before'    => '',
		'after'     => '',
		'depth'     => intval( $GLOBALS['comment_depth'] ),
		'max_depth' => get_option( 'thread_comments_depth' ),
		'class'     => 'comment__reply'
	] );

	$before = $args['before'];
	$after  = $args['after'];

	unset( $args['before'], $args['after'] );

	$html = get_comment_reply_link( $args );

	if ( $html ) {

		$html = preg_replace(
			"/class=(['\"]).+?(['\"])/i",
			'class=$1' . esc_attr( $args['class'] ) . ' comment-reply-link$2',
			$html,
			1
		);

		$html = $before . $html . $after;
	}

	return apply_filters( 'hybrid/comment/reply_link', $html );
}

/**
 * Outputs the comment parent link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function display_parent_link( array $args = [] ) {

	echo render_parent_link( $args );
}

/**
 * Returns the comment parent link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function render_parent_link( $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s', // Defaults to parent comment author.
		'depth'  => 2,    // At what level should the link show.
		'class'  => 'comment__parent-link',
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
				'<a class="%s" href="%s">%s</a>',
				esc_attr( $args['class'] ),
				esc_url( $url ),
				esc_html( $text )
			);

			$html = $args['before'] . $html . $args['after'];
		}
	}

	return apply_filters( 'hybrid/comment/parent_link', $html, $args );
}

/**
 * Conditional function to check if a comment is approved.
 *
 * @since  5.0.0
 * @access public
 * @param  \WP_Comment|int  Comment object or ID.
 * @return bool
 */
function is_approved( $comment = null ) {
	$comment = get_comment( $comment );

	return 'approved' === wp_get_comment_status( $comment->ID );
}
