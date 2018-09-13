<?php
/**
 * View service provider.
 *
 * This is the service provider for the view system. The primary purpose of
 * this is to use the container as a factory for creating views. By adding this
 * to the container, it also allows the view implementation to be overwritten.
 * That way, any custom functions will utilize the new class.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\View;

use Hybrid\Tools\ServiceProvider;
use Hybrid\Contracts\View\View as ViewContract;

/**
 * View provider class.
 *
 * @since  5.0.0
 * @access public
 */
class ViewServiceProvider extends ServiceProvider {

	/**
	 * Binds the implementation of the view contract to the container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->bind( ViewContract::class, View::class );

		$this->app->alias( ViewContract::class, 'view' );
	}
}
