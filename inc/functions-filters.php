<?php
/**
 * Filters for theme-related WordPress features.  These filters are for handling adding or modifying the
 * output of common WordPress template tags to make for a richer theme development experience without
 * having to resort to custom template tags.  Many of the filters are simply for adding HTML5 microdata.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Don't strip tags on single post titles.
remove_filter( 'single_post_title', 'strip_tags' );

# Use same default filters as 'the_content' with a little more flexibility.
add_filter( 'hybrid_archive_description', array( $GLOBALS['wp_embed'], 'run_shortcode' ),   5  );
add_filter( 'hybrid_archive_description', array( $GLOBALS['wp_embed'], 'autoembed'     ),   5  );
add_filter( 'hybrid_archive_description',                              'wptexturize',       10 );
add_filter( 'hybrid_archive_description',                              'convert_smilies',   15 );
add_filter( 'hybrid_archive_description',                              'convert_chars',     20 );
add_filter( 'hybrid_archive_description',                              'wpautop',           25 );
add_filter( 'hybrid_archive_description',                              'do_shortcode',      30 );
add_filter( 'hybrid_archive_description',                              'shortcode_unautop', 35 );

# Default excerpt more.
add_filter( 'excerpt_more', 'hybrid_excerpt_more', 5 );

# Modifies the arguments and output of wp_link_pages().
add_filter( 'wp_link_pages_args', 'hybrid_link_pages_args', 5 );
add_filter( 'wp_link_pages_link', 'hybrid_link_pages_link', 5 );

# Filters to add microdata support to common template tags.
add_filter( 'the_author_posts_link',          'hybrid_the_author_posts_link',          5 );
add_filter( 'get_comment_author_link',        'hybrid_get_comment_author_link',        5 );
add_filter( 'get_comment_author_url_link',    'hybrid_get_comment_author_url_link',    5 );
add_filter( 'comment_reply_link',             'hybrid_comment_reply_link_filter',      5 );
add_filter( 'get_avatar',                     'hybrid_get_avatar',                     5 );
add_filter( 'post_thumbnail_html',            'hybrid_post_thumbnail_html',            5 );
add_filter( 'comments_popup_link_attributes', 'hybrid_comments_popup_link_attributes', 5 );

/**
 * Filters the excerpt more output with internationalized text and a link to the post.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $text
 * @return string
 */
function hybrid_excerpt_more( $text ) {

	if ( 0 !== strpos( $text, '<a' ) )
		$text = sprintf( ' <a href="%s" class="more-link">%s</a>', esc_url( get_permalink() ), trim( $text ) );

	return $text;
}

/**
 * Wraps the output of `wp_link_pages()` with `<p class="page-links">` if it's simply wrapped in a
 * `<p>` tag.
 *
 * @since  2.0.0
 * @access public
 * @param  array  $args
 * @return array
 */
function hybrid_link_pages_args( $args ) {
	$args['before'] = str_replace( '<p>', '<p class="page-links">', $args['before'] );
	return $args;
}

/**
 * Wraps page "links" that aren't actually links (just text) with `<span class="page-numbers">` so that they
 * can also be styled.  This makes `wp_link_pages()` consistent with the output of `paginate_links()`.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_link_pages_link( $link ) {
	return 0 !== strpos( $link, '<a' ) ? "<span class='page-numbers'>{$link}</span>" : $link;
}

/**
 * Adds microdata to the author posts link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_the_author_posts_link( $link ) {

	$pattern = array(
		"/(<a.*?)(>)/i",
		'/(<a.*?>)(.*?)(<\/a>)/i'
	);

	$replace = array(
		'$1 class="url fn n" itemprop="url"$2',
		'$1<span itemprop="name">$2</span>$3'
	);

	return preg_replace( $pattern, $replace, $link );
}

/**
 * Adds microdata to the comment author link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_get_comment_author_link( $link ) {

	$pattern = array(
		'/(class=[\'"])(.+?)([\'"])/i',
		"/(<a.*?)(>)/i",
		'/(<a.*?>)(.*?)(<\/a>)/i'
	);

	$replace = array(
		'$1$2 fn n$3',
		'$1 itemprop="url"$2',
		'$1<span itemprop="name">$2</span>$3'
	);

	return preg_replace( $pattern, $replace, $link );
}

/**
 * Adds microdata to the comment author URL link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_get_comment_author_url_link( $link ) {

	$pattern = array(
		'/(class=[\'"])(.+?)([\'"])/i',
		"/(<a.*?)(>)/i"
	);
	$replace = array(
		'$1$2 fn n$3',
		'$1 itemprop="url"$2'
	);

	return preg_replace( $pattern, $replace, $link );
}

/**
 * Adds microdata to the comment reply link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $link
 * @return string
 */
function hybrid_comment_reply_link_filter( $link ) {
	return preg_replace( '/(<a\s)/i', '$1itemprop="replyToUrl" ', $link );
}

/**
 * Adds microdata to avatars.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $avatar
 * @return string
 */
function hybrid_get_avatar( $avatar ) {
	return preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $avatar );
}

/**
 * Adds microdata to the post thumbnail HTML.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $html
 * @return string
 */
function hybrid_post_thumbnail_html( $html ) {
	return function_exists( 'get_the_image' ) ? $html : preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $html );
}

/**
 * Adds microdata to the comments popup link.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $attr
 * @return string
 */
function hybrid_comments_popup_link_attributes( $attr ) {
	return 'itemprop="discussionURL"';
}
