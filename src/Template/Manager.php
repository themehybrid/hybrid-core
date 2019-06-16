<?php
/**
 * Template manager.
 *
 * This class is used to boot the templates manager and handle its action and
 * filter hooks.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Template;

use Hybrid\Contracts\Bootable;

/**
 * Template manager class.
 *
 * @since  5.0.0
 * @access public
 */
class Manager implements Bootable {

	/**
	 * Templates collection.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    Templates
	 */
	protected $templates;

	/**
	 * Sets the initial templates collection.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  Templates  $templates
	 * @return void
	 */
	public function __construct( Templates $templates ) {

		$this->templates = $templates;
	}

	/**
	 * Sets up the templates manager actions and filters.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		// Add registration callback.
		add_action( 'init', [ $this, 'register' ], 95 );

		// Filter theme post templates to add registered templates.
		add_filter( 'theme_templates', [ $this, 'postTemplates' ], 5, 4 );
	}

	/**
	 * Executes the action hook for themes to register their templates.
	 * Themes should always register on this hook.
	 *
	 * Note that this method is `public` because of WP's hook callback
	 * system. See the implemented contract for publicly-available methods.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		do_action( 'hybrid/templates/register', $this->templates );
	}

	/**
	 * Filter used on `theme_templates` to add custom templates to the template
	 * drop-down.
	 *
	 * Note that this method is `public` because of WP's hook callback
	 * system. See the implemented contract for publicly-available methods.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  array   $templates
	 * @param  object  $theme
	 * @param  object  $post
	 * @param  string  $post_type
	 * @return array
	 */
	public function postTemplates( $templates, $theme, $post, $post_type ) {

		foreach ( $this->templates->all() as $template ) {

			if ( $template->forPostType( $post_type ) ) {

				$templates[ $template->filename() ] = esc_html( $template->label() );
			}
		}

		return $templates;
	}
}
