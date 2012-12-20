<?php
/**
 * Post Format Tools - A mini library for formatting post formats.
 *
 * Post Format Tools has functions and filters for handling the output of post formats.  This library 
 * helps theme developers format posts with given post formats in a more standardized fashion.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   PostFormatTools
 * @version   0.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// @todo Instead of creating a new row with the chat format when the same speaker:
//	- we can add to an array of $chat_rows[].  This way, we can remove the last item.
//	- or something like that. The basic idea is to attribute that text better to the 
//	- previous speaker.

// @todo We need to make sure to 'break;' out of the loop in the video format code. Check 
//	- this against multiple vidoes in a single post.

/* Filter the post format archive title. */
add_filter( 'single_term_title', 'post_format_tools_single_term_title' );

/* Filter the content of chat posts. */
add_filter( 'the_content', 'post_format_tools_chat_content' );

/* Auto-add paragraphs to the chat text. */
add_filter( 'post_format_chat_text', 'wpautop' );

/* Wraps <blockquote> around quote posts. */
add_filter( 'the_content', 'post_format_tools_quote_content' );

/* Makes URLs in link posts clickable. */
add_filter( 'the_content', 'post_format_tools_link_content' );

/**
 * Filters the single post format title, which is used on the term archive page. The purpose of this 
 * function is to replace the singular name with a plural version.
 *
 * @since 0.1.0
 * @access public
 * @param string $title The term name.
 * @return string
 */
function post_format_tools_single_term_title( $title ) {

	if ( is_tax( 'post_format' ) ) {
		$term = get_queried_object();
		$plural = post_format_tools_get_plural_string( $term->slug );
		$title = !empty( $plural ) ? $plural : $title;
	}

	return $title;
}

/**
 * Gets the plural version of a post format name.
 *
 * @since 0.1.0
 * @access public
 * @param string $slug The term slug.
 * @return string
 */
function post_format_tools_get_plural_string( $slug ) {

	$strings = post_format_tools_get_plural_strings();

	$slug = str_replace( 'post-format-', '', $slug );

	return isset( $strings[ $slug ] ) ? $strings[ $slug ] : '';
}

/**
 * Defines plural versions of the post format names since WordPress only provides a singular version 
 * of each format. Basically, I hate having archive pages labeled with the singular name, so this is 
 * what I created to take care of that problem.
 *
 * @since 0.1.0
 * @access public
 * @return array
 */
function post_format_tools_get_plural_strings() {

	$strings = array(
	//	'standard' => __( 'Articles',       'post-format-tools' ), // Would this ever be used?
		'aside'    => __( 'Asides',         'post-format-tools' ),
		'audio'    => __( 'Audio',          'post-format-tools' ), // Leave as "Audio"?
		'chat'     => __( 'Chats',          'post-format-tools' ),
		'image'    => __( 'Images',         'post-format-tools' ),
		'gallery'  => __( 'Galleries',      'post-format-tools' ),
		'link'     => __( 'Links',          'post-format-tools' ),
		'quote'    => __( 'Quotes',         'post-format-tools' ), // Use "Quotations"?
		'status'   => __( 'Status Updates', 'post-format-tools' ),
		'video'    => __( 'Videos',         'post-format-tools' ),
	);

	return apply_filters( 'post_format_tools_plural_strings', $strings );
}

/**
 * Checks if a post has any content. Useful if you need to check if the user has written any content 
 * before performing any actions.
 *
 * @since 0.1.0
 * @access public
 * @param int $id The ID of the post.
 * @return bool Whether the post has content.
 */
function post_format_tools_post_has_content( $id = 0 ) {
	$post = get_post( $id );
	return ( !empty( $post->post_content ) ? true : false );
}

/**
 * Wraps the output of the quote post format content in a <blockquote> element if the user hasn't added a 
 * <blockquote> in the post editor.
 *
 * @since 0.1.0
 * @access public
 * @param string $content The post content.
 * @return string $content
 */
function post_format_tools_quote_content( $content ) {

	if ( has_post_format( 'quote' ) ) {
		preg_match( '/<blockquote.*?>/', $content, $matches );

		if ( empty( $matches ) )
			$content = "<blockquote>{$content}</blockquote>";
	}

	return $content;
}

/**
 * Filters the content of the link format posts.  Wraps the content in the make_clickable() function 
 * so that users can enter just a URL into the post content editor.
 *
 * @since 0.1.0
 * @access public
 * @param string $content The post content.
 * @return string $content
 */
function post_format_tools_link_content( $content ) {

	if ( has_post_format( 'link' ) )
		$content = make_clickable( $content );

	return $content;
}

