<?php
/**
 * Functions and filters for handling the output of post formats.  Most of this file is for continuing the 
 * use of previous Hybrid Core functionality related to post formats as well as fixing the backwards-
 * compatibility issues that WordPress 3.6 created with its new post format functionality.
 *
 * This file is only loaded if themes declare support for 'post-formats'.  If a theme declares support for 
 * 'structured-post-formats', the content filters will not run for the individual formats that the theme 
 * supports.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add support for structured post formats. */
add_action( 'wp_loaded', 'hybrid_structured_post_formats', 1 );

/* Filter the post format archive title. */
add_filter( 'single_term_title', 'hybrid_single_post_format_title' );

/* Returns the post permalink if there's no post format URL. */
add_filter( 'get_the_post_format_url', 'hybrid_get_the_post_format_url', 10, 2 );

/**
 * If the theme hasn't declared support for 'structured-post-formats', the framework will take care of 
 * the functionality.  This function checks whether the theme supports individual formats.  The reason 
 * for using this feature over WordPress' built-in compat function is for backwards compatibility and 
 * supporting the features that most Hybrid Core users have been enjoying since post formats were first 
 * added to WordPress.
 *
 * @since  1.6.0
 * @access public
 * @return void
 */
function hybrid_structured_post_formats() {

	/* Add infinity symbol to aside posts. */
	if ( !current_theme_supports( 'structured-post-formats', 'aside' ) )
		add_filter( 'the_content', 'hybrid_aside_infinity', 9 ); // run before wpautop

	/* Add image to content if the user didn't add it. */
	if ( !current_theme_supports( 'structured-post-formats', 'image' ) )
		add_filter( 'the_content', 'hybrid_image_content' );

	/* Adds the link to the content if it's not in the post. */
	if ( !current_theme_supports( 'structured-post-formats', 'link' ) )
		add_filter( 'the_content', 'hybrid_link_content', 9 ); // run before wpautop

	/* Wraps <blockquote> around quote posts. */
	if ( !current_theme_supports( 'structured-post-formats', 'quote' ) ) {
		add_filter( 'the_content', 'hybrid_quote_content' );

		/* Filters the quote source format. */
		add_filter( 'quote_source_format', 'hybrid_quote_source_format' );
	}

	/* Filter the content of chat posts. */
	if ( !current_theme_supports( 'structured-post-formats', 'chat' ) ) {
		add_filter( 'the_content', 'hybrid_chat_content' );

		/* Auto-add paragraphs to the chat text. */
		add_filter( 'post_format_chat_text', 'wpautop' );
	}

	/* Add structured support for all post formats that the theme supports.  Let WP handle the rest. */
	$supports = get_theme_support( 'post-formats' );
	$formats  = isset( $supports[0] ) ? $supports[0] : array();

	if ( !empty( $formats ) )
		add_theme_support( 'structured-post-formats', array_intersect( $formats, get_post_format_slugs() ) );
}

/**
 * Filters the single post format title, which is used on the term archive page. The purpose of this 
 * function is to replace the singular name with a plural version.
 *
 * @since  1.6.0
 * @access public
 * @param  string $title The term name.
 * @return string
 */
function hybrid_single_post_format_title( $title ) {

	if ( is_tax( 'post_format' ) ) {
		$term   = get_queried_object();
		$plural = hybrid_get_plural_post_format_string( $term->slug );
		$title  = !empty( $plural ) ? $plural : $title;
	}

	return $title;
}

/**
 * Gets the plural version of a post format name.
 *
 * @since  1.6.0
 * @access public
 * @param  string $slug The term slug.
 * @return string
 */
function hybrid_get_plural_post_format_string( $slug ) {

	$strings = hybrid_get_plural_post_format_strings();

	$slug = hybrid_clean_post_format_slug( $slug );

	return isset( $strings[ $slug ] ) ? $strings[ $slug ] : '';
}

/**
 * Defines plural versions of the post format names since WordPress only provides a singular version 
 * of each format. Basically, I hate having archive pages labeled with the singular name, so this is 
 * what I created to take care of that problem.
 *
 * @since  1.6.0
 * @access public
 * @return array
 */
