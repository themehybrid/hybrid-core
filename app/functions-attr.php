<?php
/**
 * HTML attribute functions and filters.  The purposes of this is to provide a way for theme/plugin devs
 * to hook into the attributes for specific HTML elements and create new or modify existing attributes.
 * This is sort of like `body_class()`, `post_class()`, and `comment_class()` on steroids.  Plus, it
 * handles attributes for many more elements.  The biggest benefit of using this is to provide richer
 * microdata while being forward compatible with the ever-changing Web.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Attributes for a few default elements.
add_filter( 'hybrid_attr_body',    'hybrid_attr_body',    5 );
add_filter( 'hybrid_attr_post',    'hybrid_attr_post',    5 );
add_filter( 'hybrid_attr_entry',   'hybrid_attr_post',    5 ); // Alternate for "post".
add_filter( 'hybrid_attr_comment', 'hybrid_attr_comment', 5 );

/**
 * Outputs an HTML element's attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $slug     The slug/ID of the element (e.g., 'sidebar').
 * @param  string  $context  A specific context (e.g., 'primary').
 * @param  array   $attr     Array of attributes to pass in (overwrites filters).
 * @return void
 */
function hybrid_attr( $slug, $context = '', $attr = array()  ) {

	echo hybrid_get_attr( $slug, $context, $attr );
}

/**
 * Gets an HTML element's attributes.  This function is actually meant to be filtered by theme authors, plugins,
 * or advanced child theme users.  The purpose is to allow folks to modify, remove, or add any attributes they
 * want without having to edit every template file in the theme.  So, one could support microformats instead
 * of microdata, if desired.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $slug     The slug/ID of the element (e.g., 'sidebar').
 * @param  string  $context  A specific context (e.g., 'primary').
 * @param  array   $attr     Array of attributes to pass in (overwrites filters).
 * @return string
 */
function hybrid_get_attr( $slug, $context = '', $attr = array() ) {

	$out = '';

	// Default attributes.
	$defaults = array( 'class' => $context ? "{$slug} {$slug}-{$context}" : $slug );

	// Filtered attributes.
	$filtered = apply_filters( 'hybrid_attr', $defaults, $slug, $context  );
	$filtered = apply_filters( "hybrid_attr_{$slug}", $filtered, $context );

	// Merge the attributes with those input.
	$attr = wp_parse_args( $attr, $filtered );

	foreach ( $attr as $name => $value ) {

		// Provide a filter hook for the class attribute directly. The classes are
		// split up into an array for easier filtering. Note that theme authors
		// should still utilize the core WP body, post, and comment class filter
		// hooks. This should only be used for custom attributes.
		if ( 'class' === $name && has_filter( "hybrid_attr_{$slug}_class" ) ) {

			$value = join( ' ', apply_filters( "hybrid_attr_{$slug}_class", explode( ' ', $value ) ) );
		}

		$out .= false !== $value ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );
	}

	return trim( $out );
}

/**
 * <body> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_body( $attr ) {

	$attr['class'] = join( ' ', get_body_class() );
	$attr['dir']   = is_rtl() ? 'rtl' : 'ltr';

	return $attr;
}

/**
 * Post <article> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_post( $attr ) {

	$post = get_post();

	$attr['id']    = ! empty( $post ) ? sprintf( 'post-%d', get_the_ID() ) : 'post-0';
	$attr['class'] = join( ' ', get_post_class() );

	return $attr;
}


/**
 * Comment wrapper attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function hybrid_attr_comment( $attr ) {

	$attr['id']    = 'comment-' . get_comment_ID();
	$attr['class'] = join( ' ', get_comment_class() );

	return $attr;
}
