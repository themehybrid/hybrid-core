<?php
/**
 * Application interface.
 *
 * Defines the interface for the applications.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts;

/**
 * Application interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Application {

	/**
         * Calls the `register()` method of all the available service providers.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function registerProviders();

	/**
	 * Calls the `boot()` method of all the registered service providers.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function bootProviders();
}
