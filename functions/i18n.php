<?php
/**
 * Internationalization and translation functions.  Because Hybrid Core is a framework made up of various 
 * extensions with different textdomains, it must filter 'gettext' so that a single translation file can 
 * handle all translations.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Overrides the load textdomain function for the 'hybrid-core' domain. */
add_filter( 'override_load_textdomain', 'hybrid_override_load_textdomain', 5, 3 );

/* Filter the textdomain mofile to allow child themes to load the parent theme translation. */
add_filter( 'load_textdomain_mofile', 'hybrid_load_textdomain_mofile', 10, 2 );

/**
 * Overrides the load textdomain functionality when 'hybrid-core' is the domain in use.  The purpose of 
 * this is to allow theme translations to handle the framework's strings.  What this function does is 
 * sets the 'hybrid-core' domain's translations to the themes.  That way, we're not loading multiple 
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

	/* Set up array of domains to catch. */
	$text_domains = array( 'hybrid-core' );

	if ( current_theme_supports( 'breadcrumb-trail' ) ) $text_domains[] = 'breadcrumb-trail';
	if ( current_theme_supports( 'post-stylesheets' ) ) $text_domains[] = 'post-stylesheets';
	if ( current_theme_supports( 'theme-layouts'    ) ) $text_domains[] = 'theme-layouts';

	/* Check if the domain is one of our framework domains. */
	if ( in_array( $domain, $text_domains ) ) {
		global $l10n;

		$theme = wp_get_theme( get_template() );
		$theme_textdomain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_template();

		if ( !empty( $theme_textdomain ) && isset( $l10n[ $theme_textdomain ] ) )
			$l10n[ $domain ] = $l10n[ $theme_textdomain ];

		/* Always override.  We only want the theme to handle translations. */
		$override = true;
	}

	return $override;
}

/**
 * Checks if a textdomain's translation files have been loaded.  This function behaves differently from 
 * WordPress core's is_textdomain_loaded(), which will return true after any translation function is run over 
 * a text string with the given domain.  The purpose of this function is to simply check if the translation files 
 * are loaded.
 *
 * @since 1.3.0
 * @access private This is only used internally by the framework for checking translations.
 * @param string $domain The textdomain to check translations for.
 */
function hybrid_is_textdomain_loaded( $domain ) {
	global $hybrid;

	return ( isset( $hybrid->textdomain_loaded[ $domain ] ) && true === $hybrid->textdomain_loaded[ $domain ] ) ? true : false;
}

/**
 * Loads an empty MO file for the framework textdomain.  This will be overwritten.  The framework domain 
 * will be merged with the theme domain.
 *
 * @since 1.3.0
 * @access private
 * @uses load_textdomain() Loads an MO file into the domain for the framework.
 * @param string $domain The name of the framework's textdomain.
 * @return true|false Whether the MO file was loaded.
 */
function hybrid_load_framework_textdomain( $domain ) {
	return load_textdomain( $domain, '' );
}

/**
 * @since 0.7.0
 * @deprecated 1.3.0
 */
function hybrid_get_textdomain() {
	_deprecated_function( __FUNCTION__, '1.3.0', 'hybrid_get_parent_textdomain' );
	return hybrid_get_parent_textdomain();
}

/**
 * Gets the parent theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * parent theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since 1.3.0
 * @access private
 * @uses get_template() Defines the theme textdomain based on the template directory.
 * @global object $hybrid The global Hybrid object.
 * @return string $hybrid->textdomain The textdomain of the theme.
 */
function hybrid_get_parent_textdomain() {
	global $hybrid;

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $hybrid->parent_textdomain ) ) {

		$theme = wp_get_theme( get_template() );

		$textdomain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_template();

		$hybrid->parent_textdomain = sanitize_key( apply_filters( 'hybrid_parent_textdomain', $textdomain ) );
	}

	/* Return the expected textdomain of the parent theme. */
	return $hybrid->parent_textdomain;
}

/**
 * Gets the child theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * child theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since 1.2.0
 * @access private
 * @uses get_stylesheet() Defines the child theme textdomain based on the stylesheet directory.
 * @global object $hybrid The global Hybrid object.
 * @return string $hybrid->child_theme_textdomain The textdomain of the child theme.
 */
function hybrid_get_child_textdomain() {
	global $hybrid;

	/* If a child theme isn't active, return an empty string. */
	if ( !is_child_theme() )
		return '';

	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $hybrid->child_textdomain ) ) {

		$theme = wp_get_theme();

		$textdomain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_stylesheet();

		$hybrid->child_textdomain = sanitize_key( apply_filters( 'hybrid_child_textdomain', $textdomain ) );
	}

	/* Return the expected textdomain of the child theme. */
	return $hybrid->child_textdomain;
}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since 1.3.0
 * @access private
 * @param string $mofile File name of the .mo file.
 * @param string $domain The textdomain currently being filtered.
 * @return $mofile
 */
function hybrid_load_textdomain_mofile( $mofile, $domain ) {

	/* If the $domain is for the parent or child theme, search for a $domain-$locale.mo file. */
	if ( $domain == hybrid_get_parent_textdomain() || $domain == hybrid_get_child_textdomain() ) {

		/* Check for a $domain-$locale.mo file in the parent and child theme root and /languages folder. */
		$locale = get_locale();
		$locate_mofile = locate_template( array( "languages/{$domain}-{$locale}.mo", "{$domain}-{$locale}.mo" ) );

		/* If a mofile was found based on the given format, set $mofile to that file name. */
		if ( !empty( $locate_mofile ) )
			$mofile = $locate_mofile;
	}

	/* Return the $mofile string. */
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

	return preg_replace( '/(.*?)_.*?$/i', '$1', $locale );
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

	return preg_replace( '/.*?_(.*?)$/i', '$1', $locale );
}
