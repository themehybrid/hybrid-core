<?php
/**
 * Customize service provider.
 *
 * This is the service provider for the customization API integration. It binds
 * an instance of the frameworks `Customize` class to the container.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Customize;

use Hybrid\Core\ServiceProvider;

/**
 * Customize provider.
 *
 * @since  5.0.0
 * @access public
 */
class CustomizeServiceProvider extends ServiceProvider {

	/**
	 * Registration callback that adds a single instance of the customize
	 * object to the container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->instance( 'customize', new Customize() );
	}
}
