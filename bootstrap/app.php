<?php
/**
 * App bootstrap.
 *
 * This file bootstraps the framework.  It sets up the single, one-true instance
 * of the app, which can be accessed via the `app()` function.  The file is
 * used to configure any "global" configuration and load any functions-files
 * that are needed for the theme.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * The single instance of the app. Use this function for quickly working
 * with data.  Returns an instance of the `Container` class.
 *
 * @since  1.0.0
 * @access public
 * @return object
 */
function app() {

	static $app = null;

	if ( is_null( $app ) ) {
		$app = new Container();
	}

	return $app;
}

// Add configuration.
app()->add( 'config.app', function( $container ) {

	return new Collection( require_once( trailingslashit( realpath( trailingslashit( __DIR__ ) . '../' ) ) . 'config/app.php' ) );
} );

app()->add( 'config.view', function( $container ) {

	return new Collection( require_once( $container->dir . 'config/view.php' ) );
} );

// Use the theme namespace as the overall app namespace.
app()->add( 'namespace', app()->get( 'config.theme' )->namespace );

app()->add( 'dir', app()->get( 'config.app' )->dir );
app()->add( 'uri', app()->get( 'config.app' )->uri );

app()->add( 'parent_dir', trailingslashit( get_template_directory() ) );
app()->add( 'child_dir', trailingslashit( get_stylesheet_directory() ) );

app()->add( 'parent_uri', trailingslashit( get_template_directory_uri() ) );
app()->add( 'child_uri', trailingslashit( get_stylesheet_directory_uri() ) );

app()->add( 'parent_textdomain', '' );
app()->add( 'child_textdomain', '' );
app()->add( 'comment_templates', [] );

app()->add( 'templates', function( $container ) {

	return new Collection();
} );

app()->add( 'layouts', function( $container ) {

	return new Collection();
} );

app()->add( 'media_meta', function( $container ) {

	return new Collection();
} );

app()->add( 'template_hierarchy', function() {

	return new TemplateHierarchy();
} );

// Resolve.
app()->get( 'template_hierarchy' );

// Load functions files.
array_map(
	function( $file ) {
		require_once( app()->dir . "app/{$file}.php" );
	},
	// Add file names of files to auto-load from the `/app` folder.
	// Classes are auto-loaded, so we only need this for functions-files.
	[
		'class-registry',
		'functions-attr',
		'functions-context',
		'functions-customize',
		'functions-deprecated',
		'functions-filters',
		'functions-fonts',
		'functions-formats',
		'functions-head',
		'functions-i18n',
		'functions-layouts',
		'functions-meta',
		'functions-scripts',
		'functions-setup',
		'functions-sidebars',
		'functions-styles',
		'functions-templates',
		'functions-utility',
		'template-comments',
		'template-general',
		'template-media',
		'template-post',
		'template'
	]
);

// Load admin files.
if ( is_admin() ) {
	require_once( app()->dir . 'admin/functions-admin.php' );
}

// Runs after the app has been bootstrapped.
do_action( app()->namespace . '/app_bootstrapped', app() );
