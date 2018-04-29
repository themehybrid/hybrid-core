<?php
/**
 * Fetchable interface.
 *
 * Defines the interface that any class that fetches output should use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts;

/**
 * Fetchable interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Fetchable {

	/**
	 * Fetches the content.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function fetch();
}
