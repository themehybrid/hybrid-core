<?php
/**
 * Config service provider.
 *
 * This is the service provider for storing a collection of configuration data.
 * It binds an instance of the `Collection` class with custom config data to the
 * framework's container.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Providers;

use Hybrid\Tools\Collection;

/**
 * Configuration provider.
 *
 * @since  5.0.0
 * @access public
 */
class Config extends ServiceProvider {

	/**
	 * Registration callback that binds a single instance of the `Collection`
	 * class to the container. The config is actually a collection of other
	 * collections of data.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->singleton( 'config', function( $container ) {

			// Create a new collection to house the view config.
			$view = new Collection(
				apply_filters( 'hybrid/config/view', [
					'path'    => 'resources/views',
					'name'    => 'data',
					'extract' => true
				] )
			);

			// Create and return a new collection of config objects.
			return new Collection( [ 'view' => $view ] );
		} );
	}
}
