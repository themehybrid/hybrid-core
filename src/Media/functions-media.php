<?php
/**
 * Media functions.
 *
 * Helper functions and template tags related to media.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Media;

use Hybrid\Proxies\App;

/**
 * Outputs the media grabber HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function display( array $args = [] ) {

	( new Grabber( $args ) )->display();
}

/**
 * Returns the media grabber HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function render( array $args = [] ) {

	return ( new Grabber( $args ) )->render();
}

/**
 * Returns an instance of a media meta repository based on the attachment ID.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return Meta
 */
function meta_repo( $post_id ) {

	$repositories = App::resolve( 'media/meta' );

	if ( ! $repositories->has( $post_id ) ) {

		$repositories[ $post_id ] = new Meta( $post_id );
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
function display_meta( $property, $args = [] ) {

	echo render_meta( $property, $args );
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
function render_meta( $property, array $args = [] ) {

	$html = $label = '';

	$args = wp_parse_args( $args, [
		'post_id'     => get_the_ID(),
		'tag'         => 'span',
		'label'       => '',
		'text'        => '%s',
		'class'       => 'media-meta__item',
		'label_class' => 'media-meta__label',
		'data_class'  => 'media-meta__data',
		'before'      => '',
		'after'       => ''
	] );

	// Append formatted property to class name.
	if ( ! $args['class'] ) {

		$args['class'] = sprintf(
			'media-meta__item media-meta__item--',
			strtolower( str_replace( '_', '-', $property ) )
		);
	}

	// Get the media meta repository for this post.
	$meta_object = meta_repo( $args['post_id'] );

	// Retrieve the meta value that we want from the repository.
	$meta = $meta_object->get( $property );

	if ( $meta ) {

		if ( $args['label'] ) {

			$label = sprintf(
				'<span class="%s">%s</span> ',
				esc_attr( $args['label_class'] ),
				$args['label']
			);
		}

		$data = sprintf(
			'<span class="%s">%s</span>',
			esc_attr( $args['data_class'] ),
			sprintf( $args['text'], $meta )
		);

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $args['class'] ),
			$label . $data
		);

		$html = $args['before'] . $html . $args['after'];
	}

	return $html;
}

/**
 * Outputs the image size links HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function display_image_sizes( array $args = [] ) {

	echo render_image_sizes( $args );
}

/**
 * Returns a set of image attachment links based on size.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function render_image_sizes( array $args = [] ) {

	// If not viewing an image attachment page, return.
	if ( ! wp_attachment_is_image( get_the_ID() ) ) {
		return;
	}

	$args = wp_parse_args( $args, [
		'text'       => '%s',
		'sep'        => '/',
		'class'      => 'entry__image-sizes',
		'size_class' => 'entry__image-size-link',
		'before'     => '',
		'after'      => ''
	] );

	// Set up an empty array for the links.
	$links = [];

	// Get the intermediate image sizes and add the full size to the array.
	$sizes   = get_intermediate_image_sizes();
	$sizes[] = 'full';

	// Loop through each of the image sizes.
	foreach ( $sizes as $size ) {

		// Get the image source, width, height, and whether it's intermediate.
		$image = wp_get_attachment_image_src( get_the_ID(), $size );

		// Add the link to the array if there's an image and if
		// `$is_intermediate` (4th array value) is true or full size.
		if ( ! empty( $image ) && ( true === $image[3] || 'full' == $size ) ) {

			$label = sprintf(
				// Translators: Media dimensions - 1 is width and 2 is height.
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $image[1] ) ),
				number_format_i18n( absint( $image[2] ) )
			);

			$links[] = sprintf(
				'<a class="%s" href="%s">%s</a>',
				esc_attr( $args['size_class'] ),
				esc_url( $image[0] ),
				$label
			);
		}
	}

	$sep = $args['sep'] ? sprintf( '<span class="sep">%s</span>', $args['sep'] ) : '';

	$html = sprintf(
		'<span class="%s">%s</span>',
		esc_attr( $args['class'] ),
		sprintf( $args['text'], join( " {$sep} ", $links ) )
	);

	return $args['before'] . $html . $args['after'];
}
