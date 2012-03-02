<?php
/**
 * Functions file for loading scripts and stylesheets.  This file also handles the output of attachment files 
 * by displaying appropriate HTML elements for the attachments.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register Hybrid Core scripts. */
add_action( 'wp_enqueue_scripts', 'hybrid_register_scripts', 1 );

/* Load Hybrid Core scripts. */
add_action( 'wp_enqueue_scripts', 'hybrid_enqueue_scripts' );

/* Load the development stylsheet in script debug mode. */
add_filter( 'stylesheet_uri', 'hybrid_debug_stylesheet', 10, 2 );

/* Add all image sizes to the image editor to insert into post. */
add_filter( 'image_size_names_choose', 'hybrid_image_size_names_choose' );

/**
 * Registers JavaScript files for the framework.  This function merely registers scripts with WordPress using
 * the wp_register_script() function.  It does not load any script files on the site.  If a theme wants to register 
 * its own custom scripts, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since 1.2.0
 * @access private
 * @return void
 */
function hybrid_register_scripts() {

	/* Supported JavaScript. */
	$supports = get_theme_support( 'hybrid-core-javascript' );

	/* Register the 'drop-downs' script if the current theme supports 'hybrid-core-drop-downs'. */
	if ( current_theme_supports( 'hybrid-core-drop-downs' ) || ( isset( $supports[0] ) && in_array( 'drop-downs', $supports[0] ) ) )
		wp_register_script( 'drop-downs', esc_url( apply_atomic( 'drop_downs_script', trailingslashit( HYBRID_JS ) . 'drop-downs.js' ) ), array( 'jquery' ), '20110920', true );

	/* Register the 'nav-bar' script if the current theme supports 'hybrid-core-nav-bar'. */
	if ( isset( $supports[0] ) && in_array( 'nav-bar', $supports[0] ) )
		wp_register_script( 'nav-bar', esc_url( apply_atomic( 'nav_bar_script', trailingslashit( HYBRID_JS ) . 'nav-bar.js' ) ), array( 'jquery' ), '20111008', true );
}

/**
 * Tells WordPress to load the scripts needed for the framework using the wp_enqueue_script() function.
 *
 * @since 1.2.0
 * @access private
 * @return void
 */
function hybrid_enqueue_scripts() {

	/* Supported JavaScript. */
	$supports = get_theme_support( 'hybrid-core-javascript' );

	/* Load the comment reply script on singular posts with open comments if threaded comments are supported. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );

	/* Load the 'drop-downs' script if the current theme supports 'hybrid-core-drop-downs'. */
	if ( current_theme_supports( 'hybrid-core-drop-downs' ) || ( isset( $supports[0] ) && in_array( 'drop-downs', $supports[0] ) ) )
		wp_enqueue_script( 'drop-downs' );

	/* Load the 'nav-bar' script if the current theme supports 'hybrid-core-nav-bar'. */
	if ( isset( $supports[0] ) && in_array( 'nav-bar', $supports[0] ) )
		wp_enqueue_script( 'nav-bar' );
}

/**
 * Function for using a debug stylesheet when developing.  To develop with the debug stylesheet, 
 * SCRIPT_DEBUG must be set to 'true' in the 'wp-config.php' file.  This will check if a 'style.dev.css'
 * file is present within the theme folder and use it if it exists.  Else, it defaults to 'style.css'.
 *
 * @since 0.9.0
 * @access private
 * @param string $stylesheet_uri The URI of the active theme's stylesheet.
 * @param string $stylesheet_dir_uri The directory URI of the active theme's stylesheet.
 * @return string $stylesheet_uri
 */
function hybrid_debug_stylesheet( $stylesheet_uri, $stylesheet_dir_uri ) {

	/* If SCRIPT_DEBUG is set to true and the theme supports 'dev-stylesheet'. */
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && current_theme_supports( 'dev-stylesheet' ) ) {

		/* Remove the stylesheet directory URI from the file name. */
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		/* Change the stylesheet name to 'style.dev.css'. */
		$stylesheet = str_replace( '.css', '.dev.css', $stylesheet );

		/* If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet. */
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $stylesheet ) )
			$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . $stylesheet;
	}

	/* Return the theme stylesheet. */
	return $stylesheet_uri;
}

