<?php
/**
 * App static proxy class.
 *
 * Static proxy class for the application instance.
 *
 * @package   HybridCore
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Proxies;

/**
 * App static proxy class.
 *
 * @since  5.0.0
 * @access public
 */
class App extends Proxy {

	/**
	 * Returns the name of the accessor for object registered in the container.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected static function accessor() {
		return 'app';
	}
}
