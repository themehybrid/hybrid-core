<?php
/**
 * Post format functions.
 *
 * Functions and filters for handling the output of post formats.  This file is
 * only loaded if themes declare support for `post-formats`.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Add support for structured post formats.
add_action( 'wp_loaded', __NAMESPACE__ . '\structured_post_formats', 0 );

/**
 * Theme compatibility for post formats.  This function adds appropriate filters to 'the_content' for
 * the various post formats that a theme supports.
 *
 * @since  1.6.0
 * @access public
 * @return void
 */
function structured_post_formats() {

	// Add infinity symbol to aside posts.
	if ( current_theme_supports( 'post-formats', 'aside' ) ) {

		add_filter( 'the_content', __NAMESPACE__ . '\aside_infinity', 9 ); // run before wpautop
	}

	// Adds the link to the content if it's not in the post.
	if ( current_theme_supports( 'post-formats', 'link' ) ) {

		add_filter( 'the_content', __NAMESPACE__ . '\link_content', 9 ); // run before wpautop
	}

	// Wraps `<blockquote>` around quote posts.
	if ( current_theme_supports( 'post-formats', 'quote' ) ) {

		add_filter( 'the_content', __NAMESPACE__ . '\quote_content' );
	}
}

/**
 * Strips the 'post-format-' prefix from a post format (term) slug.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $slug
 * @return string
 */
function clean_post_format_slug( $slug ) {

	return str_replace( 'post-format-', '', $slug );
}

/* === Asides === */

/**
 * Adds an infinity character "&#8734;" to the end of the post content on
 * 'aside' posts.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return string
 */
function aside_infinity( $content ) {

	if ( has_post_format( 'aside' ) && ! is_singular() && ! post_password_required() ) {

		$content .= apply_filters(
			app()->namespace . '/aside_infinity',
			sprintf( ' <a class="permalink" href="%s">&#8734;</a>', esc_url( get_permalink() ) )
		);
	}

	return $content;
}

/* === Links === */

/**
 * Filters the content of the link format posts.  Wraps the content in the
 * `make_clickable()` function so that users can enter just a URL into the post
 * content editor.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return string
 */
function link_content( $content ) {

	if ( has_post_format( 'link' ) && ! post_password_required() && ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', $content ) ) {

		$content = make_clickable( $content );
	}

	return $content;
}

/* === Quotes === */

/**
 * Checks if the quote post has a `<blockquote>` tag within the content.  If not,
 * wraps the entire post content with one.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return string
 */
function quote_content( $content ) {

	if ( has_post_format( 'quote' ) && ! post_password_required() ) {
		preg_match( '/<blockquote.*?>/', $content, $matches );

		if ( empty( $matches ) ) {
			$content = "<blockquote>{$content}</blockquote>";
		}
	}

	return $content;
}
