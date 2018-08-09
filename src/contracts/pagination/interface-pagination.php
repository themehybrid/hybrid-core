<?php
/**
 * Pagination interface.
 *
 * Defines the interface that pagination classes must use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Pagination;

use Hybrid\Contracts\Fetchable;
use Hybrid\Contracts\Displayable;

/**
 * Pagination interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Pagination extends Fetchable, Displayable {

	/**
	 * Builds the pagination.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return Pagination
	 */
	public function make();
}
