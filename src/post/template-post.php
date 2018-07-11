<?php

namespace Hybrid\Post;

use function Hybrid\Attr\attr;

function render_author( array $args = [] ) {

	echo fetch_author( $args );
}

function fetch_author( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'before' => '',
		'after'  => ''
	] );

	$attr = [
		'href'  => get_author_posts_url( get_the_author_meta( 'ID' ) ),
		'class' => 'entry__author'
	];

	$html = sprintf(
		'<a %s>%s</a>',
		attr( 'entry-author', '', $attr )->fetch(),
		sprintf( $args['text'], get_the_author() )
	);

	return $args['before'] . $html . $args['after'];
}

function render_date( array $args = [] ) {

	echo fetch_date( $args );
}

function fetch_date( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'format' => '',
		'before' => '',
		'after'  => ''
	] );

	$attr = [ 'class' => 'entry__published' ];

	$html = sprintf(
		'<time %s>%s</time>',
		attr( 'entry-published', '', $attr ),
		sprintf( $args['text'], get_the_date( $args['format'] ) )
	);

	return $args['before'] . $html . $args['after'];
}

function render_comments_link( array $args = [] ) {

	echo fetch_comments_link( $args );
}

function fetch_comments_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'zero'   => false,
		'one'    => false,
		'more'   => false,
		'before' => '',
		'after'  => ''
	] );

	$number = get_comments_number();

	if ( 0 == $number && ! comments_open() && ! pings_open() ) {
		return '';
	}

	$attr = [
		'href'  => get_comments_link(),
		'class' => 'entry__comments'
	];

	$text = get_comments_number( $args['zero'], $args['one'], $args['more'] );

	$html = sprintf(
		'<a %s>%s</a>',
		attr( 'entry-comments', '', $attr ),
		$text
	);

	return $args['before'] . $html . $args['after'];
}

function render_terms( array $args = [] ) {

	echo fetch_terms( $args );
}

function fetch_terms( array $args = [] ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'taxonomy' => 'category',
		'text'     => '%s',
		// Translators: Separates tags, categories, etc. when displaying a post.
		'sep'      => _x( ', ', 'taxonomy terms separator', 'hybrid-core' ),
		'before'   => '',
		'after'    => ''
	] );

	$terms = get_the_term_list( get_the_ID(), $args['taxonomy'], '', $args['sep'], '' );

	if ( $terms ) {

		$attr = [
			'class' => "entry__terms entry__terms--{$args['taxonomy']}"
		];

		$html = sprintf(
			'<span %s>%s</span>',
			attr( 'entry-terms', $args['taxonomy'], $attr )->fetch(),
			sprintf( $args['text'], $terms )
		);

		$html = $args['before'] . $html . $args['after'];
	}

	return $html;
}

function render_format( array $args = [] ) {

	echo fetch_format( $args );
}

function fetch_format( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'before' => '',
		'after'  => ''
	] );

	$format = get_post_format();
	$url    = $format ? get_post_format_link( $format ) : get_permalink();
	$string = get_post_format_string( $format );

	$attr = [
		'href'  => $url,
		'class' => 'entry__format'
	];

	$html = sprintf(
		'<a %s>%s</a>',
		attr( 'entry-format', '', $attr )->fetch(),
		sprintf( $args['text'], $string )
	);

	return $args['before'] . $html . $args['after'];
}
