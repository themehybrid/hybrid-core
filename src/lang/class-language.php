<?php
/**
 * Language class.
 *
 * This file holds the `Lang` class, which deals with loading textdomains and
 * locale-specific function files.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Lang;

use Hybrid\Contracts\Bootable;

/**
 * Language class.
 *
 * @since  5.0.0
 * @access public
 */
class Language implements Bootable {

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
	 * Absolute path to the parent theme's language folder. Theme authors
	 * should set the relative path via the `Domain Path` header in `style.css`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string|null
	 */
	protected $parent_path = null;

	/**
	 * Absolute path to the child theme's language folder. Theme authors
	 * should set the relative path via the `Domain Path` header in `style.css`.
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
	public function boot() {

		// Load the locale functions files.
		add_action( 'after_setup_theme', [ $this, 'loadLocaleFunctions' ], ~PHP_INT_MAX );

		// Load framework textdomain.
		add_action( 'after_setup_theme', [ $this, 'loadTextdomain' ], 95 );

		// Overrides the load textdomain function for the 'hybrid-core' domain.
		add_filter( 'override_load_textdomain', [ $this, 'overrideLoadTextdomain' ], 5, 3 );

		// Filter the textdomain mofile to allow child themes to load the parent theme translation.
		add_filter( 'load_textdomain_mofile', [ $this, 'loadTextdomainMofile' ], 10, 2 );
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
	public function loadLocaleFunctions() {

		// Get the site's locale.
		$locale = is_admin() ? get_user_locale() : get_locale();
		$locale = strtolower( str_replace( '_', '-', $locale ) );

		// Define locale functions files.
		$child_func = $this->childPath(  "{$locale}.php" );
		$theme_func = $this->parentPath( "{$locale}.php" );

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
	 * Loads the framework textdomain. Note that we're just dropping in an
	 * empty string for the MO file path. This gets overwritten by the
	 * `overrideLoadTextdomain()` filter.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function loadTextdomain() {

		load_textdomain( 'hybrid-core', '' );
	}

	/**
	 * Overrides the load textdomain functionality when `hybrid-core` is
	 * the domain in use. The purpose of this is to allow theme translations
	 * to handle the framework's strings.  What this function does is sets
	 * the `hybrid-core` domain's translations to the theme's. That way,
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
	public function overrideLoadTextdomain( $override, $domain, $mofile ) {
		global $l10n;

		// Check if the domain is one of our framework domains.
		if ( 'hybrid-core' === $domain ) {

			// Get the theme's textdomain.
			$theme_textdomain = $this->parentTextdomain();

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
	 * Filters the `load_textdomain_mofile` filter hook so that we can
	 * prepend the theme textdomain to the mofile filename. This also allows
	 * child themes to house a copy of the parent theme translations so that
	 * it doesn't get overwritten when a parent theme is updated.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string $mofile File name of the .mo file.
	 * @param  string $domain The textdomain currently being filtered.
	 * @return string
	 */
	 public function loadTextdomainMofile( $mofile, $domain ) {

		// If the `$domain` is for the parent or child theme, search for
		// a `$domain-$locale.mo` file.
		if ( $domain == $this->parentTextdomain() || $domain == $this->childTextdomain() ) {

			// Get the locale.
			$locale = is_admin() ? get_user_locale() : get_locale();

			// Define locale functions files.
			$child_mofile = $this->childPath(  "{$domain}-{$locale}.mo" );
			$theme_mofile = $this->parentPath( "{$domain}-{$locale}.mo" );

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
	public function parentTextdomain() {

		if ( ! $this->parent_textdomain ) {

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
	public function childTextdomain() {

		if ( ! $this->child_textdomain ) {

			$this->child_textdomain = wp_get_theme()->get( 'TextDomain' );
		}

		return $this->child_textdomain;
	}

	/**
	 * Returns the full directory path for the parent theme's domain path set
	 * in `style.css`. No trailing slash.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	public function parentPath( $file = '' ) {

		if ( ! $this->parent_path ) {

			$path = trim( wp_get_theme( \get_template() )->get( 'DomainPath' ), '/' );

			$this->parent_path = get_template_directory() . "/{$path}";
		}

		return $file ? "{$this->parent_path}/{$file}" : $this->parent_path;
	}

	/**
	 * Returns the full directory path for the child theme's domain path set
	 * in `style.css`. No trailing slash.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	public function childPath( $file = '' ) {

		if ( ! $this->child_path ) {

			$path = trim( wp_get_theme()->get( 'DomainPath' ), '/' );

			$this->child_path = get_stylesheet_directory() . "/{$path}";
		}

		return $file ? "{$this->child_path}/{$file}" : $this->child_path;
	}
}
