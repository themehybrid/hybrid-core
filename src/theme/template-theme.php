<?php

namespace Hybrid\Theme;

/**
 * Displays a link to the parent theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function render_link() {

	echo fetch_link();
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function fetch_link() {

	$theme = wp_get_theme( get_template() );

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	return sprintf(
		'<a class="theme-link" href="%s">%s</a>',
		esc_url( $theme->display( 'ThemeURI' ) ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);
}

/**
 * Displays a link to the child theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function render_child_link() {

	echo fetch_child_link();
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function fetch_child_link() {

	if ( ! is_child_theme() ) {
		return '';
	}

	$theme = wp_get_theme();

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	return sprintf(
		'<a class="child-link" href="%s">%s</a>',
		esc_url( $theme->display( 'ThemeURI' ) ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);
}
