<?php

namespace Hybrid\Menu;

/**
 * Outputs the nav menu name by theme location.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return void
 */
 function render_name( $location ) {

	 echo esc_html( fetch_name( $location ) );
 }

/**
 * Function for grabbing a WP nav menu name based on theme location.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function fetch_name( $location ) {

	$locations = get_nav_menu_locations();

	$menu = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : '';

	return $menu ? $menu->name : '';
}

/**
 * Outputs the nav menu theme location name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return void
 */
function render_location( $location ) {

	echo esc_html( fetch_location( $location ) );
}

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function fetch_location( $location ) {

	$locations = get_registered_nav_menus();

	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}
