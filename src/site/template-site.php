<?php

namespace Hybrid\Site;

/**
 * Renders the site title HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_title( array $args = [] ) {

	echo fetch_title( $args );
}

/**
 * Returns the site title HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_title( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'tag'       => is_front_page() ? 'h1' : 'div',
		'component' => 'app-header'
	] );

	$html  = '';
	$title = get_bloginfo( 'name', 'display' );

	if ( $title ) {
		$link = sprintf( '<a href="%s">%s</a>', esc_url( home_url() ), $title );

		$class = $args['component'] ? "{$args['component']}__title" : 'site-title';

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $class ),
			$link
		);
	}

	return apply_filters( 'hybrid/site/title', $html );
}

/**
 * Renders the site description HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_description( array $args = [] ) {

	echo fetch_description( $args );
}

/**
 * Returns the site description HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_description( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'tag'       => 'div',
		'component' => 'app-header',
	] );

	$html = '';
	$desc = get_bloginfo( 'description', 'display' );

	if ( $desc ) {

		$class = $args['component'] ? "{$args['component']}__description" : 'site-description';

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $class ),
			$desc
		);
	}

	return apply_filters( 'hybrid/site/description', $html );
}
