<?php
/**
 * The core functions file for the Hybrid framework. Functions defined here are generally
 * used across the entire framework to make various tasks faster. This file should be loaded
 * prior to any other files because its functions are needed to run the framework.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * @since 1.3.0
 */
function hybrid_load_framework_textdomain( $domain ) {
	global $hybrid;

	$locale = get_locale();

	$mofile = locate_template( array( "languages/{$domain}-{$locale}.mo" ) );

	if ( !empty( $mofile ) )
		return load_textdomain( $domain, $mofile );

	$mofile = trailingslashit( HYBRID_LANGUAGES ) . "{$domain}-{$locale}.mo";

	if ( file_exists( $mofile ) )
		return load_textdomain( $domain, $mofile );

	return false;
}

/**
 * @since 1.3.0
 */
function hybrid_gettext( $translated, $text, $domain ) {

	/**
	 * @todo Don't use this array here. We need to check current_theme_supports() b/c some extensions
	 * are plugins. We don't want to muck with their translations.
	 *
	 * @todo Somewhat related. The framework should remove_theme_support() if it's checking against a
	 * function b/c the theme would still support something even if it's running the plugin and not the extension.
	 */
	$domains = array( 'breadcrumb-trail', 'custom-field-series', 'post-stylesheets', 'theme-layouts' );

	if ( !is_textdomain_loaded( 'hybrid-core' ) ) {
		$domains[] = 'hybrid-core';
		$use_domain = hybrid_get_parent_textdomain();
	} else {
		$use_domain = 'hybrid-core';
	}

	if ( in_array( $domain, $domains ) ) {
		$translations = &get_translations_for_domain( $use_domain );

		$translated = $translations->translate( $text );
	}

	return $translated;
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
	if ( empty( $hybrid->textdomain ) )
		$hybrid->textdomain = sanitize_key( apply_filters( hybrid_get_prefix() . '_textdomain', get_template() ) );

	return $hybrid->textdomain;
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

	return $hybrid->child_textdomain;
}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since 0.9.0
 * @param string $mofile File name of the .mo file.
 * @param string 'hybrid-core' The textdomain currently being filtered.
 */
function hybrid_load_textdomain( $mofile, $domain ) {

	/* If the 'hybrid-core' is for the parent or child theme, search for a $domain-$locale.mo file. */
	if ( $domain == hybrid_get_textdomain() || $domain == hybrid_get_child_textdomain() ) {

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

?>