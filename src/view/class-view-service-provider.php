<?php
/**
 * View service provider.
 *
 * This is the service provider for the view system. It's used to bind the
 * `View` class to the container.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\View;

use Hybrid\Core\Collection;
use Hybrid\Core\ServiceProvider;

/**
 * View provider class.
 *
 * @since  5.0.0
 * @access public
 */
class ViewServiceProvider extends ServiceProvider {

	/**
	 * Registration callback that binds the `View` class to the container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->add( 'view', function( $container, $params ) {

			return new View(
				$params['name'],
				$params['slugs'],
				$params['data'] instanceof Collection ? $params['data'] : new Collection( $params['data'] )
			);
		} );
	}
}
