<?php
/**
 * Functions and filters for handling the output of post formats.  This file is only loaded if
 * themes declare support for `post-formats`.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add support for structured post formats.
add_action( 'wp_loaded', 'hybrid_structured_post_formats', 0 );

/**
 * Theme compatibility for post formats.  This function adds appropriate filters to 'the_content' for
 * the various post formats that a theme supports.
 *
 * @since  1.6.0
 * @access public
 * @return void
 */
function hybrid_structured_post_formats() {

	// Add infinity symbol to aside posts.
	if ( current_theme_supports( 'post-formats', 'aside' ) )
		add_filter( 'the_content', 'hybrid_aside_infinity', 9 ); // run before wpautop

	// Adds the link to the content if it's not in the post.
	if ( current_theme_supports( 'post-formats', 'link' ) )
		add_filter( 'the_content', 'hybrid_link_content', 9 ); // run before wpautop

	// Wraps `<blockquote>` around quote posts.
	if ( current_theme_supports( 'post-formats', 'quote' ) )
		add_filter( 'the_content', 'hybrid_quote_content' );

	// Filter the content of chat posts.
	if ( current_theme_supports( 'post-formats', 'chat' ) )
		add_filter( 'the_content', 'hybrid_chat_content', 9 ); // run before wpautop
}

/**
 * Strips the 'post-format-' prefix from a post format (term) slug.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $slug
 * @return string
 */
function hybrid_clean_post_format_slug( $slug ) {
	return str_replace( 'post-format-', '', $slug );
}

/* === Asides === */

/**
 * Adds an infinity character "&#8734;" to the end of the post content on 'aside' posts.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return string
 */
function hybrid_aside_infinity( $content ) {

	if ( has_post_format( 'aside' ) && ! is_singular() && ! post_password_required() )
		$content .= apply_filters( 'hybrid_aside_infinity', sprintf( ' <a class="permalink" href="%s">&#8734;</a>', esc_url( get_permalink() ) ) );

	return $content;
}

/* === Images === */

/**
 * Adds the post format image to the content if no image is found in the post content.  Note, this is not run
 * by default.  To use, add the filter to 'the_content'.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $content
 * @return string
 */
function hybrid_image_content( $content ) {

	if ( has_post_format( 'image' ) && ! post_password_required() ) {
		preg_match( '/<img.*?>/', $content, $matches );

		if ( empty( $matches ) && function_exists( 'get_the_image' ) )
			$content = get_the_image( array( 'meta_key' => false, 'size' => 'large', 'link' => false, 'echo' => false ) ) . $content;

		elseif ( empty( $matches ) )
			$content = get_the_post_thumbnail( get_the_ID(), 'large' ) . $content;
	}

	return $content;
}

/* === Links === */

/**
 * Filters the content of the link format posts.  Wraps the content in the `make_clickable()` function
 * so that users can enter just a URL into the post content editor.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return string
 */
function hybrid_link_content( $content ) {

	if ( has_post_format( 'link' ) && ! post_password_required() && ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', $content ) )
		$content = make_clickable( $content );

	return $content;
}

/* === Quotes === */

/**
 * Checks if the quote post has a <blockquote> tag within the content.  If not, wraps the entire post
 * content with one.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return string
 */
function hybrid_quote_content( $content ) {

	if ( has_post_format( 'quote' ) && ! post_password_required() ) {
		preg_match( '/<blockquote.*?>/', $content, $matches );

		if ( empty( $matches ) )
			$content = "<blockquote>{$content}</blockquote>";
	}

	return $content;
}

/* === Chats === */

/**
 * This function filters the post content when viewing a post with the "chat" post format.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $content
 * @return string
 */
function hybrid_chat_content( $content ) {
	return has_post_format( 'chat' ) && ! post_password_required() ? hybrid_get_chat_transcript( $content ) : $content;
}

/**
 * Gets a chat transcript.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $content
 * @return string
 */
function hybrid_get_chat_transcript( $content ) {
	$chat = new Hybrid_Chat( $content );

	return $chat->get_transcript();
}
