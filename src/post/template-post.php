<?php

namespace Hybrid\Post;

/**
 * Creates a hierarchy based on the current post. It's primary purpose is for
 * use with post views/templates.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function hierarchy() {

	// Set up an empty array and get the post type.
	$hierarchy = [];
	$post_type = get_post_type();

	// If attachment, add attachment type templates.
	if ( 'attachment' === $post_type ) {

		extract( mime_types() );

		if ( $subtype ) {
			$hierarchy[] = "attachment-{$type}-{$subtype}";
			$hierarchy[] = "attachment-{$subtype}";
		}

		$hierarchy[] = "attachment-{$type}";
	}

	// If the post type supports 'post-formats', get the template based on the format.
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// Get the post format.
		$post_format = get_post_format() ?: 'standard';

		// Template based off post type and post format.
		$hierarchy[] = "{$post_type}-{$post_format}";

		// Template based off the post format.
		$hierarchy[] = $post_format;
	}

	// Template based off the post type.
	$hierarchy[] = $post_type;

	return apply_filters( 'hybrid/post/hierarchy', $hierarchy );
}

/**
 * Renders the post author HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_author( array $args = [] ) {

	echo fetch_author( $args );
}

/**
 * Returns the post author HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_author( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'before' => '',
		'after'  => ''
	] );

	$url = get_author_posts_url( get_the_author_meta( 'ID' ) );

	$html = sprintf(
		'<a class="entry__author" href="%s">%s</a>',
		esc_url( $url ),
		sprintf( $args['text'], get_the_author() )
	);

	return apply_filters(
		'hybrid/post/author',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Renders the post date HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_date( array $args = [] ) {

	echo fetch_date( $args );
}

/**
 * Returns the post date HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_date( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'format' => '',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<time class="entry__published">%s</time>',
		sprintf( $args['text'], get_the_date( $args['format'] ) )
	);

	return apply_filters(
		'hybrid/post/date',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Renders the post comments link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_comments_link( array $args = [] ) {

	echo fetch_comments_link( $args );
}

/**
 * Returns the post comments link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
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

	$url  = get_comments_link();
	$text = get_comments_number( $args['zero'], $args['one'], $args['more'] );

	$html = sprintf(
		'<a class="entry__comments" href="%s">%s</a>',
		esc_url( $url ),
		$text
	);

	return apply_filters(
		'hybrid/post/comments',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Renders the post terms HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_terms( array $args = [] ) {

	echo fetch_terms( $args );
}

/**
 * Returns the post terms HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
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

		$class = "entry__terms entry__terms--{$args['taxonomy']}";

		$html = sprintf(
			'<span class="%s">%s</span>',
			esc_attr( $class ),
			sprintf( $args['text'], $terms )
		);

		$html = $args['before'] . $html . $args['after'];
	}

	return apply_filters(
		'hybrid/post/terms',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Renders the post format HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_format( array $args = [] ) {

	echo fetch_format( $args );
}

/**
 * Returns the post format HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_format( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'before' => '',
		'after'  => ''
	] );

	$format = get_post_format();
	$url    = $format ? get_post_format_link( $format ) : get_permalink();
	$string = get_post_format_string( $format );

	$html = sprintf(
		'<a class="entry__format" href="%s">%s</a>',
		esc_url( $url ),
		sprintf( $args['text'], $string )
	);

	return apply_filters(
		'hybrid/post/format',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Splits the post mime type into two distinct parts: type / subtype
 * (e.g., image / png). Returns an array of the parts.
 *
 * @since  5.0.0
 * @access public
 * @param  \WP_Post|int  $post  A post object or ID.
 * @return array
 */
function mime_types( $post = null ) {

	$type    = get_post_mime_type( $post );
	$subtype = '';

	if ( false !== strpos( $type, '/' ) ) {
		list( $type, $subtype ) = explode( '/', $type );
	}

	return [
		'type'    => $type,
		'subtype' => $subtype
	];
}

/**
 * Checks if a post has any content. Useful if you need to check if the user has
 * written any content before performing any actions.
 *
 * @since  5.0.0
 * @access public
 * @param  \WP_Post|int  $post  A post object or post ID.
 * @return bool
 */
function has_content( $post = null ) {
	$post = get_post( $post );

	return ! empty( $post->post_content );
}

/**
 * Returns the number of items in all the galleries for the post.
 *
 * @since  5.0.0
 * @access public
 * @param  \WP_Post|int  $post  A post object or ID.
 * @return int
 */
function gallery_count( $post = null ) {

	$post   = get_post( $post );
	$images = [];

	// `get_post_galleries_images()` passes an array of arrays, so we need
	// to merge them all together.
	foreach ( get_post_galleries_images( $post ) as $gallery_images ) {
		$images = array_merge( $images, $gallery_images );
	}

	// If there are no images in the array, just grab the attached images.
	if ( ! $images ) {

		$images = get_posts( [
			'fields'         => 'ids',
			'post_parent'    => $post->ID,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => -1
		] );
	}

	// Return the count of the images.
	return count( $images );
}