function hybrid_get_plural_post_format_strings() {

	$strings = array(
	//	'standard' => __( 'Articles',       'hybrid-core' ), // Would this ever be used?
		'aside'    => __( 'Asides',         'hybrid-core' ),
		'audio'    => __( 'Audio',          'hybrid-core' ), // Leave as "Audio"?
		'chat'     => __( 'Chats',          'hybrid-core' ),
		'image'    => __( 'Images',         'hybrid-core' ),
		'gallery'  => __( 'Galleries',      'hybrid-core' ),
		'link'     => __( 'Links',          'hybrid-core' ),
		'quote'    => __( 'Quotes',         'hybrid-core' ), // Use "Quotations"?
		'status'   => __( 'Status Updates', 'hybrid-core' ),
		'video'    => __( 'Videos',         'hybrid-core' ),
	);

	return apply_filters( 'hybrid_plural_post_format_strings', $strings );
}

/**
 * Strips the 'post-format-' prefix from a post format (term) slug.
 *
 * @since  1.6.0
 * @access public
 * @param  string $slug The slug of the post format.
 * @return string
 */
function hybrid_clean_post_format_slug( $slug ) {
	return str_replace( 'post-format-', '', $slug );
}

/**
 * Checks if a post has any content. Useful if you need to check if the user has written any content 
 * before performing any actions.
 *
 * @since  1.6.0
 * @access public
 * @param  int    $id  The ID of the post.
 * @return bool
 */
function hybrid_post_has_content( $id = 0 ) {
	$post = get_post( $id );
	return ( !empty( $post->post_content ) ? true : false );
}

/* === Asides === */

/**
 * Adds an infinity character "&#8734;" to the end of the post content on 'aside' posts.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content The post content.
 * @return string $content
 */
function hybrid_aside_infinity( $content ) {

	if ( has_post_format( 'aside' ) && !is_singular() )
		$content .= ' <a class="permalink" href="' . get_permalink() . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">&#8734;</a>';

	return $content;
}

/* === Galleries === */

/**
 * Returns the number of images displayed by the gallery or galleries in a post.
 *
 * @since  1.6.0
 * @access public
 * @return int
 */
function hybrid_get_gallery_image_count() {

	/* Set up an empty array for images. */
	$images = array();

	/* Get the images from all post galleries. */
	$galleries = get_post_galleries_images();

	/* Merge each gallery image into a single array. */
	foreach ( $galleries as $gallery_images )
		$images = array_merge( $images, $gallery_images );

	/* If there are no images in the array, just grab the attached images. */
	if ( empty( $images ) ) {
		$images = get_posts( 
			array( 
				'fields'         => 'ids',
				'post_parent'    => get_the_ID(), 
				'post_type'      => 'attachment', 
				'post_mime_type' => 'image', 
				'numberposts'    => -1 
			) 
		);
	}

	/* Return the count of the images. */
	return count( $images );
}

/* === Images === */

/**
 * Adds the post format image to the content if no image is found in the post content.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $content
 * @return string
 */
function hybrid_image_content( $content ) {

	if ( has_post_format( 'image' ) ) {
		preg_match( '/<img.*?>/', $content, $matches );

		if ( empty( $matches ) )
			$content = get_the_post_format_image() . $content;
	}

	return $content;
}

/* === Links === */

/**
 * Gets a URL from the content, even if it's not wrapped in an <a> tag.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $content
 * @return string
 */
function hybrid_get_content_url( $content ) {

	/* Catch links that are not wrapped in an '<a>' tag. */
	preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( $content ), $matches );

	return !empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
}

/**
 * Filters 'get_the_post_format_url' to make for a more robust and back-compatible function.  If WP did 
 * not find a URL, check the post content for one.  If nothing is found, return the post permalink.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $url
 * @param  object  $post
 * @return string
 */
function hybrid_get_the_post_format_url( $url, $post ) {

	if ( empty( $url ) ) {

		$content_url = hybrid_get_content_url( $post->post_content );

		$url = !empty( $content_url ) ? $content_url : get_permalink( $post->ID );
	}

	return $url;
}

/**
 * Filters the content of the link format posts.  Wraps the content in the make_clickable() function 
 * so that users can enter just a URL into the post content editor.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content The post content.
 * @return string $content
 */
function hybrid_link_content( $content ) {

	if ( has_post_format( 'link' ) ) {
		if ( !preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( $content ) ) )
			$content = get_the_post_format_url() . $content;
	}

	return $content;
}

