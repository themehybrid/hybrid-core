<?php
/**
 * Hybrid Core - A WordPress theme development framework.
 *
 * Hybrid Core is a framework for developing WordPress themes.  The framework allows theme developers
 * to quickly build themes without having to handle all of the "logic" behind the theme or having to
 * code complex functionality for features that are often needed in themes.  The framework does these
 * things for developers to allow them to get back to what matters the most:  developing and designing
 * themes. Themes handle all the markup, style, and scripts while the framework handles the logic.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   HybridCore
 * @version   4.0.0
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2017, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! class_exists( 'Hybrid' ) ) {

	/**
	 * The Hybrid class launches the framework.  It's the organizational structure behind the
	 * entire framework.  This file should be loaded before anything else to use the framework.
	 *
	 * Theme authors should not access this class directly. Instead, use the `hybrid()` function.
	 *
	 * @since  0.7.0
	 * @access public
	 */
	final class Hybrid {

		/**
		 * Framework version number.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $version = '4.0.0';

		/**
		 * Framework directory path with trailing slash.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $dir = '';

		/**
		 * Framework directory URI with trailing slash.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $uri = '';

		/**
		 * Parent theme directory path with trailing slash.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $parent_dir = '';

		/**
		 * Child theme directory path with trailing slash.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $child_dir = '';

		/**
		 * Parent theme directory URI with trailing slash.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $parent_uri = '';

		/**
		 * Child theme directory URI with trailing slash.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $child_uri = '';

		/**
		 * Parent theme textdomain.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $parent_textdomain = '';

		/**
		 * Child theme textdomain.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    string
		 */
		public $child_textdomain = '';

		/**
		 * Stores an array of comment templates based on comment type.  We store
		 * these globally so that we're not running unnecessary checks for posts
		 * with 100s or 1,000s of comments.
		 *
		 * @since  4.0.0
		 * @access public
		 * @var    array
		 */
		public $comment_templates = array();

		/**
		 * Returns the instance.
		 *
		 * @since  4.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {

			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new self;
				$instance->setup();
				$instance->core();
				$instance->setup_actions();
			}

			return $instance;
		}

		/**
		 * Constructor method.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
		 */
		private function __construct() {}

		/**
		 * Sets up the framework.
		 *
		 * @since  4.0.0
		 * @access private
		 * @return void
		 */
		private function setup() {

			// Theme directory paths.
			$this->parent_dir = trailingslashit( get_template_directory()   );
			$this->child_dir  = trailingslashit( get_stylesheet_directory() );

			// Theme directory URIs.
			$this->parent_uri = trailingslashit( get_template_directory_uri()   );
			$this->child_uri  = trailingslashit( get_stylesheet_directory_uri() );

			// Sets the path to the core framework directory.
			if ( ! defined( 'HYBRID_DIR' ) )
				define( 'HYBRID_DIR', trailingslashit( $this->parent_dir . basename( dirname( __FILE__ ) ) ) );

			// Sets the path to the core framework directory URI.
			if ( ! defined( 'HYBRID_URI' ) )
				define( 'HYBRID_URI', trailingslashit( $this->parent_uri . basename( dirname( __FILE__ ) ) ) );

			// Set the directory properties.
			$this->dir = HYBRID_DIR;
			$this->uri = HYBRID_URI;
		}

		/**
		 * Loads the core framework files.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
		 */
		private function core() {

			// Load the class files.
			require_once( $this->dir . 'inc/class-media-meta.php'    );
			require_once( $this->dir . 'inc/class-media-grabber.php' );
			require_once( $this->dir . 'inc/class-registry.php'      );
			require_once( $this->dir . 'inc/class-template.php'      );

			// Load the functions files.
			require_once( $this->dir . 'inc/functions-attr.php'      );
			require_once( $this->dir . 'inc/functions-context.php'   );
			require_once( $this->dir . 'inc/functions-i18n.php'      );
			require_once( $this->dir . 'inc/functions-customize.php' );
			require_once( $this->dir . 'inc/functions-filters.php'   );
			require_once( $this->dir . 'inc/functions-fonts.php'     );
			require_once( $this->dir . 'inc/functions-head.php'      );
			require_once( $this->dir . 'inc/functions-meta.php'      );
			require_once( $this->dir . 'inc/functions-sidebars.php'  );
			require_once( $this->dir . 'inc/functions-scripts.php'   );
			require_once( $this->dir . 'inc/functions-styles.php'    );
			require_once( $this->dir . 'inc/functions-templates.php' );
			require_once( $this->dir . 'inc/functions-utility.php'   );

			// Load the template files.
			require_once( $this->dir . 'inc/template.php'          );
			require_once( $this->dir . 'inc/template-comments.php' );
			require_once( $this->dir . 'inc/template-general.php'  );
			require_once( $this->dir . 'inc/template-media.php'    );
			require_once( $this->dir . 'inc/template-post.php'     );

			// Load admin files.
			if ( is_admin() )
				require_once( $this->dir . 'admin/functions-admin.php' );
		}

		/**
		 * Adds the necessary setup actions for the theme.
		 *
		 * @since  4.0.0
		 * @access private
		 * @return void
		 */
		private function setup_actions() {

			// Set up the load order.
			add_action( 'after_setup_theme', array( $this, 'theme_support' ),  15 );
			add_action( 'after_setup_theme', array( $this, 'includes'      ),  20 );
			add_action( 'after_setup_theme', array( $this, 'extensions'    ),  20 );
		}

		/**
		 * Adds theme support for features that themes should be supporting.  Also, removes
		 * theme supported features from themes in the case that a user has a plugin installed
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
		 * Loads the framework files supported by themes.  Functionality in these files should
		 * not be expected within the theme setup function.
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function includes() {

			// Load the template hierarchy if supported.
			require_if_theme_supports( 'hybrid-core-template-hierarchy', $this->dir . 'inc/class-template-hierarchy.php' );

			// Load the post format functionality if post formats are supported.
			require_if_theme_supports( 'post-formats', $this->dir . 'inc/functions-formats.php' );
			require_if_theme_supports( 'post-formats', $this->dir . 'inc/class-chat.php'        );

			// Load the Theme Layouts extension if supported.
			require_if_theme_supports( 'theme-layouts', $this->dir . 'inc/class-layout.php'      );
			require_if_theme_supports( 'theme-layouts', $this->dir . 'inc/functions-layouts.php' );

			// Load the deprecated functions if supported.
			require_if_theme_supports( 'hybrid-core-deprecated', $this->dir . 'inc/functions-deprecated.php' );

			// Load admin files.
			if ( is_admin() && current_theme_supports( 'theme-layouts' ) ) {
				require_once( $this->dir . 'admin/class-post-layout.php' );
				require_once( $this->dir . 'admin/class-term-layout.php' );
			}
		}

		/**
		 * Load extensions (external projects).  Extensions are projects that are included
		 * within the framework but are not a part of it.  They are external projects
		 * developed outside of the framework.  Themes must use `add_theme_support( $extension )`
		 * to use a specific extension within the theme.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		public function extensions() {

			hybrid_require_if_theme_supports( 'breadcrumb-trail', $this->dir . 'ext/breadcrumb-trail.php' );
			hybrid_require_if_theme_supports( 'cleaner-gallery',  $this->dir . 'ext/cleaner-gallery.php'  );
			hybrid_require_if_theme_supports( 'get-the-image',    $this->dir . 'ext/get-the-image.php'    );
		}
	}

	/**
	 * Gets the instance of the `Hybrid` class.  This function is useful for quickly grabbing data
	 * used throughout the framework.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return object
	 */
	function hybrid() {
		return Hybrid::get_instance();
	}

	// Let's do this thang!
	hybrid();
}
