<?php
/**
 * Template interface.
 *
 * Defines the interface that template classes must use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts;

/**
 * Template interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Template {

	/**
	 * Returns the filename relative to the templates location.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function filename();

	/**
	 * Returns the internationalized text label for the template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function label();
}
