<?php
/**
 * Functions for handling media (i.e., attachments) within themes.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add all image sizes to the image editor to insert into post. */
add_filter( 'image_size_names_choose', 'hybrid_image_size_names_choose' );

/* Adds ID3 tags for media display. */
add_filter( 'wp_get_attachment_id3_keys', 'hybrid_attachment_id3_keys', 5, 3 );

/* Filters for the audio transcript. */
add_filter( 'hybrid_audio_transcript', 'wptexturize',   10 );
add_filter( 'hybrid_audio_transcript', 'convert_chars', 20 );
add_filter( 'hybrid_audio_transcript', 'wpautop',       25 );

/**
 * Adds theme "post-thumbnail" size plus an internationalized version of the image size name to the 
 * "add media" modal.  This allows users to insert the image within their post content editor.
 *
 * @since  1.3.0
 * @access public
 * @param  array   $sizes  Selectable image sizes.
 * @return array
 */
function hybrid_image_size_names_choose( $sizes ) {

	/* If the theme as set a custom post thumbnail size, give it a nice name. */
	if ( has_image_size( 'post-thumbnail' ) )
		$sizes['post-thumbnail'] = esc_html__( 'Post Thumbnail', 'hybrid-core' );

	/* Return the image size names. */
	return $sizes;
}

/**
 * Creates custom labels for ID3 tags that are used on the front end of the site when displaying 
 * media within the theme, typically on attachment pages.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $fields
 * @param  object  $attachment
 * @param  string  $context
 * @return array
 */
function hybrid_attachment_id3_keys( $fields, $attachment, $context ) {

	if ( 'display' === $context ) {

		$fields['filesize']         = esc_html__( 'File Size', 'hybrid-core' );
		$fields['mime_type']        = esc_html__( 'Mime Type', 'hybrid-core' );
		$fields['length_formatted'] = esc_html__( 'Run Time',  'hybrid-core' );
	}

	if ( hybrid_attachment_is_audio( $attachment->ID ) ) {

		$fields['genre']        = esc_html__( 'Genre',    'hybrid-core' );
		$fields['year']         = esc_html__( 'Year',     'hybrid-core' );
		$fields['composer']     = esc_html__( 'Composer', 'hybrid-core' );
		$fields['track_number'] = esc_html__( 'Track',    'hybrid-core' );

		if ( 'display' === $context )
			$fields['unsynchronised_lyric'] = esc_html__( 'Lyrics', 'hybrid-core' );
	}

	return $fields;
}
