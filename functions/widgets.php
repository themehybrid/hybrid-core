<?php
/**
 * Sets up the core framework's widgets and unregisters some of the default WordPress widgets if the 
 * theme supports this feature.  The framework's widgets are meant to extend the default WordPress
 * widgets by giving users highly-customizable widget settings.  A theme must register support for the 
 * 'hybrid-core-widgets' feature to use the framework widgets.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Unregister WP widgets. */
add_action( 'widgets_init', 'hybrid_unregister_widgets' );

/* Register Hybrid widgets. */
add_action( 'widgets_init', 'hybrid_register_widgets' );

/**
 * Registers the core frameworks widgets.  These widgets typically overwrite the equivalent default WordPress
 * widget by extending the available options of the widget.
 *
 * @since 0.6.0
 * @uses register_widget() Registers individual widgets with WordPress
 * @link http://codex.wordpress.org/Function_Reference/register_widget
 */
function hybrid_register_widgets() {

	/* Load the core framework widget files. */
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-archives.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-authors.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-bookmarks.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-calendar.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-categories.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-nav-menu.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-pages.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-search.php' );
	require_once( trailingslashit( HYBRID_CLASSES ) . 'widget-tags.php' );

	/* Register each of the core framework widgets. */
	register_widget( 'Hybrid_Widget_Archives' );
	register_widget( 'Hybrid_Widget_Authors' );
	register_widget( 'Hybrid_Widget_Bookmarks' );
	register_widget( 'Hybrid_Widget_Calendar' );
	register_widget( 'Hybrid_Widget_Categories' );
	register_widget( 'Hybrid_Widget_Nav_Menu' );
	register_widget( 'Hybrid_Widget_Pages' );
	register_widget( 'Hybrid_Widget_Search' );
	register_widget( 'Hybrid_Widget_Tags' );
}

/**
 * Unregister default WordPress widgets that are replaced by the framework's widgets.  Widgets that
 * aren't replaced by the framework widgets are not unregistered.
 *
 * @since 0.3.2
 * @uses unregister_widget() Unregisters a registered widget.
 * @link http://codex.wordpress.org/Function_Reference/unregister_widget
 */
function hybrid_unregister_widgets() {

	/* Unregister the default WordPress widgets. */
	unregister_widget( 'WP_Widget_Archives' );
	unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'WP_Widget_Categories' );
	unregister_widget( 'WP_Widget_Links' );
	unregister_widget( 'WP_Nav_Menu_Widget' );
	unregister_widget( 'WP_Widget_Pages' );
	unregister_widget( 'WP_Widget_Recent_Posts' );
	unregister_widget( 'WP_Widget_Search' );
	unregister_widget( 'WP_Widget_Tag_Cloud' );
}

?>