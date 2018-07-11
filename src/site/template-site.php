<?php

namespace Hybrid\Site;

use function Hybrid\Attr\attr;

function render_title( array $args = [] ) {

	echo fetch_title( $args );
}

function fetch_title( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'tag'       => is_front_page() ? 'h1' : 'div',
		'component' => 'app-header'
	] );

	$html  = '';
	$title = get_bloginfo( 'name', 'display' );

	if ( $title ) {
		$link = sprintf( '<a href="%s">%s</a>', esc_url( home_url() ), $title );

		$attr = [];

		if ( $args['component'] ) {
			$attr['class'] = "{$args['component']}__title";
		}

		$html = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			attr( 'site-title', $args['component'], $attr )->fetch(),
			$link
		);
	}

	return apply_filters( 'hybrid/site/title', $html );
}

function render_description( array $args = [] ) {

	echo fetch_description( $args );
}

function fetch_description( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'tag'       => 'div',
		'component' => 'app-header',
	] );

	$html = '';
	$desc = get_bloginfo( 'description', 'display' );

	if ( $desc ) {

		$attr = [];

		if ( $args['component'] ) {
			$attr['class'] = "{$args['component']}__description";
		}

		$html = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			attr( 'site-description', $args['component'], $attr )->fetch(),
			$desc
		);
	}

	return apply_filters( 'hybrid/site/description', $html );
}
