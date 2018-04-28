<?php
/**
 * Comment template tags.
 *
 * Functions for handling how comments are displayed and used on the site.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Filter the comments template.
add_filter( 'comments_template', __NAMESPACE__ . '\comments_template', 5 );

/**
 * Returns a hierarchy for the current comment.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_comment_hierarchy() {

	$hier = [];
	$type = get_comment_type() ?: 'comment';

	$hier[] = $type;

	if ( in_array( $type, [ 'pingback', 'trackback'] ) ) {

		$hier[] = 'ping';
	}

	return apply_filters( 'hybrid/hierarchy/comment', $hier );
}

/**
 * Outputs the comment reply link.  Only use outside of `wp_list_comments()`.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function comment_reply_link( $args = [] ) {

	echo get_comment_reply_link( $args );
}

/**
 * Outputs the comment reply link.  Note that WP's `comment_reply_link()`
 * doesn't work outside of `wp_list_comments()` without passing in the proper
 * arguments (it isn't meant to).  This function is just a wrapper for
 * `get_comment_reply_link()`, which adds in the arguments automatically.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function get_comment_reply_link( $args = [] ) {

	if ( ! get_option( 'thread_comments' ) || in_array( get_comment_type(), [ 'pingback', 'trackback' ] ) ) {
		return '';
	}

	$args = wp_parse_args( $args, [
		'depth'     => intval( $GLOBALS['comment_depth'] ),
		'max_depth' => get_option( 'thread_comments_depth' )
	] );

	return \get_comment_reply_link( $args );
}

/**
 * Prints the comment parent link.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function comment_parent_link( $args = [] ) {

	echo get_comment_parent_link( $args );
}

/**
 * Gets the link to the comment's parent comment.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function get_comment_parent_link( $args = [] ) {

	$link = '';

	$args = wp_parse_args( $args, [
		'text'   => '%s', // Defaults to parent comment author.
		'depth'  => 2,    // At what level should the link show.
		'before' => '',
		'after'  => ''
	] );

	// Only display the link if the current comment is greater than or equal
	// to the depth requested.
	if ( $args['depth'] <= $GLOBALS['comment_depth'] ) {

		$parent = get_comment()->comment_parent;

		if ( 0 < $parent ) {

			$url  = esc_url( get_comment_link( $parent ) );
			$text = sprintf( $args['text'], esc_html( get_comment_author( $parent ) ) );

			$link = sprintf(
				'%s<a class="comment-parent-link" href="%s">%s</a>%s',
				$args['before'],
				$url,
				$text,
				$args['after']
			);
		}
	}

	return apply_filters( 'hybrid/comment_parent_link', $link, $args );
}

/**
 * Overrides the default comments template.  This filter allows for a
 * `comments-{$post_type}.php` template based on the post type of the current
 * single post view.  If this template is not found, it falls back to the
 * default `comments.php` template.
 *
 * @since  5.0.0
 * @access public
 * @param  string $template
 * @return string
 */
function comments_template( $template ) {

	$templates = [];

	// Allow for custom templates entered into comments_template( $file ).
	$template = str_replace( trailingslashit( get_stylesheet_directory() ), '', $template );

	if ( 'comments.php' !== $template ) {
		$templates[] = $template;
	}

	// Add a comments template based on the post type.
	$templates[] = sprintf( 'comments/%s.php', get_post_type() );

	// Add the default comments template.
	$templates[] = 'comments/default.php';
	$templates[] = 'comments.php';

	// Return the found template.
	return locate_template( $templates );
}
