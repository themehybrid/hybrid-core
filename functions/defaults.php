<?php
/**
 * Sets up some default actions for the Hybrid parent theme.  While theme authors could certainly load
 * this file and use the Hybrid theme defaults, it's not recommended.  You'll probably find yourself 
 * overwriting the defaults more often than not.  Generally, any parent theme would add its own actions to 
 * its functions.php file, but hey, it's my framework.  So, I'm breaking the rules.  But, this file would be a 
 * good guide to follow when setting up your own functions.php file.
 *
 * @package Hybrid
 * @subpackage Functions
 */

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'hybrid_setup_theme' );

/**
 * Function for setting up all the Hybrid parent theme default actions and supported features.  This structure 
 * should be followed when creating custom parent themes with the Hybrid Core framework.
 *
 * @since 0.9
 */
function hybrid_setup_theme() {

	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	/* Add support for the core sidebars. */
	add_theme_support( 'hybrid-core-sidebars' );

	/* Add support for the core widgets. */
	add_theme_support( 'hybrid-core-widgets' );

	/* Add support for the core shortcodes. */
	add_theme_support( 'hybrid-core-shortcodes' );

	/* Add support for the core menus. */
	if ( hybrid_get_setting( 'use_menus' ) )
		add_theme_support( 'hybrid-core-menus' );

	/* Add support for the core post meta box. */
	add_theme_support( 'hybrid-core-post-meta-box' );

	/* Add support for the core SEO feature. */
	if ( !hybrid_get_setting( 'seo_plugin' ) )
		add_theme_support( 'hybrid-core-seo' );

	/* Add support for the core drop-downs script. */
	if ( hybrid_get_setting( 'superfish_js' ) )
		add_theme_support( 'hybrid-core-drop-downs' );

	/* Add support for the core print stylesheet. */
	if ( hybrid_get_setting( 'print_style' ) )
		add_theme_support( 'hybrid-core-print-style' );

	/* Add support for core theme settings meta boxes. */
	add_theme_support( 'hybrid-core-meta-box-general' );
	add_theme_support( 'hybrid-core-meta-box-footer' );

	/* Add support for the breadcrumb trail extension. */
	add_theme_support( 'breadcrumb-trail' );

	/* Add support for the custom field series extension. */
	add_theme_support( 'custom-field-series' );

	/* Add support for the Get the Image extension. */
	add_theme_support( 'get-the-image' );

	/* Add support for the Post Stylesheets extension. */
	add_theme_support( 'post-stylesheets' );

	/* If no child theme is active, add support for the Post Layouts and Pagination extensions. */
	if ( 'hybrid' == get_stylesheet() ) {
		add_theme_support( 'post-layouts' );
		add_theme_support( 'loop-pagination' );
	}

	/* Header actions. */
	add_action( "{$prefix}_header", 'hybrid_site_title' );
	add_action( "{$prefix}_header", 'hybrid_site_description' );

	/* Load the correct menu. */
	if ( hybrid_get_setting( 'use_menus' ) )
		add_action( "{$prefix}_after_header", 'hybrid_get_primary_menu' );
	else
		add_action( "{$prefix}_after_header", 'hybrid_page_nav' );

	/* Add the primary and secondary sidebars after the container. */
	add_action( "{$prefix}_after_container", 'hybrid_get_primary' );
	add_action( "{$prefix}_after_container", 'hybrid_get_secondary' );

	/* Add the breadcrumb trail and before content sidebar before the content. */
	add_action( "{$prefix}_before_content", 'hybrid_breadcrumb' );
	add_action( "{$prefix}_before_content", 'hybrid_get_utility_before_content' );

	/* Add the title, byline, and entry meta before and after the entry. */
	add_action( "{$prefix}_before_entry", 'hybrid_entry_title' );
	add_action( "{$prefix}_before_entry", 'hybrid_byline' );
	add_action( "{$prefix}_after_entry", 'hybrid_entry_meta' );

	/* Add the after singular sidebar and custom field series extension after singular views. */
	add_action( "{$prefix}_after_singular", 'hybrid_get_utility_after_singular' );
	add_action( "{$prefix}_after_singular", 'custom_field_series' );

	/* Add the after content sidebar and navigation links after the content. */
	add_action( "{$prefix}_after_content", 'hybrid_get_utility_after_content' );
	add_action( "{$prefix}_after_content", 'hybrid_navigation_links' );

	/* Add the subsidiary sidebar and footer insert to the footer. */
	add_action( "{$prefix}_before_footer", 'hybrid_get_subsidiary' );
	add_action( "{$prefix}_footer", 'hybrid_footer_insert' );

	/* Add the comment avatar and comment meta before individual comments. */
	add_action( "{$prefix}_before_comment", 'hybrid_avatar' );
	add_action( "{$prefix}_before_comment", 'hybrid_comment_meta' );
}

?>