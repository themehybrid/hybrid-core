<?php
/**
 * General template functions.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Outputs the link back to the site.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_site_link() {
	echo hybrid_get_site_link();
}

/**
 * Returns a link back to the site.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_link() {
	return '<a class="site-link" href="' . home_url() . '" rel="home">' . get_bloginfo( 'name' ) . '</a>';
}

/**
 * Displays a link to WordPress.org.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_wp_link() {
	echo hybrid_get_wp_link();
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_wp_link() {
	return '<a class="wp-link" href="http://wordpress.org" title="' . esc_attr__( 'State-of-the-art semantic personal publishing platform', 'hybrid-core' ) . '">' . __( 'WordPress', 'hybrid-core' ) . '</a>';
}

/**
 * Displays a link to the parent theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_theme_link() {
	echo hybrid_get_theme_link();
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_theme_link() {
	$theme = wp_get_theme( get_template() );

	return sprintf( '<a class="theme-link" href="%s">%s</a>', esc_url( $theme->get( 'ThemeURI' ) ), $theme->display( 'Name', false, true ) ); 
}

/**
 * Displays a link to the child theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_child_theme_link() {
	echo hybrid_get_child_theme_link();
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_child_theme_link() {

	if ( !is_child_theme() )
		return '';

	$theme = wp_get_theme();

	return sprintf( '<a class="theme-link" href="%s">%s</a>', esc_url( $theme->get( 'ThemeURI' ) ), $theme->display( 'Name', false, true ) ); 
}
