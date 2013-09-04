<?php
/**
 * Hybrid Core - A WordPress theme development framework.
 *
 * Hybrid Core is a framework for developing WordPress themes.  The framework allows theme developers
 * to quickly build themes without having to handle all of the "logic" behind the theme or having to code 
 * complex functionality for features that are often needed in themes.  The framework does these things 
 * for developers to allow them to get back to what matters the most:  developing and designing themes.  
 * The framework was built to make it easy for developers to include (or not include) specific, pre-coded 
 * features.  Themes handle all the markup, style, and scripts while the framework handles the logic.
 *
 * Hybrid Core is a modular system, which means that developers can pick and choose the features they 
 * want to include within their themes.  Most files are only loaded if the theme registers support for the 
 * feature using the add_theme_support( $feature ) function within their theme.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   HybridCore
 * @version   1.6.1
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2013, Justin Tadlock
 * @link      http://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * The Hybrid class launches the framework.  It's the organizational structure behind the entire framework. 
 * This class should be loaded and initialized before anything else within the theme is called to properly use 
 * the framework.  
 *
 * After parent themes call the Hybrid class, they should perform a theme setup function on the 
 * 'after_setup_theme' hook with a priority of 10.  Child themes should add their theme setup function on
 * the 'after_setup_theme' hook with a priority of 11.  This allows the class to load theme-supported features
 * at the appropriate time, which is on the 'after_setup_theme' hook with a priority of 12.
 *
 * @since 0.7.0
 */
class Hybrid {

	/**
	 * Constructor method for the Hybrid class.  This method adds other methods of the class to 
	 * specific hooks within WordPress.  It controls the load order of the required files for running 
	 * the framework.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		global $hybrid;

		/* Set up an empty class for the global $hybrid object. */
		$hybrid = new stdClass;

		/* Define framework, parent theme, and child theme constants. */
		add_action( 'after_setup_theme', array( &$this, 'constants' ), 1 );

		/* Load the core functions required by the rest of the framework. */
		add_action( 'after_setup_theme', array( &$this, 'core' ), 2 );

		/* Initialize the framework's default actions and filters. */
		add_action( 'after_setup_theme', array( &$this, 'default_filters' ), 3 );

		/* Language functions and translations setup. */
		add_action( 'after_setup_theme', array( &$this, 'i18n' ), 4 );

		/* Handle theme supported features. */
		add_action( 'after_setup_theme', array( &$this, 'theme_support' ), 12 );

		/* Load the framework functions. */
		add_action( 'after_setup_theme', array( &$this, 'functions' ), 13 );

		/* Load the framework extensions. */
		add_action( 'after_setup_theme', array( &$this, 'extensions' ), 14 );

