<?php
/**
 * Internationalization and translation functions. This file provides a few functions for use by theme 
 * authors.  It also handles properly loading translation files for both the parent and child themes.  Part 
 * of the functionality below handles consolidating the framework's textdomains with the textdomain of the 
 * parent theme to avoid having multiple translation files.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Load the locale functions file(s).
add_action( 'after_setup_theme', 'hybrid_load_locale_functions', 0 );

# Overrides the load textdomain function for the 'hybrid-core' domain.
add_filter( 'override_load_textdomain', 'hybrid_override_load_textdomain', 5, 3 );

# Filter the textdomain mofile to allow child themes to load the parent theme translation.
add_filter( 'load_textdomain_mofile', 'hybrid_load_textdomain_mofile', 10, 2 );

/**
 * Loads a `/languages/{$locale}.php` file for specific locales.  `$locale` should be an all lowercase 
 * and hyphenated (as opposed to an underscore) file name.  So, an `en_US` locale would be `en-us.php`. 
 * Also note that the child theme locale file will load **before** the parent theme locale file.  This 
 * is standard practice in core WP for allowing pluggable functions if a theme author so desires.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_load_locale_functions() {

	// Get the site's locale.
	$locale = strtolower( str_replace( '_', '-', get_locale() ) );

	// Define locale functions files.
	$stylesheet_locale_functions = trailingslashit( get_stylesheet_directory() ) . "languages/{$locale}.php";
	$template_locale_functions   = trailingslashit( get_template_directory()   ) . "languages/{$locale}.php";

	// If file exists in active/child theme.
	if ( file_exists( $stylesheet_locale_functions ) )
		require_once( $stylesheet_locale_functions );

	// If file exists in parent theme.
	if ( is_child_theme() && file_exists( $template_locale_functions ) )
		require_once( $template_locale_functions );
}

/**
 * Overrides the load textdomain functionality when 'hybrid-core' is the domain in use.  The purpose of 
 * this is to allow theme translations to handle the framework's strings.  What this function does is 
 * sets the 'hybrid-core' domain's translations to the theme's.  That way, we're not loading multiple 
 * of the same MO files.
 *
 * @since  2.0.0
 * @access public
 * @globl  array   $l10n
 * @param  bool    $override
 * @param  string  $domain
 * @param  string  $mofile
 * @return bool
 */
function hybrid_override_load_textdomain( $override, $domain, $mofile ) {

	// Check if the domain is one of our framework domains.
	if ( 'hybrid-core' === $domain ) {
		global $l10n;

		// Get the theme's textdomain.
		$theme_textdomain = hybrid_get_parent_textdomain();

		// If the theme's textdomain is loaded, use its translations instead.
		if ( !empty( $theme_textdomain ) && isset( $l10n[ $theme_textdomain ] ) )
			$l10n[ $domain ] = $l10n[ $theme_textdomain ];

		// Always override.  We only want the theme to handle translations.
		$override = true;
	}

	return $override;
}

/**
 * Loads an empty MO file for the framework textdomain.  This will be overwritten.  The framework domain 
 * will be merged with the theme domain.
 *
 * @since  1.3.0
 * @access public
 * @param  string $domain The name of the framework's textdomain.
 * @return bool           Whether the MO file was loaded.
 */
function hybrid_load_framework_textdomain( $domain = 'hybrid-core' ) {
	return load_textdomain( $domain, '' );
}

/**
 * Gets the parent theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * parent theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since  1.3.0
 * @access public
 * @global object $hybrid The global Hybrid object.
 * @return string         The textdomain of the theme.
 */
function hybrid_get_parent_textdomain() {
	global $hybrid;

	// If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain.
	if ( empty( $hybrid->parent_textdomain ) ) {

		$theme = wp_get_theme( get_template() );

		$textdomain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_template();

		$hybrid->parent_textdomain = sanitize_key( apply_filters( 'hybrid_parent_textdomain', $textdomain ) );
	}

	// Return the expected textdomain of the parent theme.
	return $hybrid->parent_textdomain;
}

/**
 * Gets the child theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * child theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since  1.2.0
 * @access public
 * @global object $hybrid The global Hybrid object.
 * @return string         The textdomain of the child theme.
 */
function hybrid_get_child_textdomain() {
	global $hybrid;

	// If a child theme isn't active, return an empty string.
	if ( !is_child_theme() )
		return '';

	// If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain.
	if ( empty( $hybrid->child_textdomain ) ) {

		$theme = wp_get_theme();

		$textdomain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_stylesheet();

		$hybrid->child_textdomain = sanitize_key( apply_filters( 'hybrid_child_textdomain', $textdomain ) );
	}

	// Return the expected textdomain of the child theme. */
	return $hybrid->child_textdomain;
}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since  1.3.0
 * @access public
 * @param  string $mofile File name of the .mo file.
 * @param  string $domain The textdomain currently being filtered.
 * @return string
 */
function hybrid_load_textdomain_mofile( $mofile, $domain ) {

	// If the $domain is for the parent or child theme, search for a $domain-$locale.mo file.
	if ( $domain == hybrid_get_parent_textdomain() || $domain == hybrid_get_child_textdomain() ) {

		// Check for a $domain-$locale.mo file in the parent and child theme root and /languages folder.
		$locale = get_locale();
		$locate_mofile = locate_template( array( "languages/{$domain}-{$locale}.mo", "{$domain}-{$locale}.mo" ) );

		// If a mofile was found based on the given format, set $mofile to that file name.
		if ( !empty( $locate_mofile ) )
			$mofile = $locate_mofile;
	}

	// Return the $mofile string.
	return $mofile;
}

/**
 * Gets the language for the currently-viewed page.  It strips the region from the locale if needed 
 * and just returns the language code.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function hybrid_get_language( $locale = '' ) {

	if ( empty( $locale ) )
		$locale = get_locale();

	return sanitize_key( preg_replace( '/(.*?)_.*?$/i', '$1', $locale ) );
}

/**
 * Gets the region for the currently viewed page.  It strips the language from the locale if needed.  Note that 
 * not all locales will have a region, so this might actually return the same thing as `hybrid_get_language()`.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function hybrid_get_region( $locale = '' ) {

	if ( empty( $locale ) )
		$locale = get_locale();

	return sanitize_key( preg_replace( '/.*?_(.*?)$/i', '$1', $locale ) );
}
