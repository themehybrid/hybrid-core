<?php
/**
 * Site functions.
 *
 * Helper functions and template tags related to the site.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

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
		'tag'        => is_front_page() ? 'h1' : 'div',
		'class'      => 'app-header__title',
		'link_class' => 'app-header__title-link'
	] );

	$html  = '';
	$title = get_bloginfo( 'name', 'display' );

	if ( $title ) {

		$link = fetch_home_link( [
			'text'  => $title,
			'class' => $args['link_class']
		] );

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $args['class'] ),
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
		'tag'   => 'div',
		'class' => 'app-header__description',
	] );

	$html = '';
	$desc = get_bloginfo( 'description', 'display' );

	if ( $desc ) {

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $args['class'] ),
			$desc
		);
	}

	return apply_filters( 'hybrid/site/description', $html );
}

/**
 * Renders the site link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_home_link( array $args = [] ) {

	echo fetch_home_link( $args );
}

/**
 * Returns the site link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_home_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'home-link',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<a class="%s" href="%s" rel="home">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( home_url() ),
		sprintf( $args['text'], get_bloginfo( 'name', 'display' ) )
	);

	return apply_filters(
		'hybrid/site/home_link',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Renders the WordPress.org link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function render_wp_link( array $args = [] ) {

	echo fetch_wp_link();
}

/**
 * Returns the WordPress.org link HTML.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function fetch_wp_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'wp-link',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( __( 'https://wordpress.org', 'hybrid-core' ) ),
		sprintf( $args['text'], esc_html__( 'WordPress', 'hybrid-core' ) )
	);

	return apply_filters(
		'hybrid/site/wp_link',
		$args['before'] . $html . $args['after']
	);
}
