<?php
/**
 * Theme administration functions used with other components of the framework admin.  This file is for
 * setting up any basic features and holding additional admin helper functions.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Load the post meta boxes on the new post and edit post screens.
add_action( 'load-post.php',     'hybrid_admin_load_post_meta_boxes' );
add_action( 'load-post-new.php', 'hybrid_admin_load_post_meta_boxes' );

# Register scripts and styles.
add_action( 'admin_enqueue_scripts', 'hybrid_admin_register_styles',  0 );

# Allow posts page to be edited.
add_action( 'edit_form_after_title', 'hybrid_enable_posts_page_editor', 0 );

/**
 * Loads the core post meta box files on the 'load-post.php' action hook.  Each meta box file is only loaded if
 * the theme declares support for the feature.
 *
 * @since  1.2.0
 * @access public
 * @return void
 */
function hybrid_admin_load_post_meta_boxes() {

	// Load the layout meta box.
	require_if_theme_supports( 'theme-layouts', HYBRID_ADMIN . 'meta-box-post-layout.php' );

	// Load the post style meta box.
	require_once( HYBRID_ADMIN . 'meta-box-post-style.php' );
}

/**
 * Registers admin styles.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_admin_register_styles() {
	wp_register_style( 'hybrid-admin', HYBRID_CSS . 'admin.css' );
}

/**
 * Fix for users who want to display content on the posts page above the posts list, which is a
 * theme feature common to themes built from the framework.
 *
 * @since  3.0.0
 * @access public
 * @param  object  $post
 * @return void
 */
function hybrid_enable_posts_page_editor( $post ) {

	if ( get_option( 'page_for_posts' ) != $post->ID )
		return;

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( $post->post_type, 'editor' );
}

/**
 * Gets the stylesheet files within the parent or child theme and checks if they have the 'Style Name'
 * header. If any files are found, they are returned in an array.
 *
 * @since  3.0.0
 * @access public
 * @global object  $hybrid
 * @return array
 */
function hybrid_get_post_styles( $post_type = 'post' ) {
	global $hybrid;

	// If stylesheets have already been loaded, return them.
	if ( ! empty( $hybrid->post_styles ) && isset( $hybrid->post_styles[ $post_type ] ) )
		return $hybrid->post_styles[ $post_type ];

	// Set up an empty styles array.
	$hybrid->post_styles[ $post_type ] = array();

	// Get the theme CSS files two levels deep.
	$files = wp_get_theme( get_template() )->get_files( 'css', 2 );

	// If a child theme is active, get its files and merge with the parent theme files.
	if ( is_child_theme() )
		$files = array_merge( $files, wp_get_theme()->get_files( 'css', 2 ) );

	// Loop through each of the CSS files and check if they are styles.
	foreach ( $files as $file => $path ) {

		// Get file data based on the 'Style Name' header.
		$headers = get_file_data(
			$path,
			array(
				'Style Name'         => 'Style Name',
				"{$post_type} Style" => "{$post_type} Style"
			)
		);

		// Add the CSS filename and template name to the array.
		if ( ! empty( $headers['Style Name'] ) )
			$hybrid->post_styles[ $post_type ][ $file ] = $headers['Style Name'];

		elseif ( ! empty( $headers["{$post_type} Style"] ) )
			$hybrid->post_styles[ $post_type ][ $file ] = $headers["{$post_type} Style"];
	}

	// Flip the array of styles.
	$hybrid->post_styles[ $post_type ] = array_flip( $hybrid->post_styles[ $post_type ] );

	// Return array of styles.
	return $hybrid->post_styles[ $post_type ];
}
