<?php
/**
 * Widgets and sidebars functions file.  The functions here register default sidebars and widgets for the 
 * core framework.  It also unregisters default WordPress widgets if being replaced by the framework 
 * widgets.  The framework's widgets are meant to extend the functionality of the base WordPress widgets 
 * by offering additional options to the end user.  The framework sidebars provide a starting point for theme
 * developers when developing themes off the framework.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Register widget areas. */
add_action( 'widgets_init', 'hybrid_register_sidebars' );

/* Unregister WP widgets. */
add_action( 'widgets_init', 'hybrid_unregister_widgets' );

/* Register Hybrid widgets. */
add_action( 'widgets_init', 'hybrid_register_widgets' );

/* Disables widget areas. */
add_filter( 'sidebars_widgets', 'remove_sidebars' );

/**
 * Registers the default framework dynamic sidebars.  Theme developers may optionally choose to support 
 * these sidebars within their themes or add more custom sidebars to the mix.
 *
 * @since 0.7.0
 * @uses register_sidebar() Registers a sidebar with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function hybrid_register_sidebars() {

	/* If the current theme doesn't support the Hybrid Core sidebars, return. */
	if ( !current_theme_supports( 'hybrid-core-sidebars' ) )
		return;

	/* Get the theme textdomain. */
	$domain = hybrid_get_textdomain();

	/* Register aside widget areas. */
	register_sidebar( array( 'name' => __( 'Primary', $domain ), 'id' => 'primary', 'description' => __( 'The main (primary) widget area, most often used as a sidebar.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'Secondary', $domain ), 'id' => 'secondary', 'description' => __( 'The second most important widget area, most often used as a secondary sidebar', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'Subsidiary', $domain ), 'id' => 'subsidiary', 'description' => __( 'A widget area loaded in the footer of the site.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );

	/* Register utility widget areas. */
	register_sidebar( array( 'name' => __( 'Before Content', $domain ), 'id' => 'before-content', 'description' => __( 'Loaded before the page\'s main content area.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'After Content', $domain ), 'id' => 'after-content', 'description' => __( 'Loaded after the page\'s main content area.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'After Singular', $domain ), 'id' => 'after-singular', 'description' => __( 'Loaded on singular post (page, attachment, etc.) views before the comments area.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
}

/**
 * Registers the core frameworks widgets.  These widgets typically overwrite the equivalent default WordPress
 * widget by extending the available options of the widget.
 *
 * @since 0.6.0
 * @uses register_widget() Registers individual widgets with WordPress
 * @link http://codex.wordpress.org/Function_Reference/register_widget
 */
function hybrid_register_widgets() {

	/* If the current theme doesn't support the Hybrid Core widgets, return. */
	if ( !current_theme_supports( 'hybrid-core-widgets' ) )
		return;

	/* Load each widget file. */
	require_once( HYBRID_CLASSES . '/widget-archives.php' );
	require_once( HYBRID_CLASSES . '/widget-authors.php' );
	require_once( HYBRID_CLASSES . '/widget-bookmarks.php' );
	require_once( HYBRID_CLASSES . '/widget-calendar.php' );
	require_once( HYBRID_CLASSES . '/widget-categories.php' );
	require_once( HYBRID_CLASSES . '/widget-nav-menu.php' );
	require_once( HYBRID_CLASSES . '/widget-pages.php' );
	require_once( HYBRID_CLASSES . '/widget-search.php' );
	require_once( HYBRID_CLASSES . '/widget-tags.php' );

	/* Register each widget. */
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
 * aren't replaced are not unregistered.
 *
 * @since 0.3.2
 * @uses unregister_widget() Unregisters a preexisting widget.
 * @link http://codex.wordpress.org/Function_Reference/unregister_widget
 */
function hybrid_unregister_widgets() {

	/* If the current theme doesn't support the Hybrid Core widgets, return. */
	if ( !current_theme_supports( 'hybrid-core-widgets' ) )
		return;

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

/**
 * Loads the Primary widget area. Users can overwrite 'sidebar-primary.php'.
 *
 * @since 0.2.2
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_primary() {
	get_sidebar( 'primary' );
}

/**
 * Loads the Secondary widget area. Users can overwrite 'sidebar-secondary.php'.
 *
 * @since 0.2.2
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_secondary() {
	get_sidebar( 'secondary' );
}

/**
 * Loads the Subsidiary widget area. Users can overwrite 'sidebar-subsidiary.php'.
 *
 * @since 0.3.1
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_subsidiary() {
	get_sidebar( 'subsidiary' );
}

/**
 * Loads the Utility: Before Content widget area. Users can overwrite 
 * 'sidebar-before-content.php' in child themes.
 *
 * @since 0.4.0
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_before_content() {
	get_sidebar( 'before-content' );
}

/**
 * Loads the Utility: After Content widget area. Users can overwrite 
 * 'sidebar-after-content.php' in child themes.
 *
 * @since 0.4.0
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_after_content() {
	get_sidebar( 'after-content' );
}

/**
 * Loads the Utility: After Singular widget area. Users can overwrite 
 * 'sidebar-after-singular.php' in child themes.
 *
 * @since 0.7.0
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_after_singular() {
	get_sidebar( 'after-singular' );
}

/**
 * Removes all widget areas on the No Widgets page template. We're only going to run 
 * it on the No Widgets template. Users that need additional templates without widgets 
 * should create a simliar function in their child theme.
 *
 * @since 0.5.0
 * @uses sidebars_widgets Filter to remove all widget areas
 */
function remove_sidebars( $sidebars_widgets ) {
	global $wp_query;

	if ( is_singular() ) {
		$template = get_post_meta( $wp_query->post->ID, "_wp_{$wp_query->post->post_type}_template", true );
		if ( 'no-widgets.php' == $template || "{$wp_query->post->post_type}-no-widgets.php" == $template )
			$sidebars_widgets = array( false );
	}
	return $sidebars_widgets;
}

?>