<?php
/**
 * Functions for dealing with widgets and sidebars within the theme. WP widgets must be 
 * unregistered. Hybrid widgets must be registered in their place. All sidebars are loaded 
 * and registered with WP.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Add theme support for widgets.
 * @since 0.8
 */
add_theme_support( 'widgets' );

/**
 * Register widget areas
 * @since 0.7
 */
add_action( 'init', 'hybrid_register_sidebars' );

/**
 * Unregister WP widgets
 * @since 0.3.2
 */
add_action( 'widgets_init', 'hybrid_unregister_widgets' );

/**
 * Register Hybrid Widgets
 * @since 0.6
 */
add_action( 'widgets_init', 'hybrid_register_widgets' );

/**
 * Disables widget areas
 * @since 0.5
 */
add_filter( 'sidebars_widgets', 'remove_sidebars' );

/**
 * Registers each widget area for the theme. This includes all of the asides
 * and the utility widget areas throughout the theme.
 *
 * @since 0.7
 * @uses register_sidebar() Registers a widget area.
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
	register_sidebar( array( 'name' => __( 'Utility: Before Content', $domain ), 'id' => 'before-content', 'description' => __( 'Loaded before the page\'s main content area.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'Utility: After Content', $domain ), 'id' => 'after-content', 'description' => __( 'Loaded after the page\'s main content area.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'Utility: After Singular', $domain ), 'id' => 'after-singular', 'description' => __( 'Loaded on singular post (page, attachment, etc.) views before the comments area.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );

	/* Register template widget areas only if the templates are available. */
	if ( locate_template( array( 'page-widgets.php' ) ) )
		register_sidebar( array( 'name' => __( 'Widgets Template', $domain ), 'id' => 'widgets-template', 'description' => __( 'Used as the content of the Widgets page template.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	if ( locate_template( array( '404.php' ) ) )
		register_sidebar( array( 'name' => __( '404 Template', $domain ), 'id' => 'error-404-template', 'description' => __( 'Replaces the default 404 error page content.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
}

/**
 * Register Hybrid's extra widgets. Each widget is meant to replace or extend the 
 * current default WordPress widgets.
 *
 * @since 0.6
 * @uses register_widget() Registers individual widgets.
 * @link http://codex.wordpress.org/WordPress_Widgets_Api
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
 * Unregister default WordPress widgets we don't need. The theme adds its own 
 * versions of these widgets.
 *
 * @since 0.3.2
 * @uses unregister_widget() Removes individual widgets.
 * @link http://codex.wordpress.org/WordPress_Widgets_Api
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
 * @since 0.4
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_before_content() {
	get_sidebar( 'before-content' );
}

/**
 * Loads the Utility: After Content widget area. Users can overwrite 
 * 'sidebar-after-content.php' in child themes.
 *
 * @since 0.4
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_after_content() {
	get_sidebar( 'after-content' );
}

/**
 * Loads the Utility: After Singular widget area. Users can overwrite 
 * 'sidebar-after-singular.php' in child themes.
 *
 * @since 0.7
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
 * @since 0.5
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