/**
 * Adds theme/plugin custom images sizes added with add_image_size() to the image uploader/editor.  This 
 * allows users to insert these images within their post content editor.
 *
 * @since 1.3.0
 * @access private
 * @param array $sizes Selectable image sizes.
 * @return array $sizes
 */
function hybrid_image_size_names_choose( $sizes ) {

	/* Get all intermediate image sizes. */
	$intermediate_sizes = get_intermediate_image_sizes();
	$add_sizes = array();

	/* Loop through each of the intermediate sizes, adding them to the $add_sizes array. */
	foreach ( $intermediate_sizes as $size )
		$add_sizes[$size] = $size;

	/* Merge the original array, keeping it intact, with the new array of image sizes. */
	$sizes = array_merge( $add_sizes, $sizes );

	/* Return the new sizes plus the old sizes back. */
	return $sizes;
}

/**
 * Loads the correct function for handling attachments.  Checks the attachment mime type to call 
 * correct function. Image attachments are not loaded with this function.  The functionality for them 
 * should be handled by the theme's attachment or image attachment file.
 *
 * Ideally, all attachments would be appropriately handled within their templates. However, this could 
 * lead to messy template files.
 *
 * @since 0.5.0
 * @access public
 * @uses get_post_mime_type() Gets the mime type of the attachment.
 * @uses wp_get_attachment_url() Gets the URL of the attachment file.
 * @return void
 */
function hybrid_attachment() {
	$file = wp_get_attachment_url();
	$mime = get_post_mime_type();
	$mime_type = explode( '/', $mime );

	/* Loop through each mime type. If a function exists for it, call it. Allow users to filter the display. */
	foreach ( $mime_type as $type ) {
		if ( function_exists( "hybrid_{$type}_attachment" ) )
			$attachment = call_user_func( "hybrid_{$type}_attachment", $mime, $file );

		$attachment = apply_atomic( "{$type}_attachment", $attachment );
	}

	echo apply_atomic( 'attachment', $attachment );
}

/**
 * Handles application attachments on their attachment pages.  Uses the <object> tag to embed media 
 * on those pages.
 *
 * @since 0.3.0
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_application_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();
	$application = '<object class="text" type="' . esc_attr( $mime ) . '" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
	$application .= '<param name="src" value="' . esc_url( $file ) . '" />';
	$application .= '</object>';

	return $application;
}

/**
 * Handles text attachments on their attachment pages.  Uses the <object> element to embed media 
 * in the pages.
 *
 * @since 0.3.0
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_text_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();
	$text = '<object class="text" type="' . esc_attr( $mime ) . '" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
	$text .= '<param name="src" value="' . esc_url( $file ) . '" />';
	$text .= '</object>';

	return $text;
}

/**
 * Handles audio attachments on their attachment pages.  Puts audio/mpeg and audio/wma files into 
 * an <object> element.
 *
 * @todo Test out and support more audio types.
 *
 * @since 0.2.2
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_audio_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();
	$audio = '<object type="' . esc_attr( $mime ) . '" class="player audio" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
		$audio .= '<param name="src" value="' . esc_url( $file ) . '" />';
		$audio .= '<param name="autostart" value="false" />';
		$audio .= '<param name="controller" value="true" />';
	$audio .= '</object>';

	return $audio;
}

/**
 * Handles video attachments on attachment pages.  Add other video types to the <object> element.
 *
 * @since 0.2.2
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_video_attachment( $mime = false, $file = false ) {
	$embed_defaults = wp_embed_defaults();

	if ( $mime == 'video/asf' )
		$mime = 'video/x-ms-wmv';

	$video = '<object type="' . esc_attr( $mime ) . '" class="player video" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
		$video .= '<param name="src" value="' . esc_url( $file ) . '" />';
		$video .= '<param name="autoplay" value="false" />';
		$video .= '<param name="allowfullscreen" value="true" />';
		$video .= '<param name="controller" value="true" />';
	$video .= '</object>';

	return $video;
}

?>