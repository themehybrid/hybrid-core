<?php
/**
 * The menus functions deal with registering nav menus within WordPress for the core framework.  Theme 
 * developers may use the default menu(s) provided by the framework within their own themes, decide not
 * to use them, or register additional menus.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Register nav menus. */
add_action( 'init', 'hybrid_register_menus' );

/**
 * Registers the the framework's default menus.  By default, the framework registers the 'primary' menu, 
 * which is technically a location within the theme for a user-created menu to be shown.
 *
 * @since 0.8.0
 * @uses register_nav_menu() Registers a nav menu with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_nav_menu
 */
function hybrid_register_menus() {

	/* Get theme-supported sidebars. */
	$menus = get_theme_support( 'hybrid-core-menus' );

	/* If there is no array of sidebars IDs, return. */
	if ( !is_array( $menus[0] ) )
		return;

	/* Register the 'primary' menu. */
	if ( in_array( 'primary', $menus[0] ) )
		register_nav_menu( 'primary', __( 'Primary Menu', hybrid_get_textdomain() ) );

	/* Register the 'secondary' menu. */
	if ( in_array( 'secondary', $menus[0] ) )
		register_nav_menu( 'secondary', __( 'Secondary Menu', hybrid_get_textdomain() ) );

	/* Register the 'subsidiary' menu. */
	if ( in_array( 'subsidiary', $menus[0] ) )
		register_nav_menu( 'subsidiary', __( 'Subsidiary Menu', hybrid_get_textdomain() ) );
}

?>