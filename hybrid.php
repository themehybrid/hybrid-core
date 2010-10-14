<?php
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
 * @package HybridCore
 * @subpackage Classes
 */

class Hybrid {

	/**
	 * Theme prefix (mostly used for hooks).
	 *
	 * @since 0.7
	 * @var string
	 */
	var $prefix;

	/**
	 * PHP4 constructor method.  This simply provides backwards compatibility for users with setups
	 * on older versions of PHP.  Once WordPress no longer supports PHP4, this method will be removed.
	 *
	 * @since 0.9
	 */
	function Hybrid() {
		$this->__construct();
	}

	/**
	 * Constructor method for the Hybrid class.  Initializes the theme framework, loads the 
	 * required files, and calls the functions needed to run the framework.
	 *
	 * @since 0.9.1
	 */
	function __construct() {

		/* Define theme constants. */
		$this->constants();

		/* Load the core theme functions. */
		$this->core();

		/* Load admin files. */
		$this->admin();

		/* Theme prefix for creating things such as filter hooks (i.e., "$prefix_hook_name"). */
		$this->prefix = hybrid_get_prefix();

		/* Initialize the theme's default actions. */
		$this->actions();

		/* Initialize the theme's default filters. */
		$this->filters();

		/* Load theme framework functions. */
		add_action( 'after_setup_theme', array( &$this, 'functions' ), 12 );

		/* Load theme extensions later since we need to check if they're supported. */
		add_action( 'after_setup_theme', array( &$this, 'extensions' ), 12 );

		/* Load theme textdomain. */
		$domain = hybrid_get_textdomain();
		$locale = get_locale();
		load_theme_textdomain( $domain );

		/* Load locale-specific functions file. */
		$locale_functions = locate_template( array( "languages/{$locale}.php", "{$locale}.php" ) );
		if ( !empty( $locale_functions ) && is_readable( $locale_functions ) )
			require_once( $locale_functions );

		/* Theme init hook. */
		do_action( "{$this->prefix}_init" );
	}

