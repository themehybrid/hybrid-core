<?php

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
