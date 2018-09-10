<?php
/**
 * Attributes contract.
 *
 * Defines the contract that classes for building HTML attributes must adhere to.
 * Extends the `Renderable` and `Displayable` contracts for handling output.
 * Attributes are meant to be used for HTML elements.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Attr;

use Hybrid\Contracts\Renderable;
use Hybrid\Contracts\Displayable;

/**
 * Attributes interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Attributes extends Renderable, Displayable {

	/**
	 * Returns an array of HTML attributes in name/value pairs. Attributes
	 * are not expected to be escaped. Escaping should be handled on output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return array
	 */
	public function all();

	/**
	 * Returns a single, unescaped attribute's value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @return string
	 */
	public function get( $name );
}
