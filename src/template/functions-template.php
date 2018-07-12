<?php

namespace Hybrid\Template;

use function Hybrid\app;
use function Hybrid\config;

/**
 * Returns the global hierarchy. This is a wrapper around the values stored via
 * the template hierarchy object.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function hierarchy() {

	return apply_filters( 'hybrid/template/hierarchy', app( 'template/hierarchy' )->hierarchy() );
}

/**
 * A better `locate_template()` function than what core WP provides. Note that
 * this function merely locates templates and does no loading. Use the core
 * `load_template()` function for actually loading the template.
 *
 * @since  5.0.0
 * @access public
 * @param  array|string  $templates
 * @return string
 */
function locate( $templates ) {
	$located = '';

	foreach ( (array) $templates as $template ) {

		foreach ( locations() as $location ) {

			$file = trailingslashit( $location ) . $template;

			if ( file_exists( $file ) ) {
				$located = $file;
				break 2;
			}
		}
	}

	return $located;
}

/**
 * Returns an array of locations to look for templates.
 *
 * Note that this won't work with the core WP template hierarchy due to an
 * issue that hasn't been addressed since 2010.
 *
 * @link   https://core.trac.wordpress.org/ticket/13239
 * @since  5.0.0
 * @access public
 * @return array
 */
function locations() {

	$path = config( 'view' )->path ? '/' . config( 'view' )->path : '';

	// Add active theme path.
	$locations = [ get_stylesheet_directory() . $path ];

	// If child theme, add parent theme path second.
	if ( is_child_theme() ) {
		$locations[] = get_template_directory() . $path;
	}

	return (array) apply_filters( 'hybrid/template/locations', $locations );
}

/**
 * Filters an array of templates and prefixes them with the view path.
 *
 * @since  5.0.0
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
 * Executes the action hook for themes to register their templates. Themes should
 * always register on this hook.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function register_templates() {

	do_action( 'hybrid/templates/register', app( 'template/templates' ) );
}

/**
 * Filter used on `theme_templates` to add custom templates to the template
 * drop-down.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $templates
 * @param  object  $theme
 * @param  object  $post
 * @param  string  $post_type
 * @return array
 */
function post_templates_filter( $templates, $theme, $post, $post_type ) {

	foreach ( app( 'template/templates' )->all() as $template ) {

		if ( $template->forPostType( $post_type ) ) {

			$templates[ $template->filename() ] = esc_html( $template->label() );
		}
	}

	return $templates;
}