		/* Load admin files. */
		add_action( 'wp_loaded', array( &$this, 'admin' ) );
	}

	/**
	 * Defines the constant paths for use within the core framework, parent theme, and child theme.  
	 * Constants prefixed with 'HYBRID_' are for use only within the core framework and don't 
	 * reference other areas of the parent or child theme.
	 *
	 * @since 0.7.0
	 */
	function constants() {

		/* Sets the framework version number. */
		define( 'HYBRID_VERSION', '1.6.1' );

		/* Sets the path to the parent theme directory. */
		define( 'THEME_DIR', get_template_directory() );

		/* Sets the path to the parent theme directory URI. */
		define( 'THEME_URI', get_template_directory_uri() );

		/* Sets the path to the child theme directory. */
		define( 'CHILD_THEME_DIR', get_stylesheet_directory() );

		/* Sets the path to the child theme directory URI. */
		define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );

		/* Sets the path to the core framework directory. */
		define( 'HYBRID_DIR', trailingslashit( THEME_DIR ) . basename( dirname( __FILE__ ) ) );

		/* Sets the path to the core framework directory URI. */
		define( 'HYBRID_URI', trailingslashit( THEME_URI ) . basename( dirname( __FILE__ ) ) );

		/* Sets the path to the core framework admin directory. */
		define( 'HYBRID_ADMIN', trailingslashit( HYBRID_DIR ) . 'admin' );

		/* Sets the path to the core framework classes directory. */
		define( 'HYBRID_CLASSES', trailingslashit( HYBRID_DIR ) . 'classes' );

		/* Sets the path to the core framework extensions directory. */
		define( 'HYBRID_EXTENSIONS', trailingslashit( HYBRID_DIR ) . 'extensions' );

		/* Sets the path to the core framework functions directory. */
		define( 'HYBRID_FUNCTIONS', trailingslashit( HYBRID_DIR ) . 'functions' );

		/* Sets the path to the core framework languages directory. */
		define( 'HYBRID_LANGUAGES', trailingslashit( HYBRID_DIR ) . 'languages' );

		/* Sets the path to the core framework images directory URI. */
		define( 'HYBRID_IMAGES', trailingslashit( HYBRID_URI ) . 'images' );

		/* Sets the path to the core framework CSS directory URI. */
		define( 'HYBRID_CSS', trailingslashit( HYBRID_URI ) . 'css' );

		/* Sets the path to the core framework JavaScript directory URI. */
		define( 'HYBRID_JS', trailingslashit( HYBRID_URI ) . 'js' );
	}

	/**
	 * Loads the core framework functions.  These files are needed before loading anything else in the 
	 * framework because they have required functions for use.
	 *
	 * @since 1.0.0
	 */
	function core() {

		/* Load the core framework functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'core.php' );

		/* Load the context-based functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'context.php' );

		/* Load the core framework internationalization functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'i18n.php' );
	}

	/**
	 * Loads both the parent and child theme translation files.  If a locale-based functions file exists
	 * in either the parent or child theme (child overrides parent), it will also be loaded.  All translation 
	 * and locale functions files are expected to be within the theme's '/languages' folder, but the 
	 * framework will fall back on the theme root folder if necessary.  Translation files are expected 
	 * to be prefixed with the template or stylesheet path (example: 'templatename-en_US.mo').
	 *
	 * @since 1.2.0
	 */
	function i18n() {
		global $hybrid;

		/* Get parent and child theme textdomains. */
		$parent_textdomain = hybrid_get_parent_textdomain();
		$child_textdomain = hybrid_get_child_textdomain();

		/* Load the framework textdomain. */
		$hybrid->textdomain_loaded['hybrid-core'] = hybrid_load_framework_textdomain( 'hybrid-core' );

		/* Load theme textdomain. */
		$hybrid->textdomain_loaded[$parent_textdomain] = load_theme_textdomain( $parent_textdomain );

		/* Load child theme textdomain. */
		$hybrid->textdomain_loaded[$child_textdomain] = is_child_theme() ? load_child_theme_textdomain( $child_textdomain ) : false;

		/* Get the user's locale. */
		$locale = get_locale();

		/* Locate a locale-specific functions file. */
		$locale_functions = locate_template( array( "languages/{$locale}.php", "{$locale}.php" ) );

		/* If the locale file exists and is readable, load it. */
		if ( !empty( $locale_functions ) && is_readable( $locale_functions ) )
			require_once( $locale_functions );
	}

	/**
	 * Removes theme supported features from themes in the case that a user has a plugin installed
	 * that handles the functionality.
	 *
	 * @since 1.3.0
	 */
	function theme_support() {

		/* Remove support for the core SEO component if the WP SEO plugin is installed. */
		if ( defined( 'WPSEO_VERSION' ) )
			remove_theme_support( 'hybrid-core-seo' );

		/* Remove support for the the Breadcrumb Trail extension if the plugin is installed. */
		if ( function_exists( 'breadcrumb_trail' ) )
			remove_theme_support( 'breadcrumb-trail' );

		/* Remove support for the the Cleaner Gallery extension if the plugin is installed. */
		if ( function_exists( 'cleaner_gallery' ) )
			remove_theme_support( 'cleaner-gallery' );

		/* Remove support for the the Get the Image extension if the plugin is installed. */
		if ( function_exists( 'get_the_image' ) )
			remove_theme_support( 'get-the-image' );

		/* Remove support for the Featured Header extension if the class exists. */
		if ( class_exists( 'Featured_Header' ) )
			remove_theme_support( 'featured-header' );

		/* Remove support for the Random Custom Background extension if the class exists. */
		if ( class_exists( 'Random_Custom_Background' ) )
			remove_theme_support( 'random-custom-background' );
	}

	/**
	 * Loads the framework functions.  Many of these functions are needed to properly run the 
	 * framework.  Some components are only loaded if the theme supports them.
	 *
	 * @since 0.7.0
	 */
	function functions() {

		/* Load the comments functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'comments.php' );

		/* Load media-related functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'media.php' );

		/* Load the metadata functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'meta.php' );

		/* Load the template functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'template.php' );

		/* Load the utility functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'utility.php' );

		/* Load the wish-list functions. */
		require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'wish-list.php' );

		/* Load the theme settings functions if supported. */
		require_if_theme_supports( 'hybrid-core-theme-settings', trailingslashit( HYBRID_FUNCTIONS ) . 'settings.php' );

		/* Load the customizer functions if theme settings are supported. */
		require_if_theme_supports( 'hybrid-core-theme-settings', trailingslashit( HYBRID_FUNCTIONS ) . 'customize.php' );

		/* Load the menus functions if supported. */
		require_if_theme_supports( 'hybrid-core-menus', trailingslashit( HYBRID_FUNCTIONS ) . 'menus.php' );

		/* Load the core SEO component if supported. */
		require_if_theme_supports( 'hybrid-core-seo', trailingslashit( HYBRID_FUNCTIONS ) . 'core-seo.php' );

		/* Load the shortcodes if supported. */
		require_if_theme_supports( 'hybrid-core-shortcodes', trailingslashit( HYBRID_FUNCTIONS ) . 'shortcodes.php' );

		/* Load the sidebars if supported. */
		require_if_theme_supports( 'hybrid-core-sidebars', trailingslashit( HYBRID_FUNCTIONS ) . 'sidebars.php' );

		/* Load the widgets if supported. */
		require_if_theme_supports( 'hybrid-core-widgets', trailingslashit( HYBRID_FUNCTIONS ) . 'widgets.php' );

		/* Load the template hierarchy if supported. */
		require_if_theme_supports( 'hybrid-core-template-hierarchy', trailingslashit( HYBRID_FUNCTIONS ) . 'template-hierarchy.php' );

		/* Load the styles if supported. */
		require_if_theme_supports( 'hybrid-core-styles', trailingslashit( HYBRID_FUNCTIONS ) . 'styles.php' );

		/* Load the scripts if supported. */
		require_if_theme_supports( 'hybrid-core-scripts', trailingslashit( HYBRID_FUNCTIONS ) . 'scripts.php' );

		/* Load the media grabber script if supported. */
		require_if_theme_supports( 'hybrid-core-media-grabber', trailingslashit( HYBRID_CLASSES ) . 'hybrid-media-grabber.php' );

		/* Load the post format functionality if post formats are supported. */
		require_if_theme_supports( 'post-formats', trailingslashit( HYBRID_FUNCTIONS ) . 'post-formats.php' );

		/* Load the deprecated functions if supported. */
		require_if_theme_supports( 'hybrid-core-deprecated', trailingslashit( HYBRID_FUNCTIONS ) . 'deprecated.php' );
	}

	/**
	 * Load extensions (external projects).  Extensions are projects that are included within the 
	 * framework but are not a part of it.  They are external projects developed outside of the 
	 * framework.  Themes must use add_theme_support( $extension ) to use a specific extension 
	 * within the theme.  This should be declared on 'after_setup_theme' no later than a priority of 11.
	 *
	 * @since 0.7.0
	 */
	function extensions() {

		/* Load the Breadcrumb Trail extension if supported. */
		require_if_theme_supports( 'breadcrumb-trail', trailingslashit( HYBRID_EXTENSIONS ) . 'breadcrumb-trail.php' );

		/* Load the Cleaner Gallery extension if supported. */
		require_if_theme_supports( 'cleaner-gallery', trailingslashit( HYBRID_EXTENSIONS ) . 'cleaner-gallery.php' );

		/* Load the Get the Image extension if supported. */
		require_if_theme_supports( 'get-the-image', trailingslashit( HYBRID_EXTENSIONS ) . 'get-the-image.php' );

		/* Load the Cleaner Caption extension if supported. */
		require_if_theme_supports( 'cleaner-caption', trailingslashit( HYBRID_EXTENSIONS ) . 'cleaner-caption.php' );

		/* Load the Custom Field Series extension if supported. */
		require_if_theme_supports( 'custom-field-series', trailingslashit( HYBRID_EXTENSIONS ) . 'custom-field-series.php' );

		/* Load the Loop Pagination extension if supported. */
		require_if_theme_supports( 'loop-pagination', trailingslashit( HYBRID_EXTENSIONS ) . 'loop-pagination.php' );

		/* Load the Entry Views extension if supported. */
		require_if_theme_supports( 'entry-views', trailingslashit( HYBRID_EXTENSIONS ) . 'entry-views.php' );

		/* Load the Theme Layouts extension if supported. */
		require_if_theme_supports( 'theme-layouts', trailingslashit( HYBRID_EXTENSIONS ) . 'theme-layouts.php' );

		/* Load the Post Stylesheets extension if supported. */
		require_if_theme_supports( 'post-stylesheets', trailingslashit( HYBRID_EXTENSIONS ) . 'post-stylesheets.php' );

		/* Load the Featured Header extension if supported. */
		require_if_theme_supports( 'featured-header', trailingslashit( HYBRID_EXTENSIONS ) . 'featured-header.php' );

		/* Load the Random Custom Background extension if supported. */
		require_if_theme_supports( 'random-custom-background', trailingslashit( HYBRID_EXTENSIONS ) . 'random-custom-background.php' );

		/* Load the Color Palette extension if supported. */
		require_if_theme_supports( 'color-palette', trailingslashit( HYBRID_EXTENSIONS ) . 'color-palette.php' );

		/* Load the Theme Fonts extension if supported. */
		require_if_theme_supports( 'theme-fonts', trailingslashit( HYBRID_EXTENSIONS ) . 'theme-fonts.php' );
	}

	/**
	 * Load admin files for the framework.
	 *
	 * @since 0.7.0
	 */
	function admin() {

		/* Check if in the WordPress admin. */
		if ( is_admin() ) {

			/* Load the main admin file. */
			require_once( trailingslashit( HYBRID_ADMIN ) . 'admin.php' );

			/* Load the theme settings feature if supported. */
			require_if_theme_supports( 'hybrid-core-theme-settings', trailingslashit( HYBRID_ADMIN ) . 'theme-settings.php' );
		}
	}

	/**
	 * Adds the default framework actions and filters.
	 *
	 * @since 1.0.0
	 */
	function default_filters() {

		/* Remove bbPress theme compatibility if current theme supports bbPress. */
		if ( current_theme_supports( 'bbpress' ) )
			remove_action( 'bbp_init', 'bbp_setup_theme_compat', 8 );

		/* Move the WordPress generator to a better priority. */
		remove_action( 'wp_head', 'wp_generator' );
		add_action( 'wp_head', 'wp_generator', 1 );

		/* Add the theme info to the header (lets theme developers give better support). */
		add_action( 'wp_head', 'hybrid_meta_template', 1 );

		/* Make text widgets and term descriptions shortcode aware. */
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'term_description', 'do_shortcode' );
	}
}

?>