<?php
/**
 * Attributes service provider.
 *
 * This is the service provider for the attributes system. It's used to bind the
 * `Attributes` class to the container.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Attributes;

use Hybrid\Core\ServiceProvider;

/**
 * Attributes provider class.
 *
 * @since  5.0.0
 * @access public
 */
class AttributesServiceProvider extends ServiceProvider {

	/**
	 * Registration callback that binds the `Attributes` class to the container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->add( 'attr', function( $container, $params ) {

			return new Attributes(
				$params['name'],
				$params['context'],
				$params['attr']
			);
		} );
	}
}
