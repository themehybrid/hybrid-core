<?php
/**
 * Functions for handling styles in the framework.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Register Hybrid Core styles.
add_action( 'wp_enqueue_scripts', 'hybrid_register_styles', 0 );

# Active theme style filters.
add_filter( 'stylesheet_uri', 'hybrid_min_stylesheet_uri', 5, 2 );
add_filter( 'stylesheet_uri', 'hybrid_style_filter',       15   );

# Filters the WP locale stylesheet.
add_filter( 'locale_stylesheet_uri', 'hybrid_locale_stylesheet_uri', 5 );

# Remove the default emoji styles. We'll handle this in the stylesheet.
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Registers stylesheets for the framework.  This function merely registers styles with WordPress using
 * the wp_register_style() function.  It does not load any stylesheets on the site.  If a theme wants to
 * register its own custom styles, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since  1.5.0
 * @access public
 * @global object  $wp_styles
 * @return void
 */
function hybrid_register_styles() {
	global $wp_styles;

	$suffix = hybrid_get_min_suffix();

	// Register styles for use by themes.
	wp_register_style( 'hybrid-one-five', HYBRID_CSS . "one-five{$suffix}.css" );
	wp_register_style( 'hybrid-gallery',  HYBRID_CSS . "gallery{$suffix}.css"  );
	wp_register_style( 'hybrid-parent',   hybrid_get_parent_stylesheet_uri()   );
	wp_register_style( 'hybrid-style',    get_stylesheet_uri()                 );

	// Use the RTL style for the hybrid-one-five style.
	$wp_styles->add_data( 'hybrid-one-five', 'rtl', 'replace' );

	// Adds the suffix for the hybrid-one-five RTL style.
	if ( $suffix )
		$wp_styles->add_data( 'hybrid-one-five', 'suffix', $suffix );
}

/**
 * Returns the parent theme stylesheet URI.  Will return the active theme's stylesheet URI if no child
 * theme is active. Be sure to check `is_child_theme()` when using.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_parent_stylesheet_uri() {

	// Get the minified suffix.
	$suffix = hybrid_get_min_suffix();

	// Get the parent theme stylesheet.
	$stylesheet_uri = HYBRID_PARENT_URI . 'style.css';

	// If a '.min' version of the parent theme stylesheet exists, use it.
	if ( $suffix && file_exists( HYBRID_PARENT . "style{$suffix}.css" ) )
		$stylesheet_uri = HYBRID_PARENT_URI . "style{$suffix}.css";

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

	// Get the minified suffix.
	$suffix = hybrid_get_min_suffix();

	// Use the .min stylesheet if available.
	if ( $suffix ) {

		// Remove the stylesheet directory URI from the file name.
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		// Change the stylesheet name to 'style.min.css'.
		$stylesheet = str_replace( '.css', "{$suffix}.css", $stylesheet );

		// If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet.
		if ( file_exists( HYBRID_CHILD . $stylesheet ) )
			$stylesheet_uri = esc_url( trailingslashit( $stylesheet_dir_uri ) . $stylesheet );
	}

	// Return the theme stylesheet.
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

	return $locale_style ? esc_url( $locale_style ) : $stylesheet_uri;
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

	// Get the locale, language, and region.
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

/**
 * Filters the 'stylesheet_uri' and checks if a post has a style that should overwrite the theme's
 * primary `style.css`.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $stylesheet_uri
 * @return string
 */
function hybrid_style_filter( $stylesheet_uri ) {

	if ( is_singular() ) {

		$style = hybrid_get_post_style( get_queried_object_id() );

		if ( $style && $style_uri = hybrid_locate_theme_file( array( $style ) ) )
			$stylesheet_uri = $style_uri;
	}

	return $stylesheet_uri;
}

/**
 * Gets a post style.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function hybrid_get_post_style( $post_id ) {
	return get_post_meta( $post_id, hybrid_get_style_meta_key(), true );
}

/**
 * Sets a post style.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @param  string  $layout
 * @return bool
 */
function hybrid_set_post_style( $post_id, $style ) {
	return update_post_meta( $post_id, hybrid_get_style_meta_key(), $style );
}

/**
 * Deletes a post style.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function hybrid_delete_post_style( $post_id ) {
	return delete_post_meta( $post_id, hybrid_get_style_meta_key() );
}

/**
 * Checks a post if it has a specific style.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function hybrid_has_post_style( $style, $post_id = '' ) {

	if ( ! $post_id )
		$post_id = get_the_ID();

	return $style === hybrid_get_post_style( $post_id ) ? true : false;
}

/**
 * Wrapper function for returning the metadata key used for objects that can use styles.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_style_meta_key() {
	return apply_filters( 'hybrid_style_meta_key', 'Stylesheet' );
}
