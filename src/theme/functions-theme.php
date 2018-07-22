<?php
/**
 * Theme functions.
 *
 * Helper functions and template tags related to the theme itself.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Theme;

/**
 * This is a wrapper function for core WP's `get_theme_mod()` function.  Core
 * doesn't provide a filter hook for the default value (useful for child themes).
 * The purpose of this function is to provide that additional filter hook.  To
 * filter the final theme mod, use the core `theme_mod_{$name}` filter hook.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @param  mixed   $default
 * @return mixed
 */
function mod( $name, $default = false ) {

	return get_theme_mod(
		$name,
		apply_filters( "hybrid/theme/mod/{$name}/default", $default )
	);
}

/**
 * Renders the [parent] theme link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_link( array $args = [] ) {

	echo fetch_link( $args );
}

/**
 * Returns the [parent] theme link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'class'  => 'theme-link',
		'before' => '',
		'after'  => ''
	] );

	$theme = wp_get_theme( get_template() );

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $theme->display( 'ThemeURI' ) ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);

	return apply_filters(
		'hybrid/theme/link/parent',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Renders the child theme link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_child_link( array $args = [] ) {

	echo fetch_child_link( $args );
}

/**
 * Returns the child theme link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_child_link( array $args = [] ) {

	if ( ! is_child_theme() ) {
		return '';
	}

	$args = wp_parse_args( $args, [
		'class'  => 'child-link',
		'before' => '',
		'after'  => ''
	] );

	$theme = wp_get_theme();

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $theme->display( 'ThemeURI' ) ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);

	return apply_filters(
		'hybrid/theme/link/child',
		$args['before'] . $html . $args['after']
	);
}