/**
 * Grabs the first URL from the post content of the current post.  This is meant to be used with the link post 
 * format to easily find the link for the post. 
 *
 * @note This is a modified version of the twentyeleven_url_grabber() function in the TwentyEleven theme.
 * @author wordpressdotorg
 * @copyright Copyright (c) 2011, wordpressdotorg
 * @link http://wordpress.org/extend/themes/twentyeleven
 * @license http://wordpress.org/about/license
 *
 * @since 0.1.0
 * @access public
 * @return string The link if found.  Otherwise, the permalink to the post.
 */
function post_format_tools_url_grabber() {

	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( get_the_content() ), $matches ) )
		return get_permalink( get_the_ID() );

	return esc_url_raw( $matches[1] );
}

/**
 * Returns the number of images attached to the current post in the loop.
 *
 * @since 0.1.0
 * @access public
 * @return int
 */
function post_format_tools_get_image_attachment_count() {
	$images = get_children( array( 'post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => -1 ) );
	return count( $images );
}

/**
 * Strips the 'post-format-' prefix from a post format (term) slug.
 *
 * @since 0.1.0
 * @access public
 * @param string $slug The slug of the post format.
 * @return string
 */
function post_format_tools_clean_post_format_slug( $slug ) {
	return str_replace( 'post-format-', '', $slug );
}

/**
 * This function filters the post content when viewing a post with the "chat" post format.  It formats 
 * the content with structured HTML markup to make it easy for theme developers to style chat posts. 
 * The advantage of this solution is that it allows for more than two speakers (like most solutions). 
 * You can have 100s of speakers in your chat post, each with their own, unique classes for styling.
 *
 * @author David Chandra <david.warna@gmail.com>
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012
 * @link http://justintadlock.com/archives/2012/08/21/post-formats-chat
 *
 * @since 0.1.0
 * @access public
 * @global array $_post_format_chat_ids An array of IDs for the chat rows based on the author.
 * @param string $content The content of the post.
 * @return string $chat_output The formatted content of the post.
 */
function post_format_tools_chat_content( $content ) {
	global $_post_format_chat_ids;

	/* If this is not a 'chat' post, return the content. */
	if ( !has_post_format( 'chat' ) )
		return $content;

	/* Set the global variable of speaker IDs to a new, empty array for this chat. */
	$_post_format_chat_ids = array();
	$chat_author = '';
	$speaker_id = 0;

	/* Allow the separator (separator for speaker/text) to be filtered. */
	$separator = apply_filters( 'post_format_chat_separator', ':' );

	/* Open the chat transcript div and give it a unique ID based on the post ID. */
	$chat_output = "\n\t\t\t" . '<div id="chat-transcript-' . esc_attr( get_the_ID() ) . '" class="chat-transcript">';

	/* Split the content to get individual chat rows. */
	$chat_rows = preg_split( "/(\r?\n)+|(<br\s*\/?>\s*)+/", $content );

	/* Loop through each row and format the output. */
	foreach ( $chat_rows as $chat_row ) {

		/* If a speaker is found, create a new chat row with speaker and text. */
		if ( preg_match( '/(?<!http|https)' . $separator . '/', $chat_row ) ) {

			/* Split the chat row into author/text. */
			$chat_row_split = explode( $separator, trim( $chat_row ), 2 );

			/* Get the chat author and strip tags. */
			$chat_author = strip_tags( trim( $chat_row_split[0] ) );

			/* Get the chat text. */
			$chat_text = trim( $chat_row_split[1] );

			/* Get the chat row ID (based on chat author) to give a specific class to each row for styling. */
			$speaker_id = post_format_tools_chat_row_id( $chat_author );

			/* Open the chat row. */
			$chat_output .= "\n\t\t\t\t" . '<div class="chat-row ' . sanitize_html_class( "chat-speaker-{$speaker_id}" ) . '">';

			/* Add the chat row author. */
			$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-author ' . sanitize_html_class( strtolower( "chat-author-{$chat_author}" ) ) . ' vcard"><cite class="fn">' . apply_filters( 'post_format_chat_author', $chat_author, $speaker_id ) . '</cite>' . $separator . '</div>';

			/* Add the chat row text. */
			$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-text">' . str_replace( array( "\r", "\n", "\t" ), '', apply_filters( 'post_format_chat_text', $chat_text, $chat_author, $speaker_id ) ) . '</div>';

			/* Close the chat row. */
			$chat_output .= "\n\t\t\t\t" . '</div><!-- .chat-row -->';
		}

		/**
		 * If no author is found, assume this is a separate paragraph of text that belongs to the
		 * previous speaker and label it as such, but let's still create a new row.
		 */
		else {

			/* Make sure we have text. */
			if ( !empty( $chat_row ) ) {

				/* Open the chat row. */
				$chat_output .= "\n\t\t\t\t" . '<div class="chat-row ' . sanitize_html_class( "chat-speaker-{$speaker_id}" ) . '">';

				/* Don't add a chat row author.  The label for the previous row should suffice. */

				/* Add the chat row text. */
				$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-text">' . str_replace( array( "\r", "\n", "\t" ), '', apply_filters( 'post_format_chat_text', $chat_row, $chat_author, $speaker_id ) ) . '</div>';

				/* Close the chat row. */
				$chat_output .= "\n\t\t\t</div><!-- .chat-row -->";
			}
		}
	}

	/* Close the chat transcript div. */
	$chat_output .= "\n\t\t\t</div><!-- .chat-transcript -->\n";

	/* Return the chat content and apply filters for developers. */
	return apply_filters( 'post_format_chat_content', $chat_output );
}

/**
 * This function returns an ID based on the provided chat author name.  It keeps these IDs in a global 
 * array and makes sure we have a unique set of IDs.  The purpose of this function is to provide an "ID"
 * that will be used in an HTML class for individual chat rows so they can be styled.  So, speaker "John" 
 * will always have the same class each time he speaks.  And, speaker "Mary" will have a different class 
 * from "John" but will have the same class each time she speaks.
 *
 * @author David Chandra <david.warna@gmail.com>
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012
 * @link http://justintadlock.com/archives/2012/08/21/post-formats-chat
 *
 * @since 0.1.0
 * @access public
 * @global array $_post_format_chat_ids An array of IDs for the chat rows based on the author.
 * @param string $chat_author Author of the current chat row.
 * @return int The ID for the chat row based on the author.
 */
function post_format_tools_chat_row_id( $chat_author ) {
	global $_post_format_chat_ids;

	/* Let's sanitize the chat author to avoid craziness and differences like "John" and "john". */
	$chat_author = strtolower( strip_tags( $chat_author ) );

	/* Add the chat author to the array. */
	$_post_format_chat_ids[] = $chat_author;

	/* Make sure the array only holds unique values. */
	$_post_format_chat_ids = array_unique( $_post_format_chat_ids );

	/* Return the array key for the chat author and add "1" to avoid an ID of "0". */
	return absint( array_search( $chat_author, $_post_format_chat_ids ) ) + 1;
}

/**
 * Retrieves embedded videos from the post content.  This script only searches for embeds used by 
 * the WordPress embed functionality.
 *
 * @since 0.1.0
 * @access public
 * @global object $wp_embed The global WP_Embed object.
 * @param array $args Arguments for the [embed] shortcode.
 * @return string
 */
function post_format_tools_get_video( $args = array() ) {
	global $wp_embed;

	/* If this is not a 'video' post, return. */
	if ( !has_post_format( 'video' ) )
		return false;

	/* Merge the input arguments and the defaults. */
	$args = wp_parse_args( $args, wp_embed_defaults() );

	/* Get the post content. */
	$content = get_the_content();

	/* Set the default $embed variable to false. */
	$embed = false;

	/* Use WP's built in WP_Embed class methods to handle the dirty work. */
	add_filter( 'post_format_tools_video_shortcode_embed', array( $wp_embed, 'run_shortcode' ) );
	add_filter( 'post_format_tools_video_auto_embed', array( $wp_embed, 'autoembed' ) );

	/* We don't want to return a link when an embed doesn't work.  Filter this to return false. */
	add_filter( 'embed_maybe_make_link', '__return_false' );

	/* Check for matches against the [embed] shortcode. */
	preg_match_all( '|\[embed.*?](.*?)\[/embed\]|i', $content, $matches, PREG_SET_ORDER );

	/* If matches were found, loop through them to see if we can hit the jackpot. */
	if ( is_array( $matches ) ) {
		foreach ( $matches  as $value ) {

			/* Apply filters (let WP handle this) to get an embedded video. */
			$embed = apply_filters( 'post_format_tools_video_shortcode_embed', '[embed width="' . absint( $args['width'] ) . '" height="' . absint( $args['height'] ) . '"]' . $value[1]. '[/embed]' );

			/* If no embed, continue looping through the array of matches. */
			if ( empty( $embed ) )
				continue;
		}
	}

	/* If no embed at this point and the user has 'auto embeds' turned on, let's check for URLs in the post. */
	if ( empty( $embed ) && get_option( 'embed_autourls' ) ) {
		preg_match_all( '|^\s*(https?://[^\s"]+)\s*$|im', $content, $matches, PREG_SET_ORDER );

		/* If URL matches are found, loop through them to see if we can get an embed. */
		if ( is_array( $matches ) ) {
			foreach ( $matches  as $value ) {

				/* Let WP work its magic with the 'autoembed' method. */
				$embed = apply_filters( 'post_format_tools_video_auto_embed', $value[0] );

				/* If no embed, continue looping through the array of matches. */
				if ( empty( $embed ) )
					continue;
			}
		}
	}

	/* Remove the maybe make link filter. */
	remove_filter( 'embed_maybe_make_link', '__return_false' );

	/* Return the embed. */
	return $embed;
}

?>