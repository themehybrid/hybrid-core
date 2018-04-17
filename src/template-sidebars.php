<?php
/**
 * Sidebar template tags.
 *
 * Template tags related to widgets and sidebars for use within theme templates.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Outputs a sidebar name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $sidebar_id
 * @return void
 */
 function sidebar_name( $sidebar_id ) {

	 echo esc_html( get_sidebar_name( $sidebar_id ) );
 }

/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @since  5.0.0
 * @access public
 * @global array   $wp_registered_sidebars
 * @param  string  $sidebar_id
 * @return string
 */
function get_sidebar_name( $sidebar_id ) {
	global $wp_registered_sidebars;

	return isset( $wp_registered_sidebars[ $sidebar_id ] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : '';
}

/**
 * Checks if a widget exists.  Pass in the widget class name.  This function is
 * useful for checking if the widget exists before directly calling `the_widget()`
 * within a template.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $widget
 * @return bool
 */
function widget_exists( $widget ) {

	return isset( $GLOBALS['wp_widget_factory']->widgets[ $widget ] );
}
