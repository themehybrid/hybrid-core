<?php
/**
 * Media meta service provider.
 *
 * This is the service provider for the media meta system, which binds an
 * empty collection to the container that can later be used to store media meta.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Media;

use Hybrid\Tools\Collection;
use Hybrid\Tools\ServiceProvider;

/**
 * Media meta provider class.
 *
 * @since  5.0.0
 * @access public
 */
class MetaServiceProvider extends ServiceProvider {

	/**
	 * Registration callback that adds a single instance of the media meta
	 * collection to the container.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->singleton( 'media/meta', function( $container ) {

			return new Collection();
		} );
	}
}
