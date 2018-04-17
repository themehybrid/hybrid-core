<?php
/**
 * Nav menu template tags.
 *
 * Template functions related to nav menus.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Outputs the nav menu theme location name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return void
 */
 function menu_location_name( $location ) {

	 return esc_html( get_menu_location_name( $location ) );
 }

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function get_menu_location_name( $location ) {

	$locations = get_registered_nav_menus();

	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}

/**
 * Outputs the nav menu name by theme location.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return void
 */
 function menu_name( $location ) {

	 echo esc_html( get_menu_name( $location ) );
 }

/**
 * Function for grabbing a WP nav menu name based on theme location.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function get_menu_name( $location ) {

	$locations = get_nav_menu_locations();

	$menu = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : '';

	return $menu ? $menu->name : '';
}
