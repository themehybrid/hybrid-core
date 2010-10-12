<?php
/**
 * Functions file for loading scripts and styles. It also handles attachment files by 
 * displaying appropriate HTML element. Other media is handled through the theme 
 * extensions: get-the-image.php, get-the-object.php.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Function for using a debug stylesheet when developing.  To develop with the debug stylesheet, 
 * SCRIPT_DEBUG must be set to 'true' in the 'wp-config.php' file.  This will check if a 'style.dev.css'
 * file is present within the theme folder and use it if it exists.  Else, it defaults to 'style.css'.
 *
 * @since 0.9
 */
function hybrid_debug_stylesheet( $stylesheet_uri, $stylesheet_dir_uri ) {

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && current_theme_supports( 'dev-stylesheet' ) ) {
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );
		$stylesheet = str_replace( '.css', '.dev.css', $stylesheet );

		if ( file_exists( trailingslashit( STYLESHEETPATH ) . $stylesheet ) )
			$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . $stylesheet;
	}

	return $stylesheet_uri;
}

/**
 * Checks for a custom field called 'Stylesheet' for a CSS file name in the form of 'example.css'.
 *  If the file exists in the child theme '/css' folder, replace the normal 'style.css' used with this 
 * file for the singular view of the post.
 *
 * @since 0.9
 */
function hybrid_post_stylesheets( $stylesheet_uri, $stylesheet_dir_uri ) {
	global $wp_query;

	if ( is_singular() && current_theme_supports( 'post-stylesheets' ) ) {
		$stylesheet = get_post_meta( $wp_query->post->ID, 'Stylesheet', true );

		if ( !empty( $stylesheet ) && file_exists( get_stylesheet_directory() . "/css/{$stylesheet}" ) )
			$stylesheet_uri = $stylesheet_dir_uri . "/css/{$stylesheet}";
	}

	return $stylesheet_uri;
}

/**
 * Function to load CSS at an appropriate time. Adds print.css if user chooses to use it. 
 * Users should load their own CSS using wp_enqueue_style() in their child theme's 
 * functions.php file.
 *
 * @since 0.1
 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_style
 */
function hybrid_enqueue_style() {
	global $wp_query;

	/* If is admin, don't load styles. */
	if ( is_admin() )
		return;

	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	/* Load the print stylesheet. */
	if ( current_theme_supports( 'hybrid-core-print-style' ) )
		wp_enqueue_style( "{$prefix}-print", esc_url( apply_atomic( 'print_style', HYBRID_CSS . '/print.css' ) ), false, 0.7, 'print' );
}

/**
 * Function to load JavaScript at appropriate time. Loads comment reply script only if 
 * users choose to use nested comments. Users should load custom JavaScript with 
 * wp_enqueue_script() in their child theme's functions.php file.
 *
 * If selected, the drop-downs.js file will be loaded, which is a bundled version of the
 * Superfish jQuery plugin.
 *
 * @since 0.1
 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script
 * @link http://users.tpg.com.au/j_birch/plugins/superfish
 */
function hybrid_enqueue_script() {

	/* Don't load any scripts in the admin. */
	if ( is_admin() )
		return;

	/* Comment reply. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );

	/* Superfish drop-down menus. */
	if ( current_theme_supports( 'hybrid-core-drop-downs' ) )
		wp_enqueue_script( 'drop-downs', esc_url( apply_atomic( 'drop_downs_script', HYBRID_JS . '/drop-downs.js' ) ), array( 'jquery' ), 1.4, true );
}

/**
 * Loads the correct function for handling attachments. Checks the attachment mime 
 * type to call correct function. Image attachments are not loaded with this function.
 * The functionality for them resides in image.php.
 *
 * Ideally, all attachments would be appropriately handled within their templates. However, 
 * this could lead to messy template files. For now, we'll use separate functions for handling 
 * attachment content. The biggest issue here is with handling different video types.
 *
 * @since 0.5
 * @uses get_post_mime_type() Gets the mime type of the attachment.
 * @uses wp_get_attachment_url() Gets the URL of the attachment file.
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
 * Handles application attachments on their attachment pages.
 * Uses the <object> tag to embed media on those pages.
 *
 * @todo Run a battery of tests on many different applications.
 * @todo Figure out what to do with FLV files outside of the current functionality.
 *
 * @since 0.3
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_application_attachment( $mime = '', $file = '' ) {
	$application = '<object class="text" type="' . $mime . '" data="' . $file . '" width="400">';
	$application .= '<param name="src" value="' . $file . '" />';
	$application .= '</object>';

	return $application;
}

/**
 * Handles text attachments on their attachment pages.
 * Uses the <object> element to embed media in the pages.
 *
 * @since 0.3
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_text_attachment( $mime = '', $file = '' ) {
	$text = '<object class="text" type="' . $mime . '" data="' . $file . '" width="400">';
	$text .= '<param name="src" value="' . $file . '" />';
	$text .= '</object>';

	return $text;
}

/**
 * Handles audio attachments on their attachment pages.
 * Puts audio/mpeg and audio/wma files into an <object> element.
 *
 * @todo Test out and support more audio types.
 *
 * @since 0.2.2
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_audio_attachment( $mime = '', $file = '' ) {
	$audio = '<object type="' . $mime . '" class="player audio" data="' . $file . '" width="400" height="50">';
		$audio .= '<param name="src" value="' . $file . '" />';
		$audio .= '<param name="autostart" value="false" />';
		$audio .= '<param name="controller" value="true" />';
	$audio .= '</object>';

	return $audio;
}

/**
 * Handles video attachments on attachment pages.
 * Add other video types to the <object> element.
 *
 * In 0.6, FLV files were moved to using hybrid_application_attachment.
 *
 * @todo Test out and support more video types.
 *
 * @since 0.2.2
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_video_attachment( $mime = false, $file = false ) {
	if ( $mime == 'video/asf' )
		$mime = 'video/x-ms-wmv';

	$video = '<object type="' . $mime . '" class="player video" data="' . $file . '" width="400" height="320">';
		$video .= '<param name="src" value="' . $file . '" />';
		$video .= '<param name="autoplay" value="false" />';
		$video .= '<param name="allowfullscreen" value="true" />';
		$video .= '<param name="controller" value="true" />';
	$video .= '</object>';

	return $video;
}

?>