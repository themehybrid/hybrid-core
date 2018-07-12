<?php

namespace Hybrid\MediaMeta;

use function Hybrid\app;

/**
 * Returns an instance of a media meta repository based on the attachment ID.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return object
 */
function repo( $post_id ) {

	$repositories = app( 'media_meta' );

	if ( ! $repositories->has( $post_id ) ) {

		$repositories[ $post_id ] = new Repository( $post_id );
	}

	return $repositories[ $post_id ];
}

/**
 * Prints media meta directly to the screen.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $property
 * @param  array   $args
 * @return void
 */
function render( $property, $args = [] ) {

	echo fetch( $property, $args );
}

/**
 * Returns media meta from a media meta object.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $property
 * @param  array   $args
 * @return string
 */
function fetch( $property, array $args = [] ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'post_id' => get_the_ID(),
		'itemtag' => 'span',
		'label'   => '',
		'text'    => '%s',
		'before'  => '',
		'after'   => ''
	] );

	// Get the media meta repository for this post.
	$meta_object = repo( $args['post_id'] );

	// Retrieve the meta value that we want from the repository.
	$meta = is_object( $meta_object ) ? $meta_object->get( $property )->fetch() : '';

	if ( $meta ) {

		$label = $args['label'] ? sprintf( '<span class="media-meta__label">%s</span> ', $args['label'] ) : '';

		$data = '<span class="media-meta__data">' . sprintf( $args['text'], $meta ) . '</span>';

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['itemtag'] ),
			esc_attr( "media-meta__item media-meta__item--{$property}" ),
			$label . $data
		);

		$html = $args['before'] . $html . $args['after'];
	}

	return $html;
}
