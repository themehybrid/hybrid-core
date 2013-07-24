<?php
/**
 * Functions for handling stylesheets in the framework.  Themes can add support for the 
 * 'hybrid-core-styles' feature to allow the framework to handle loading the stylesheets into the 
 * theme header at an appropriate point.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register Hybrid Core styles. */
add_action( 'wp_enqueue_scripts', 'hybrid_register_styles', 1 );

/* Load Hybrid Core styles. */
add_action( 'wp_enqueue_scripts', 'hybrid_enqueue_styles', 5 );

/* Load the development stylsheet in script debug mode. */
add_filter( 'stylesheet_uri', 'hybrid_min_stylesheet_uri', 10, 2 );

/**
 * Registers stylesheets for the framework.  This function merely registers styles with WordPress using
 * the wp_register_style() function.  It does not load any stylesheets on the site.  If a theme wants to 
 * register its own custom styles, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since 1.5.0
 * @access private
 * @return void
 */
function hybrid_register_styles() {

	/* Get framework styles. */
	$styles = hybrid_get_styles();

	/* Use the .min stylesheet if SCRIPT_DEBUG is turned off. */
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	/* Loop through each style and register it. */
	foreach ( $styles as $style => $args ) {

		$defaults = array( 
			'handle'  => $style, 
			'src'     => trailingslashit( HYBRID_CSS ) . "{$style}{$suffix}.css",
			'deps'    => null,
			'version' => false,
			'media'   => 'all'
		);

		$args = wp_parse_args( $args, $defaults );

		wp_register_style(
			sanitize_key( $args['handle'] ), 
			esc_url( $args['src'] ), 
			is_array( $args['deps'] ) ? $args['deps'] : null, 
			preg_replace( '/[^a-z0-9_\-.]/', '', strtolower( $args['version'] ) ), 
			esc_attr( $args['media'] )
		);
	}
}

/**
 * Tells WordPress to load the styles needed for the framework using the wp_enqueue_style() function.
 *
 * @since 1.5.0
 * @access private
 * @return void
 */
function hybrid_enqueue_styles() {

	/* Get the theme-supported stylesheets. */
	$supports = get_theme_support( 'hybrid-core-styles' );

	/* If the theme doesn't add support for any styles, return. */
	if ( !is_array( $supports[0] ) )
		return;

	/* Get framework styles. */
	$styles = hybrid_get_styles();

	/* Loop through each of the core framework styles and enqueue them if supported. */
	foreach ( $supports[0] as $style ) {

		if ( isset( $styles[$style] ) )
			wp_enqueue_style( $style );
	}
}

/**
 * Returns an array of the core framework's available styles for use in themes.
 *
 * @since 1.5.0
 * @access private
 * @return array $styles All the available framework styles.
 */
function hybrid_get_styles() {

	/* Use the .min stylesheet if SCRIPT_DEBUG is turned off. */
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	/* Default styles available. */
	$styles = array(
		'one-five'   => array( 'version' => '20130523' ),
		'18px'       => array( 'version' => '20130526' ),
		'20px'       => array( 'version' => '20130526' ),
		'21px'       => array( 'version' => '20130526' ),
		'22px'       => array( 'version' => '20130526' ),
		'24px'       => array( 'version' => '20130526' ),
		'25px'       => array( 'version' => '20130526' ),
		'drop-downs' => array( 'version' => '20110919' ),
		'nav-bar'    => array( 'version' => '20110519' ),
		'gallery'    => array( 'version' => '20130526' ),
	);

	/* If a child theme is active, add the parent theme's style. */
	if ( is_child_theme() ) {
		$parent = wp_get_theme( get_template() );

		/* Get the parent theme stylesheet. */
		$src = trailingslashit( THEME_URI ) . "style.css";

		/* If a '.min' version of the parent theme stylesheet exists, use it. */
		if ( !empty( $suffix ) && file_exists( trailingslashit( THEME_DIR ) . "style{$suffix}.css" ) )
			$src = trailingslashit( THEME_URI ) . "style{$suffix}.css";

		$styles['parent'] = array( 'src' => $src, 'version' => $parent->get( 'Version' ) );
	}

	/* Add the active theme style. */
	$styles['style'] = array( 'src' => get_stylesheet_uri(), 'version' => wp_get_theme()->get( 'Version' ) );

	/* Return the array of styles. */
	return apply_filters( hybrid_get_prefix() . '_styles', $styles );
}

/**
 * Filters the 'stylesheet_uri' to allow theme developers to offer a minimized version of their main 
 * 'style.css' file.  It will detect if a 'style.min.css' file is available and use it if SCRIPT_DEBUG 
 * is disabled.
 *
 * @since 1.5.0
 * @access public
 * @param  string $stylesheet_uri The URI of the active theme's stylesheet.
 * @param  string $stylesheet_dir_uri The directory URI of the active theme's stylesheet.
 * @return string $stylesheet_uri
 */
function hybrid_min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	/* Use the .min stylesheet if SCRIPT_DEBUG is turned off. */
	if ( !defined( 'SCRIPT_DEBUG' ) || false === SCRIPT_DEBUG ) {
		$suffix = '.min';

		/* Remove the stylesheet directory URI from the file name. */
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		/* Change the stylesheet name to 'style.min.css'. */
		$stylesheet = str_replace( '.css', "{$suffix}.css", $stylesheet );

		/* If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet. */
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $stylesheet ) )
			$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . $stylesheet;
	}

	/* Return the theme stylesheet. */
	return $stylesheet_uri;
}

?>