	/**
	 * Defines the constant paths for use within the core framework, parent theme, and
	 * child theme.  Constants prefixed with 'HYBRID_' are for use only within the core
	 * framework and don't reference other areas of the theme.
	 *
	 * @since 0.7
	 */
	function constants() {
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
	 * @since 0.9.1
	 */
	function core() {

		/* Load the core framework functions. */
		require_once( HYBRID_FUNCTIONS . '/core.php' );

		/* Load the context-based functions. */
		require_once( HYBRID_FUNCTIONS . '/context.php' );
	}

	/**
	 * Loads the framework functions.  Many of these functions are needed to properly run the 
	 * framework.  Some components are only loaded if the theme supports them.
	 *
	 * @since 0.7
	 */
	function functions() {

		/* Load the comments functions. */
		require_once( HYBRID_FUNCTIONS . '/comments.php' );

		/* Load media-related functions. */
		require_once( HYBRID_FUNCTIONS . '/media.php' );

		/* Load the template functions. */
		require_once( HYBRID_FUNCTIONS . '/template.php' );

		/* Load the widget functions. */
		require_once( HYBRID_FUNCTIONS . '/widgets.php' );

		/* Load the menus functions if supported. */
		require_if_theme_supports( 'hybrid-core-menus', HYBRID_FUNCTIONS . '/menus.php' );

		/* Load the temporary core SEO component. */
		require_if_theme_supports( 'hybrid-core-seo', HYBRID_FUNCTIONS . '/core-seo.php' );

		/* Load the shortcodes if supported. */
		require_if_theme_supports( 'hybrid-core-shortcodes', HYBRID_FUNCTIONS . '/shortcodes.php' );

		/* Load the template hierarchy if supported. */
		require_if_theme_supports( 'hybrid-core-template-hierarchy', HYBRID_FUNCTIONS . '/template-hierarchy.php' );

		/* Load the deprecated functions if supported. */
		require_if_theme_supports( 'hybrid-core-deprecated', HYBRID_FUNCTIONS . '/deprecated.php' );
	}

	/**
	 * Load extensions (external projects).  Extensions are projects that are included within the 
	 * framework but are not a part of it.  They are external projects developed outside of the 
	 * framework.  Themes must use add_theme_support( $extension ) to use a specific extension 
	 * within the theme.  This should be declared on 'after_setup_theme' no later than a priority of 11.
	 *
	 * @since 0.7
	 */
	function extensions() {

		/* Load the Breadcrumb Trail extension if supported. */
		require_if_theme_supports( 'breadcrumb-trail', HYBRID_EXTENSIONS . '/breadcrumb-trail.php' );

		/* Load the Custom Field Series extension if supported. */
		require_if_theme_supports( 'custom-field-series', HYBRID_EXTENSIONS . '/custom-field-series.php' );

		/* Load the Get the Image extension if supported. */
		require_if_theme_supports( 'get-the-image', HYBRID_EXTENSIONS . '/get-the-image.php' );

		/* Load the Get the Object extension if supported. */
		require_if_theme_supports( 'get-the-object', HYBRID_EXTENSIONS . '/get-the-object.php' );

		/* Load the Pagination extension if supported. */
		require_if_theme_supports( 'loop-pagination', HYBRID_EXTENSIONS . '/loop-pagination.php' );

		/* Load the Entry Views extension if supported. */
		require_if_theme_supports( 'entry-views', HYBRID_EXTENSIONS . '/entry-views.php' );

		/* Load the Post Layouts extension if supported. */
		require_if_theme_supports( 'post-layouts', HYBRID_EXTENSIONS . '/post-layouts.php' );

		/* Load the Post Stylesheets extension if supported. */
		require_if_theme_supports( 'post-stylesheets', HYBRID_EXTENSIONS . '/post-stylesheets.php' );
	}

	/**
	 * Load admin files for the framework.
	 *
	 * @since 0.7
	 */
	function admin() {

		/* Check if in the WordPress admin. */
		if ( is_admin() ) {

			/* Load the main admin file. */
			require_once( HYBRID_ADMIN . '/admin.php' );

			/* Load the post meta box file. */
			require_once( HYBRID_ADMIN . '/meta-box.php' );

			/* Load the settings page file. */
			require_once( HYBRID_ADMIN . '/settings-page.php' );
		}
	}

	/**
	 * Adds the default theme actions.
	 *
	 * @since 0.7
	 */
	function actions() {

		/* Remove WP and plugin functions. */
		remove_action( 'wp_head', 'wp_generator' );

		/* Head actions. */
		add_action( 'wp_head', 'wp_generator', 1 );
		add_action( 'wp_head', 'hybrid_meta_template', 1 );
		add_action( 'wp_head', 'hybrid_head_pingback' );

		/* WP print scripts and styles. */
		add_action( 'template_redirect', 'hybrid_enqueue_style' );
		add_action( 'template_redirect', 'hybrid_enqueue_script' );
	}

	/**
	 * Adds the default theme filters.
	 *
	 * @since 0.7
	 */
	function filters() {
		/* Filter the textdomain mofile to allow child themes to load the parent theme translation. */
		add_filter( 'load_textdomain_mofile', 'hybrid_load_textdomain', 10, 2 );

		/* Add same filters to user description as term descriptions. */
		add_filter( 'get_the_author_description', 'wptexturize' );
		add_filter( 'get_the_author_description', 'convert_chars' );
		add_filter( 'get_the_author_description', 'wpautop' );

		/* Make text widgets and term descriptions shortcode aware. */
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'term_description', 'do_shortcode' );

		/* Stylesheet filters. */
		add_filter( 'stylesheet_uri', 'hybrid_debug_stylesheet', 10, 2 );
	}
}

?>