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
 * want to include within their themes.  Many files are only loaded if the theme registers support for the 
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
 * @version   3.0.0-dev
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2015, Justin Tadlock
 * @link      http://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( !class_exists( 'Hybrid' ) ) {

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
	 * Note that while it is possible to extend this class, it's not usually recommended unless you absolutely 
	 * know what you're doing and expect your sub-class to break on updates.  This class often gets modifications 
	 * between versions.
	 *
	 * @since  0.7.0
	 * @access public
	 */
	class Hybrid {

		/**
		 * Constructor method for the Hybrid class.  This method adds other methods of the class to 
		 * specific hooks within WordPress.  It controls the load order of the required files for running 
		 * the framework.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {
			global $hybrid;

			// Set up an empty class for the global $hybrid object.
			$hybrid = new stdClass;

			// Define framework, parent theme, and child theme constants.
			add_action( 'after_setup_theme', array( $this, 'constants' ), -95 );

			// Load the core functions/classes required by the rest of the framework.
			add_action( 'after_setup_theme', array( $this, 'core' ), -95 );

			// Load translations.
			add_action( 'after_setup_theme', array( $this, 'i18n' ), 5 );

			// Handle theme supported features.
			add_action( 'after_setup_theme', array( $this, 'theme_support' ), 12 );

			// Load framework includes.
			add_action( 'after_setup_theme', array( $this, 'includes' ), 13 );

			// Load the framework extensions.
			add_action( 'after_setup_theme', array( $this, 'extensions' ), 14 );

			// Load admin files.
			add_action( 'wp_loaded', array( $this, 'admin' ) );
		}

		/**
		 * Defines the constant paths for use within the core framework, parent theme, and child theme.  
		 * Constants prefixed with 'HYBRID_' are for use only within the core framework and don't 
		 * reference other areas of the parent or child theme.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		public function constants() {

			// Sets the framework version number.
			define( 'HYBRID_VERSION', '3.0.0' );

			// Sets the path to the core framework directory.
			if ( !defined( 'HYBRID_DIR' ) )
				define( 'HYBRID_DIR', trailingslashit( trailingslashit( get_template_directory() ) . basename( dirname( __FILE__ ) ) ) );

			// Sets the path to the core framework directory URI.
			if ( !defined( 'HYBRID_URI' ) )
				define( 'HYBRID_URI', trailingslashit( trailingslashit( get_template_directory_uri() ) . basename( dirname( __FILE__ ) ) ) );

			// Sets the path to the core framework admin directory.
			define( 'HYBRID_ADMIN', trailingslashit( HYBRID_DIR . 'admin' ) );

			// Sets the path to the core framework includes directory.
			define( 'HYBRID_INC', trailingslashit( HYBRID_DIR . 'inc' ) );

			// Sets the path to the core framework extensions directory.
			define( 'HYBRID_EXT', trailingslashit( HYBRID_DIR . 'ext' ) );

			// Sets the path to the core framework customize directory.
			define( 'HYBRID_CUSTOMIZE', trailingslashit( HYBRID_DIR . 'customize' ) );

			// Sets the path to the core framework CSS directory URI.
			define( 'HYBRID_CSS', trailingslashit( HYBRID_URI . 'css' ) );

			// Sets the path to the core framework JavaScript directory URI.
			define( 'HYBRID_JS', trailingslashit( HYBRID_URI . 'js' ) );
		}

		/**
		 * Loads the core framework files.  These files are needed before loading anything else in the 
		 * framework because they have required functions for use.  Many of the files run filters that 
		 * theme authors may wish to remove in their theme setup functions.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function core() {

			// Load the context-based functions.
			require_once( HYBRID_INC . 'context.php' );

			// Load the core framework internationalization functions.
			require_once( HYBRID_INC . 'i18n.php' );

			// Load the framework customize functions.
			require_once( HYBRID_INC . 'customize.php' );

			// Load the framework filters.
			require_once( HYBRID_INC . 'filters.php' );

			// Load the <head> functions.
			require_once( HYBRID_INC . 'head.php' );

			// Load the metadata functions.
			require_once( HYBRID_INC . 'meta.php' );

			// Load the sidebar functions.
			require_once( HYBRID_INC . 'sidebars.php' );

			// Load the scripts functions.
			require_once( HYBRID_INC . 'scripts.php' );

			// Load the styles functions.
			require_once( HYBRID_INC . 'styles.php' );

			// Load the utility functions.
			require_once( HYBRID_INC . 'utility.php' );
		}

		/**
		 * Loads both the parent and child theme translation files.  All translations are expected 
		 * to be within the theme's '/languages' folder, but the framework will fall back on the 
		 * theme root folder if necessary.  Translation files are expected to be prefixed with the 
		 * textdomain defined in the `style.css` header.
		 *
		 * @since  1.2.0
		 * @access public
		 * @return void
		 */
		public function i18n() {

			// Load theme textdomain.
			load_theme_textdomain( hybrid_get_parent_textdomain() );

			// Load child theme textdomain.
			if ( is_child_theme() )
				load_child_theme_textdomain( hybrid_get_child_textdomain() );

			// Load the framework textdomain.
			hybrid_load_framework_textdomain();
		}

		/**
		 * Removes theme supported features from themes in the case that a user has a plugin installed
		 * that handles the functionality.
		 *
		 * @since  1.3.0
		 * @access public
		 * @return void
		 */
		public function theme_support() {

			// Automatically add <title> to head.
			add_theme_support( 'title-tag' );

			// Adds core WordPress HTML5 support.
			add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

			// Remove support for the the Breadcrumb Trail extension if the plugin is installed.
			if ( function_exists( 'breadcrumb_trail' ) || class_exists( 'Breadcrumb_Trail' ) )
				remove_theme_support( 'breadcrumb-trail' );

			// Remove support for the the Cleaner Gallery extension if the plugin is installed.
			if ( function_exists( 'cleaner_gallery' ) || class_exists( 'Cleaner_Gallery' ) )
				remove_theme_support( 'cleaner-gallery' );

			// Remove support for the the Get the Image extension if the plugin is installed.
			if ( function_exists( 'get_the_image' ) || class_exists( 'Get_The_Image' ) )
				remove_theme_support( 'get-the-image' );
		}

		/**
		 * Loads the framework files supported by themes and template-related functions/classes.  Functionality 
		 * in these files should not be expected within the theme setup function.
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function includes() {

			// Load the HTML attributes functions.
			require_once( HYBRID_INC . 'attr.php' );

			// Load the template functions.
			require_once( HYBRID_INC . 'template.php' );

			// Load the comments functions.
			require_once( HYBRID_INC . 'template-comments.php' );

			// Load the general template functions.
			require_once( HYBRID_INC . 'template-general.php' );

			// Load the media template functions.
			require_once( HYBRID_INC . 'template-media.php' );

			// Load the post template functions.
			require_once( HYBRID_INC . 'template-post.php' );

			// Load the media meta class.
			require_once( HYBRID_INC . 'class-media-meta.php'         );
			require_once( HYBRID_INC . 'class-media-meta-factory.php' );

			// Load the media grabber class.
			require_once( HYBRID_INC . 'class-media-grabber.php' );

			// Load the template hierarchy if supported.
			require_if_theme_supports( 'hybrid-core-template-hierarchy', HYBRID_INC . 'template-hierarchy.php' );

			// Load the post format functionality if post formats are supported.
			require_if_theme_supports( 'post-formats', HYBRID_INC . 'post-formats.php' );

			// Load the deprecated functions if supported.
			require_if_theme_supports( 'hybrid-core-deprecated', HYBRID_INC . 'deprecated.php' );

			// Load the Theme Layouts extension if supported.
			require_if_theme_supports( 'theme-layouts', HYBRID_INC . 'class-layouts.php' );
			require_if_theme_supports( 'theme-layouts', HYBRID_INC . 'layouts.php'       );
		}

		/**
		 * Load extensions (external projects).  Extensions are projects that are included within the 
		 * framework but are not a part of it.  They are external projects developed outside of the 
		 * framework.  Themes must use add_theme_support( $extension ) to use a specific extension 
		 * within the theme.  This should be declared on 'after_setup_theme' no later than a priority of 11.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		public function extensions() {

			// Load the Breadcrumb Trail extension if supported.
			require_if_theme_supports( 'breadcrumb-trail', HYBRID_EXT . 'breadcrumb-trail.php' );

			// Load the Cleaner Gallery extension if supported.
			require_if_theme_supports( 'cleaner-gallery', HYBRID_EXT . 'cleaner-gallery.php' );

			// Load the Get the Image extension if supported.
			require_if_theme_supports( 'get-the-image', HYBRID_EXT . 'get-the-image.php' );
		}

		/**
		 * Load admin files for the framework.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		public function admin() {

			// Load the main admin file if in admin.
			if ( is_admin() )
				require_once( HYBRID_ADMIN . 'admin.php' );
		}
	}
}
