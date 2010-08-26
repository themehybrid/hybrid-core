<?php
/**
 * Functions for dealing with menus and menu items within the theme. WP menu items must be 
 * unregistered. Hybrid menu items must be registered in their place. All menus are loaded 
 * and registered with WP.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Add theme support for menus.
 * @since 0.8
 */
add_theme_support( 'menus' );

/**
 * Register menus.
 * @since 0.8
 */
add_action( 'init', 'hybrid_register_menus' );

/**
 * Registers the theme's menus.
 *
 * @since 0.8
 * @uses is_nav_menu() Checks if a menu exists.
 * @uses locate_template() Checks for template in child and parent theme.
 */
function hybrid_register_menus() {
	if ( current_theme_supports( 'hybrid-core-menus' ) )
		register_nav_menu( 'primary-menu', __( 'Primary Menu', hybrid_get_textdomain() ) );
}

/**
 * Loads the 'Primary Menu' template file.  Users can overwrite menu-primary.php in their child
 * theme folder.
 *
 * @since 0.8
 * @uses locate_template() Checks for template in child and parent theme.
 */
function hybrid_get_primary_menu() {
	locate_template( array( 'menu-primary.php', 'menu.php' ), true );
}

?>