<?php
/**
 * Font functions.
 *
 * Functions for handling font enqueueing, registration, etc.  This works with
 * the Google Fonts API. This extends an idea from Jose Castaneda. This is a
 * small script for loading Google fontswith an easy method for
 * adding/removing/editing the fonts loaded via child theme.
 *
 * @link http://blog.josemcastaneda.com/2016/02/29/adding-removing-fonts-from-a-theme/
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Registers a font.
 *
 * @uses   wp_register_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  array   $args
 * @return bool
 */
function register_font( $handle, $args = [] ) {

	$args = wp_parse_args(
		$args,
		array(
			// Arguments for https://developers.google.com/fonts/docs/getting_started
			'family'  => [],
			'subset'  => [],
			'text'    => '',
			'effect'  => [],

			// Arguments for `wp_register_style()`.
			'depends' => [],
			'version' => false,
			'media'   => 'all',
			'src'     => ''     // Will overwrite Google Fonts arguments.
		)
	);

	$url = get_font_url( $handle, $args );

	return wp_register_style( "{$handle}-font", $url, $args['depends'], $args['version'], $args['media'] );
}

/**
 * Deregisters a registered font.
 *
 * @uses   wp_deregister_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return void
 */
function deregister_font( $handle ) {

	wp_deregister_style( "{$handle}-font" );
}

/**
 * Enqueue a registered font.  If the font is not registered, pass the `$args` to
 * register it.  See `register_font()`.
 *
 * @uses   wp_enqueue_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  array   $args
 * @return void
 */
function enqueue_font( $handle, $args = [] ) {

	if ( ! font_is_registered( $handle ) ) {
		register_font( $handle, $args );
	}

	wp_enqueue_style( "{$handle}-font" );
}

/**
 * Dequeues a font.
 *
 * @uses   wp_dequeue_style()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return void
 */
function dequeue_font( $handle ) {

	wp_dequeue_style( "{$handle}-font" );
}

/**
 * Checks a font's status.
 *
 * @uses   wp_style_is()
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  string  $list
 * @return bool
 */
function font_is( $handle, $list = 'enqueued' ) {

	return wp_style_is( "{$handle}-font", $list );
}

/**
 * Checks if a font is registered.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return bool
 */
function font_is_registered( $handle ) {

	return font_is( $handle, 'registered' );
}

/**
 * Checks if a font is enqueued.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @return bool
 */
function font_is_enqueued( $handle ) {

	return font_is( $handle, 'enqueued' );
}

/**
 * Helper function for creating the Google Fonts URL.  Note that `add_query_arg()`
 * will call `urlencode_deep()`, so we're going to leaving the encoding to
 * that function.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $handle
 * @param  array   $args
 * @return void
 */
function get_font_url( $handle, $args ) {

	$font_url   = $args['src'] ? $args['src'] : '';
	$query_args = array();

	if ( ! $font_url ) {

		$family = apply_filters( "hybrid/{$handle}_font_family", $args['family'] );
		$subset = apply_filters( "hybrid/{$handle}_font_subset", $args['subset'] );
		$text   = apply_filters( "hybrid/{$handle}_font_text",   $args['text']   );
		$effect = apply_filters( "hybrid/{$handle}_font_effect", $args['effect'] );

		if ( $family ) {

			$query_args['family'] = implode( '|', (array) $family );

			if ( $subset ) {
				$query_args['subset'] = implode( ',', (array) $subset );
			}

			if ( $text ) {
				$query_args['text'] = $text;
			}

			if ( $effect ) {
				$query_args['effect'] = implode( '|', (array) $effect );
			}

			$font_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}
	}

	return esc_url( apply_filters( "hybrid/{$handle}_font_url", $font_url, $args, $query_args ) );
}
