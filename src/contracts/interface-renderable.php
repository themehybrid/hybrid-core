<?php
/**
 * Renderable contract.
 *
 * Renderable classes should implement a `render()` method. The intent of this
 * method is to output an HTML string to the screen. This data should already be
 * escaped prior to being output.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts;

/**
 * Renderable interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Renderable {

	/**
	 * Renders the HTML string.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function render();
}
