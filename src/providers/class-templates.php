<?php
/**
 * Object templates service provider.
 *
 * This is the service provider for the object templates system, which binds an
 * empty collection to the container that can later be used to register templates.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Providers;

use Hybrid\Template\Templates as Manager;

/**
 * Object templates provider class.
 *
 * @since  5.0.0
 * @access public
 */
class Templates extends ServiceProvider {

	/**
	 * Registration callback that adds a single instance of the object
	 * templates collection to the container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->singleton( 'template/templates', function( $container ) {

			return new Manager();
		} );
	}
}
