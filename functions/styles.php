<?php
/**
 * Functions for handling stylesheets in the framework.  Themes can add support for the 
 * 'hybrid-core-styles' feature to allow the framework to handle loading the stylesheets into the 
 * theme header at an appropriate point.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register Hybrid Core styles. */
add_action( 'wp_enqueue_scripts', 'hybrid_register_styles', 0 );

/* Load Hybrid Core styles. */
add_action( 'wp_enqueue_scripts', 'hybrid_enqueue_styles', 5 );

/* Load the development stylsheet in script debug mode. */
add_filter( 'stylesheet_uri', 'hybrid_min_stylesheet_uri', 5, 2 );

/* Filters the WP locale stylesheet. */
add_filter( 'locale_stylesheet_uri', 'hybrid_locale_stylesheet_uri', 5 );

/* Remove the default emoji styles. We'll handle this in the stylesheet. */
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Registers stylesheets for the framework.  This function merely registers styles with WordPress using
 * the wp_register_style() function.  It does not load any stylesheets on the site.  If a theme wants to 
 * register its own custom styles, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since  1.5.0
 * @access public
 * @return void
 */
function hybrid_register_styles() {

	$suffix = hybrid_get_min_suffix();

	/* Register styles for use by themes. */
	wp_register_style( 'hybrid-reset',    trailingslashit( HYBRID_CSS)  . "reset{$suffix}.css",  null, '20110523'                                       );
	wp_register_style( 'hybrid-one-five', trailingslashit( HYBRID_CSS ) . "one-five{$suffix}.css", null, '20131105'                                       );
	wp_register_style( 'hybrid-gallery',  trailingslashit( HYBRID_CSS ) . "gallery{$suffix}.css",  null, '20130526'                                       );
	wp_register_style( 'hybrid-parent',   hybrid_get_parent_stylesheet_uri(),                      null, wp_get_theme( get_template() )->get( 'Version' ) );
	wp_register_style( 'hybrid-style',    get_stylesheet_uri(),                                    null, wp_get_theme()->get( 'Version' )                 );
}

/**
 * Tells WordPress to load the styles needed for the framework using the wp_enqueue_style() function.
 *
 * As of version 2.1.0, this function and the use of `add_theme_support( 'hybrid-core-styles' )` has 
 * been deprecated. Theme authors should use `wp_enqueue_style()` to enqueue one of the appropriate 
 * framework styles registered in `hybrid_register_styles()`.
 *
 * @since      1.5.0
 * @deprecated 2.1.0
 * @access     public
 * @return     void
 */
function hybrid_enqueue_styles() {

	/* Get the theme-supported stylesheets. */
	$supports = get_theme_support( 'hybrid-core-styles' );

	/* If the theme doesn't add support for any styles, return. */
	if ( !is_array( $supports[0] ) )
		return;

	/* Loop through each of the core framework styles and enqueue them if supported. */
	foreach ( $supports[0] as $style )
		wp_enqueue_style( "hybrid-{$style}" );
}

/**
 * Returns the parent theme stylesheet URI.  Will return the active theme's stylesheet URI if no child 
 * theme is active. Be sure to check `is_child_theme()` when using.
 *
 * @since  2.1.0
 * @access public
 * @return string
 */
function hybrid_get_parent_stylesheet_uri() {

	/* Get the minified suffix. */
	$suffix = hybrid_get_min_suffix();

	/* Get the parent theme stylesheet. */
	$stylesheet_uri = trailingslashit( THEME_URI ) . 'style.css';

	/* If a '.min' version of the parent theme stylesheet exists, use it. */
	if ( !empty( $suffix ) && file_exists( trailingslashit( THEME_DIR ) . "style{$suffix}.css" ) )
		$stylesheet_uri = trailingslashit( THEME_URI ) . "style{$suffix}.css";

	return apply_filters( 'hybrid_get_parent_stylesheet_uri', $stylesheet_uri );
}

/**
 * Filters the 'stylesheet_uri' to allow theme developers to offer a minimized version of their main 
 * 'style.css' file.  It will detect if a 'style.min.css' file is available and use it if SCRIPT_DEBUG 
 * is disabled.
 *
 * @since  1.5.0
 * @access public
 * @param  string  $stylesheet_uri      The URI of the active theme's stylesheet.
 * @param  string  $stylesheet_dir_uri  The directory URI of the active theme's stylesheet.
 * @return string  $stylesheet_uri
 */
function hybrid_min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	/* Get the minified suffix. */
	$suffix = hybrid_get_min_suffix();

	/* Use the .min stylesheet if available. */
	if ( !empty( $suffix ) ) {

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

/**
 * Filters `locale_stylesheet_uri` with a more robust version for checking locale/language/region/direction 
 * stylesheets.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $stylesheet_uri
 * @return string
 */
function hybrid_locale_stylesheet_uri( $stylesheet_uri ) {

	$locale_style = hybrid_get_locale_style();

	return !empty( $locale_style ) ? $locale_style : $stylesheet_uri;
}

/**
 * Searches for a locale stylesheet.  This function looks for stylesheets in the `css` folder in the following 
 * order:  1) $lang-$region.css, 2) $region.css, 3) $lang.css, and 4) $text_direction.css.  It first checks 
 * the child theme for these files.  If they are not present, it will check the parent theme.  This is much 
 * more robust than the WordPress locale stylesheet, allowing for multiple variations and a more flexible 
 * hierarchy.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_locale_style() {

	$styles = array();

	/* Get the locale, language, and region. */
	$locale = strtolower( str_replace( '_', '-', get_locale() ) );
	$lang   = strtolower( hybrid_get_language() );
	$region = strtolower( hybrid_get_region() );

	$styles[] = "css/{$locale}.css";

	if ( $region !== $locale )
		$styles[] = "css/{$region}.css";

	if ( $lang !== $locale )
		$styles[] = "css/{$lang}.css";

	$styles[] = is_rtl() ? 'css/rtl.css' : 'css/ltr.css';

	return hybrid_locate_theme_file( $styles );
}
