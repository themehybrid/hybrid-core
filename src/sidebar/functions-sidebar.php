<?php
/**
 * Sidebar functions.
 *
 * Helper functions and template tags related to sidebars.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Sidebar;

/**
 * Outputs a sidebar name.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $sidebar_id
 * @return void
 */
function render_name( $sidebar_id ) {

	echo esc_html( fetch_name( $sidebar_id ) );
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
function fetch_name( $sidebar_id ) {
	global $wp_registered_sidebars;

	return isset( $wp_registered_sidebars[ $sidebar_id ] )
	       ? $wp_registered_sidebars[ $sidebar_id ]['name']
	       : '';
}
