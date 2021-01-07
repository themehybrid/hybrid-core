<?php
/**
 * Template functions.
 *
 * Helper functions and template tags related to templates.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Template;

use Hybrid\Contracts\Template\Hierarchy;
use Hybrid\Proxies\App;

/**
 * Returns the global hierarchy. This is a wrapper around the values stored via
 * the template hierarchy object.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function hierarchy() {

	return apply_filters(
		'hybrid/template/hierarchy',
		App::resolve( Hierarchy::class )->hierarchy()
	);
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
 * Returns the relative path to where templates are held in the theme.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function path( $file = '' ) {

	$file = ltrim( $file, '/' );
	$path = apply_filters( 'hybrid/template/path', 'resources/views' );

	return $file ? trailingslashit( $path ) . $file : $path;
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

	$path = ltrim( path(), '/' );

	// Add active theme path.
	$locations = [ get_stylesheet_directory() . "/{$path}" ];

	// If child theme, add parent theme path second.
	if ( \Hybrid\is_child_theme() ) {
		$locations[] = get_template_directory() . "/{$path}";
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

	$path = path();

	if ( $path ) {
		array_walk( $templates, function( &$template, $key ) use ( $path ) {

			$template = ltrim( str_replace( $path, '', $template ), '/' );

			$template = "{$path}/{$template}";
		} );
	}

	return $templates;
}
