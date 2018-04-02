<?php

namespace Hybrid;

/**
 * Adds theme support for features that themes should be supporting.  Also, removes
 * theme supported features from themes in the case that a user has a plugin installed
 * that handles the functionality.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
add_action( 'after_setup_theme', function() {

	// Automatically add <title> to head.
	add_theme_support( 'title-tag' );

	// Adds core WordPress HTML5 support.
	add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

	// Remove support for the the Breadcrumb Trail extension if the plugin is installed.
	if ( function_exists( 'breadcrumb_trail' ) || class_exists( 'Breadcrumb_Trail' ) )
		remove_theme_support( 'breadcrumb-trail' );

	// Remove support for the the Cleaner Gallery extension if the plugin is installed.
	if ( function_exists( 'cleaner_gallery' ) || class_exists( 'Cleaner_Gallery' ) )
		remove_theme_support( 'cleaner-gallery' );

	// Remove support for the the Get the Image extension if the plugin is installed.
	if ( function_exists( 'get_the_image' ) || class_exists( 'Get_The_Image' ) )
		remove_theme_support( 'get-the-image' );

}, 15 );

/**
 * Load extensions (external projects).  Extensions are projects that are included
 * within the framework but are not a part of it.  They are external projects
 * developed outside of the framework.  Themes must use `add_theme_support( $extension )`
 * to use a specific extension within the theme.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
 add_action( 'after_setup_theme', function() {

	require_if_theme_supports( 'breadcrumb-trail', path( 'ext/breadcrumb-trail.php' ) );
	require_if_theme_supports( 'cleaner-gallery',  path( 'ext/cleaner-gallery.php'  ) );
	require_if_theme_supports( 'get-the-image',    path( 'ext/get-the-image.php'    ) );

}, 20 );