/* === Quotes === */

/**
 * Replaces the post content with an overly-complicated post quote by WP.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content The post content.
 * @return string $content
 */
function hybrid_quote_content( $content ) {

	if ( has_post_format( 'quote' ) )
		$content = get_the_post_format_quote();

	return $content;
}

/**
 * Formats the output of the quote and quote source.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $format
 * @return string
 */
function hybrid_quote_source_format( $format ) {
	return "%s";
}

/* === Chats === */

/**
 * This function filters the post content when viewing a post with the "chat" post format.  It formats 
 * the content with structured HTML markup to make it easy for theme developers to style chat posts. 
 * The advantage of this solution is that it allows for more than two speakers (like most solutions). 
 * You can have 100s of speakers in your chat post, each with their own, unique classes for styling.
 *
 * @author    David Chandra <david.warna@gmail.com>
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012
 * @link      http://justintadlock.com/archives/2012/08/21/post-formats-chat
 *
 * @since  1.6.0
 * @access public
 * @global array   $_post_format_chat_ids  An array of IDs for the chat rows based on the author.
 * @param  string  $content                The content of the post.
 * @return string  $chat_output            The formatted content of the post.
 */
function hybrid_chat_content( $content ) {

	if ( !has_post_format( 'chat' ) )
		return $content;

	/* Open the chat transcript div and give it a unique ID based on the post ID. */
	$chat_output = "\n\t\t\t" . '<div id="chat-transcript-' . esc_attr( get_the_ID() ) . '" class="chat-transcript">';

	/* Allow the separator (separator for speaker/text) to be filtered. */
	$separator = apply_filters( 'post_format_chat_separator', ':' );

	/* Get the stanzas from the post content. */
	$stanzas = get_the_post_format_chat();

	/* Loop through the stanzas that were returned. */
	foreach ( $stanzas as $stanza ) {

		/* Loop through each row of the stanza and format. */
		foreach ( $stanza as $row ) {

			/* Get the chat author and message. */
			$chat_author = $row['author'];
			$chat_text = $row['message'];

			/* Get the speaker/row ID. */
			$speaker_id = hybrid_chat_row_id( $chat_author );

			/* Format the time if there was one given. */
			$time = empty( $row['time'] ) ? '' : '<time class="chat-timestamp">' . esc_html( $row['time'] ) . '</time> ';

			/* Open the chat row. */
			$chat_output .= "\n\t\t\t\t" . '<div class="chat-row ' . sanitize_html_class( "chat-speaker-{$speaker_id}" ) . '">';

			/* Add the chat row author. */
			$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-author ' . sanitize_html_class( strtolower( "chat-author-{$chat_author}" ) ) . ' vcard">' . $time . '<cite class="fn">' . apply_filters( 'post_format_chat_author', $chat_author, $speaker_id ) . '</cite>:</div>';

			/* Add the chat row text. */
			$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-text">' . str_replace( array( "\r", "\n", "\t" ), '', apply_filters( 'post_format_chat_text', $chat_text, $chat_author, $speaker_id ) ) . '</div>';

			/* Close the chat row. */
			$chat_output .= "\n\t\t\t\t" . '</div><!-- .chat-row -->';
		}
	}

	/* Close the chat transcript div. */
	$chat_output .= "\n\t\t\t</div><!-- .chat-transcript -->\n";

	/* Return the chat content. */
	return $chat_output;
}


/**
 * This function returns an ID based on the provided chat author name.  It keeps these IDs in a global 
 * array and makes sure we have a unique set of IDs.  The purpose of this function is to provide an "ID"
 * that will be used in an HTML class for individual chat rows so they can be styled.  So, speaker "John" 
 * will always have the same class each time he speaks.  And, speaker "Mary" will have a different class 
 * from "John" but will have the same class each time she speaks.
 *
 * @author    David Chandra <david.warna@gmail.com>
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012
 * @link      http://justintadlock.com/archives/2012/08/21/post-formats-chat
 *
 * @since  1.6.0
 * @access public
 * @global array   $_post_format_chat_ids  An array of IDs for the chat rows based on the author.
 * @param  string  $chat_author            Author of the current chat row.
 * @return int                             The ID for the chat row based on the author.
 */
function hybrid_chat_row_id( $chat_author ) {
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

?>