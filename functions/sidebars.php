<?php
/**
 * Sets up the default framework sidebars if the theme supports them.  By default, the framework registers 
 * seven sidebars.  Themes may choose to use one or more of these sidebars.  A theme must register support 
 * for 'hybrid-core-sidebars' to use them and register each sidebar ID within an array for the second 
 * parameter of add_theme_support().
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2012, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register widget areas. */
add_action( 'widgets_init', 'hybrid_register_sidebars' );

/**
 * Registers the default framework dynamic sidebars based on the sidebars the theme has added support 
 * for using add_theme_support().
 *
 * @since 0.7.0
 * @access public
 * @uses register_sidebar() Registers a sidebar with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @return void
 */
function hybrid_register_sidebars() {

	/* Get the theme-supported sidebars. */
	$supported_sidebars = get_theme_support( 'hybrid-core-sidebars' );

	/* If the theme doesn't add support for any sidebars, return. */
	if ( !is_array( $supported_sidebars[0] ) )
		return;

	/* Get the available core framework sidebars. */
	$core_sidebars = hybrid_get_sidebars();

	/* Loop through the supported sidebars. */
	foreach ( $supported_sidebars[0] as $sidebar ) {

		/* Make sure the given sidebar is one of the core sidebars. */
		if ( isset( $core_sidebars[ $sidebar ] ) ) {

			/* Set up some default sidebar arguments. */
			$defaults = array(
				'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>'
			);

			/* Allow developers to filter the default sidebar arguments. */
			$defaults = apply_filters( hybrid_get_prefix() . '_sidebar_defaults', $defaults, $sidebar );

			/* Parse the sidebar arguments and defaults. */
			$args = wp_parse_args( $core_sidebars[ $sidebar ], $defaults );

			/* If no 'id' was given, use the $sidebar variable and sanitize it. */
			$args['id'] = ( isset( $args['id'] ) ? sanitize_key( $args['id'] ) : sanitize_key( $sidebar ) );

			/* Allow developers to filter the sidebar arguments. */
			$args = apply_filters( hybrid_get_prefix() . '_sidebar_args', $args, $sidebar );

			/* Register the sidebar. */
			register_sidebar( $args );
		}
	}
}

/**
 * Returns an array of the core framework's available sidebars for use in themes.  We'll just set the 
 * ID (array keys), name, and description of each sidebar.  The other sidebar arguments will be set when the 
 * sidebar is registered.
 *
 * @since 1.2.0
 * @access public
 * @return array $sidebars All the available framework sidebars.
 */
function hybrid_get_sidebars() {

	/* Set up an array of sidebars. */
	$sidebars = array(
		'primary' => array(
			'name'        => _x( 'Primary', 'sidebar', 'hybrid-core' ),
			'description' => __( 'The main (primary) widget area, most often used as a sidebar.', 'hybrid-core' )
		),
		'secondary' => array(
			'name'        => _x( 'Secondary', 'sidebar', 'hybrid-core' ),
			'description' => __( 'The second most important widget area, most often used as a secondary sidebar.', 'hybrid-core' ),
		),
		'subsidiary' => array(
			'name'        => _x( 'Subsidiary', 'sidebar', 'hybrid-core' ),
			'description' => __( 'A widget area loaded in the footer of the site.', 'hybrid-core' ),
		),
		'header' => array(
			'name'        => _x( 'Header', 'sidebar', 'hybrid-core' ),
			'description' => __( "Displayed within the site's header area.", 'hybrid-core' ),
		),
		'before-content' => array(
			'name'        => _x( 'Before Content', 'sidebar', 'hybrid-core' ),
			'description' => __( "Loaded before the page's main content area.", 'hybrid-core' ),
		),
		'after-content' => array(
			'name'        => _x( 'After Content', 'sidebar', 'hybrid-core' ),
			'description' => __( "Loaded after the page's main content area.", 'hybrid-core' ),
		),
		'after-singular' => array(
			'name'        => _x( 'After Singular', 'sidebar', 'hybrid-core' ),
			'description' => __( 'Loaded on singular post (page, attachment, etc.) views before the comments area.', 'hybrid-core' ),
		)
	);

	/* Return the sidebars. */
	return $sidebars;
}

?>