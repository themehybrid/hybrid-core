<?php
/**
 * Internationalization and translation functions.  Because Hybrid Core is a framework made up of various 
 * extensions with different textdomains, it must filter 'gettext' so that a single translation file can 
 * handle all translations.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2012, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

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
 * Loads the framework's translation files.  The function first checks if the parent theme or child theme 
 * has the translation files housed in their '/languages' folder.  If not, it sets the translation file the the 
 * framework '/languages' folder.
 *
 * @since 1.3.0
 * @access private
 * @uses load_textdomain() Loads an MO file into the domain for the framework.
 * @param string $domain The name of the framework's textdomain.
 * @return true|false Whether the MO file was loaded.
 */
function hybrid_load_framework_textdomain( $domain ) {

	/* Get the WordPress installation's locale set by the user. */
	$locale = get_locale();

	/* Check if the mofile is located in parent/child theme /languages folder. */
	$mofile = locate_template( array( "languages/{$domain}-{$locale}.mo" ) );

	/* If no mofile was found in the parent/child theme, set it to the framework's mofile. */
	if ( empty( $mofile ) )
		$mofile = trailingslashit( HYBRID_LANGUAGES ) . "{$domain}-{$locale}.mo";

	return load_textdomain( $domain, $mofile );
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
	if ( empty( $hybrid->parent_textdomain ) )
		$hybrid->parent_textdomain = sanitize_key( apply_filters( hybrid_get_prefix() . '_parent_textdomain', get_template() ) );

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
	if ( empty( $hybrid->child_textdomain ) )
		$hybrid->child_textdomain = sanitize_key( apply_filters( hybrid_get_prefix() . '_child_textdomain', get_stylesheet() ) );

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
 * Filters 'gettext' to change the translations used for the 'hybrid-core' textdomain.  This filter makes it possible 
 * for the theme's MO file to translate the framework's text strings.
 *
 * @since 1.3.0
 * @access private
 * @param string $translated The translated text.
 * @param string $text The original, untranslated text.
 * @param string $domain The textdomain for the text.
 * @return string $translated
 */
function hybrid_gettext( $translated, $text, $domain ) {

	/* Check if 'hybrid-core' is the current textdomain, there's no mofile for it, and the theme has a mofile. */
	if ( 'hybrid-core' == $domain && !hybrid_is_textdomain_loaded( 'hybrid-core' ) && hybrid_is_textdomain_loaded( hybrid_get_parent_textdomain() ) ) {

		/* Get the translations for the theme. */
		$translations = &get_translations_for_domain( hybrid_get_parent_textdomain() );

		/* Translate the text using the theme's translation. */
		$translated = $translations->translate( $text );
	}

	return $translated;
}

/**
 * Filters 'gettext' to change the translations used for the each of the extensions' textdomains.  This filter 
 * makes it possible for the theme's MO file to translate the framework's extensions.
 *
 * @since 1.3.0
 * @access private
 * @param string $translated The translated text.
 * @param string $text The original, untranslated text.
 * @param string $domain The textdomain for the text.
 * @return string $translated
 */
function hybrid_extensions_gettext( $translated, $text, $domain ) {

	/* Check if the current textdomain matches one of the framework extensions. */
	if ( in_array( $domain, array( 'breadcrumb-trail', 'custom-field-series', 'post-stylesheets', 'theme-layouts' ) ) ) {

		/* If the theme supports the extension, switch the translations. */
		if ( current_theme_supports( $domain ) ) {

			/* If the framework mofile is loaded, use its translations. */
			if ( hybrid_is_textdomain_loaded( 'hybrid-core' ) )
				$translations = &get_translations_for_domain( 'hybrid-core' );

			/* If the theme mofile is loaded, use its translations. */
			elseif ( hybrid_is_textdomain_loaded( hybrid_get_parent_textdomain() ) )
				$translations = &get_translations_for_domain( hybrid_get_parent_textdomain() );

			/* If translations were found, translate the text. */
			if ( !empty( $translations ) )
				$translated = $translations->translate( $text );
		}
	}

	return $translated;
}

?>