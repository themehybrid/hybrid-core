<?php
/**
 * The menus functions deal with registering nav menus within WordPress for the core framework.  Theme 
 * developers may use the default menu(s) provided by the framework within their own themes, decide not
 * to use them, or register additional menus.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register nav menus. */
add_action( 'init', 'hybrid_register_menus' );

/**
 * Registers the the framework's default menus based on the menus the theme has registered support for.
 *
 * @since 0.8.0
 * @access private
 * @uses register_nav_menu() Registers a nav menu with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_nav_menu
 * @return void
 */
function hybrid_register_menus() {

	/* Get theme-supported menus. */
	$menus = get_theme_support( 'hybrid-core-menus' );

	/* If there is no array of menus IDs, return. */
	if ( !is_array( $menus[0] ) )
		return;

	/* Register the 'primary' menu. */
	if ( in_array( 'primary', $menus[0] ) )
		register_nav_menu( 'primary', _x( 'Primary', 'nav menu location', 'hybrid-core' ) );

	/* Register the 'secondary' menu. */
	if ( in_array( 'secondary', $menus[0] ) )
		register_nav_menu( 'secondary', _x( 'Secondary', 'nav menu location', 'hybrid-core' ) );

	/* Register the 'subsidiary' menu. */
	if ( in_array( 'subsidiary', $menus[0] ) )
		register_nav_menu( 'subsidiary', _x( 'Subsidiary', 'nav menu location', 'hybrid-core' ) );
}

?>