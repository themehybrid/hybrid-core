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

namespace Hybrid;

# Attributes for a few default elements.
add_filter( app()->namespace . '/attr_body',    __NAMESPACE__ . '\attr_body',    5 );
add_filter( app()->namespace . '/attr_post',    __NAMESPACE__ . '\attr_post',    5 );
add_filter( app()->namespace . '/attr_entry',   __NAMESPACE__ . '\attr_post',    5 ); // Alternate for "post".
add_filter( app()->namespace . '/attr_comment', __NAMESPACE__ . '\attr_comment', 5 );

/**
 * Wrapper for creating a new `Attributes` object.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @param  string  $context
 * @param  array   $attr
 * @return object
 */
function attributes( $name, $context = '', $attr = array() ) {

	return new Attributes( $name, $context, $attr );
}

/**
 * Outputs an HTML element's attributes.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $slug
 * @param  string  $context
 * @param  array   $attr
 * @return void
 */
function attr( $slug, $context = '', $attr = array() ) {

	attributes( $slug, $context, $attr )->render();
}

/**
 * Returns an HTML element's attributes.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $slug
 * @param  string  $context
 * @param  array   $attr
 * @return string
 */
function get_attr( $slug, $context = '', $attr = array() ) {

	return attributes( $slug, $context, $attr )->fetch();
}

/**
 * <body> element attributes.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function attr_body( $attr ) {

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
function attr_post( $attr ) {

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
function attr_comment( $attr ) {

	$attr['id']    = 'comment-' . get_comment_ID();
	$attr['class'] = join( ' ', get_comment_class() );

	return $attr;
}
