<?php

namespace Hybrid\Theme;

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
