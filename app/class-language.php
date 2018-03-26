<?php
/**
 * Language class.
 *
 * This file holds the `Lang` class, which deals with loading theme textdomains
 * and locale-specific functions files.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Language class.
 *
 * @since  5.0.0
 * @access public
 */
class Language {

	/**
	 * The parent theme's textdomain. Gets set to the value of the `Text
	 * Domain` header in `style.css`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string|null
	 */
	protected $parent_textdomain = null;

	/**
	 * The child theme's textdomain. Gets set to the value of the `Text
	 * Domain` header in `style.css`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string|null
	 */
	protected $child_textdomain = null;

	/**
	 * The parent theme's domain path. Gets set to the value of the `Domain
	 * Path` header in `style.css`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string|null
	 */
	protected $parent_path = null;

	/**
	 * The child theme's domain path. Gets set to the value of the `Domain
	 * Path` header in `style.css`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string|null
	 */
	protected $child_path = null;

	/**
	 * Adds the class' actions and filters.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Load the locale functions file(s).
		add_action( 'after_setup_theme', [ $this, 'load_locale_functions' ], 0 );

		// Load translations for theme, child theme, and framework.
		add_action( 'after_setup_theme', [ $this, 'load_textdomain' ], 5 );

		// Overrides the load textdomain function for the 'hybrid-core' domain.
		add_filter( 'override_load_textdomain', [ $this, 'override_load_textdomain' ], 5, 3 );

		// Filter the textdomain mofile to allow child themes to load the parent theme translation.
		add_filter( 'load_textdomain_mofile', [ $this, 'load_textdomain_mofile' ], 10, 2 );
	}

	/**
	 * Loads a `/{$langpath}/{$locale}.php` file for specific locales.
	 * `$locale` should be an all lowercase and hyphenated (as opposed to
	 * an underscore) file name.  So, an `en_US` locale would be `en-us.php`.
	 * Also note that the child theme locale file will load **before** the
	 * parent theme locale file.  This is standard practice in core WP for
	 * allowing pluggable functions if a theme author so desires.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function load_locale_functions() {

		// Get the site's locale.
		$locale = strtolower( str_replace( '_', '-', is_admin() ? get_user_locale() : get_locale() ) );

		// Define locale functions files.
		$child_func = trailingslashit( $this->get_child_dir()  ) . "{$locale}.php";
		$theme_func = trailingslashit( $this->get_parent_dir() ) . "{$locale}.php";

		// If file exists in child theme.
		if ( is_child_theme() && file_exists( $child_func ) ) {
			require_once( $child_func );
		}

		// If file exists in parent theme.
		if ( file_exists( $theme_func ) ) {
			require_once( $theme_func );
		}
	}

	/**
	 * Loads the theme, child theme, and framework textdomains automatically.
	 * No need for theme authors to do this. This also utilizes the `Domain
	 * Path` header from `style.css`.  It defaults to the `languages` folder.
	 * Theme authors should define this as `/lang`, `/languages` or some
	 * other variation of their choosing.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {

		// Load theme textdomain.
		load_theme_textdomain( $this->get_parent_textdomain(), $this->get_parent_dir() );

		// Load child theme textdomain.
		if ( is_child_theme() ) {

			load_child_theme_textdomain( $this->get_child_textdomain(), $this->get_child_dir() );
		}

		// Load the framework textdomain.
		load_textdomain( 'hybrid-core', '' );
	}

	/**
	 * Overrides the load textdomain functionality when 'hybrid-core' is
	 * the domain in use.  The purpose of this is to allow theme translations
	 * to handle the framework's strings.  What this function does is sets
	 * the 'hybrid-core' domain's translations to the theme's.  That way,
	 * we're not loading multiple of the same MO files.
	 *
	 * @since  5.0.0
	 * @access public
	 * @global array   $l10n
	 * @param  bool    $override
	 * @param  string  $domain
	 * @param  string  $mofile
	 * @return bool
	 */
	public function override_load_textdomain( $override, $domain, $mofile ) {
		global $l10n;

		// Check if the domain is one of our framework domains.
		if ( 'hybrid-core' === $domain ) {

			// Get the theme's textdomain.
			$theme_textdomain = $this->get_parent_textdomain();

			// If the theme's textdomain is loaded, use its translations instead.
			if ( $theme_textdomain && isset( $l10n[ $theme_textdomain ] ) ) {

				$l10n[ $domain ] = $l10n[ $theme_textdomain ];
			}

			// Always override.  We only want the theme to handle translations.
			$override = true;
		}

		return $override;
	}

	/**
	 * Filters the 'load_textdomain_mofile' filter hook so that we can change
	 * the directory and file name of the mofile for translations.  This
	 * allows child themes to have a folder called /languages with translations
	 * of their parent theme so that the translations aren't lost on a parent
	 * theme upgrade.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string $mofile File name of the .mo file.
	 * @param  string $domain The textdomain currently being filtered.
	 * @return string
	 */
	 public function load_textdomain_mofile( $mofile, $domain ) {

		// If the `$domain` is for the parent or child theme, search for a `$domain-$locale.mo` file.
		if ( $domain == $this->get_parent_textdomain() || $domain == $this->get_child_textdomain() ) {

			// Get the locale.
			$locale = is_admin() ? get_user_locale() : get_locale();

			// Define locale functions files.
			$child_mofile = trailingslashit( $this->get_child_dir() )  . "{$domain}-{$locale}.mo";
			$theme_mofile = trailingslashit( $this->get_parent_dir() ) . "{$domain}-{$locale}.mo";

			// Overwrite the mofile if it exists.
			if ( is_child_theme() && file_exists( $child_mofile ) ) {

				$mofile = $child_mofile;

			} elseif ( file_exists( $theme_mofile ) ) {

				$mofile = $theme_mofile;
			}
		}

		return $mofile;
	}

	/**
	 * Gets the parent theme textdomain. This allows the framework to
	 * recognize the proper textdomain of the parent theme.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function get_parent_textdomain() {

		if ( is_null( $this->parent_textdomain ) ) {

			$this->parent_textdomain = wp_get_theme( \get_template() )->get( 'TextDomain' );
		}

		return $this->parent_textdomain;
	}

	/**
	 * Gets the child theme textdomain. This allows the framework to
	 * recognize the proper textdomain of the child theme.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function get_child_textdomain() {

		if ( is_null( $this->child_textdomain ) ) {

			$this->child_textdomain = wp_get_theme()->get( 'TextDomain' );
		}

		return $this->child_textdomain;
	}

	/**
	 * Returns the full directory path for the parent theme's
	 * domain path set in `style.css`. No trailing slash.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function get_parent_dir() {

		return trailingslashit( app()->parent_dir ) . $this->get_parent_path();
	}

	/**
	 * Returns the full directory path for the child theme's
	 * domain path set in `style.css`. No trailing slash.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function get_child_dir() {

		return trailingslashit( app()->child_dir ) . $this->get_child_path();
	}

	/**
	 * Returns the parent theme domain path.  No slash.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function get_parent_path() {

		if ( is_null( $this->parent_path ) ) {

			$this->parent_path = trim( wp_get_theme( \get_template() )->get( 'DomainPath' ), '/' );
		}

		return $this->parent_path;
	}

	/**
	 * Returns the child theme domain path.  No slash.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function get_child_path() {

		if ( is_null( $this->child_path ) ) {

			$this->child_path = trim( wp_get_theme()->get( 'DomainPath' ), '/' );
		}

		return $this->child_path;
	}
}
