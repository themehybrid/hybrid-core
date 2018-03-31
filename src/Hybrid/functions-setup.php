<?php

namespace Hybrid;

/**
 * Returns a view object.
 *
 * @since  1.0.0
 * @access public
 * @param  string        $name
 * @param  array|string  $slugs
 * @param  array         $data
 * @return object
 */
function view( $name, $slugs = [], $data = [] ) {

	return new View( $name, $slugs, new Collection( $data ) );
}

/**
 * Outputs a view template.
 *
 * @since  1.0.0
 * @access public
 * @param  string        $name
 * @param  array|string  $slugs
 * @param  array         $data
 * @return void
 */
function render_view( $name, $slugs = [], $data = [] ) {

	view( $name, $slugs, $data )->render();
}

/**
 * Returns a view template as a string.
 *
 * @since  1.0.0
 * @access public
 * @param  string        $name
 * @param  array|string  $slugs
 * @param  array         $data
 * @return string
 */
function fetch_view( $name, $slugs = [], $data = [] ) {

	return view( $name, $slugs, $data )->fetch();
}

/**
 * Filters an array of templates and prefixes them with the
 * `/resources/views/` file path.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $templates
 * @return array
 */
function filter_templates( $templates ) {

	array_walk( $templates, function( &$template, $key ) {

		$path = config( 'view' )->path;

		$template = ltrim( str_replace( $path, '', $template ), '/' );

		$template = "{$path}/{$template}";
	} );

	return $templates;
}

/**
 * Returns a configuration object.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return object
 */
function config( $name = '' ) {

	return $name ? app()->config->$name : app()->config;
}

/**
 * Wrapper function for the `Collection` class.
 *
 * @since  1.0.0
 * @access public
 * @param  array   $items
 * @return object
 */
function collect( $items = [] ) {

	return new \Hybrid\Core\Collection( $items );
}

/**
 * Returns a new `Pagination` object.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $args
 * @return object
 */
function pagination( $args = [] ) {

	return new Pagination( $args );
}

/**
 * Outputs the posts pagination.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function posts_pagination( $args = [] ) {

	echo pagination( $args )->fetch();
}

/**
 * Single post pagination. This is a replacement for `wp_link_pages()`
 * using our `Pagination` class.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $args
 * @global int    $page
 * @global int    $numpages
 * @global bool   $multipage
 * @global bool   $more
 * @global object $wp_rewrite
 * @return void
 */
function singular_pagination( $args = [] ) {
	global $page, $numpages, $multipage, $more, $wp_rewrite;

	if ( ! $multipage ) {
		return;
	}

	$url_parts = explode( '?', html_entity_decode( get_permalink() ) );
	$base      = trailingslashit( $url_parts[0] ) . '%_%';

	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $base, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( '%#%' ) : '?page=%#%';

	$args = (array) $args + [
		'base'    => $base,
		'format'  => $format,
		'current' => ! $more && 1 === $page ? 0 : $page,
		'total'   => $numpages
	];

	echo pagination( $args )->fetch();
}

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
