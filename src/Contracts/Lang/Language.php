<?php
/**
 * Language interface.
 *
 * Defines the contract that a language class should use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Lang;

use Hybrid\Contracts\Bootable;

/**
 * Language interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Language extends Bootable {

	/**
	 * Returns the parent theme textdomain.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function parentTextdomain();

	/**
	 * Returns the child theme textdomain.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function childTextdomain();

	/**
	 * Returns the full directory path for the parent theme's domain path
	 * and should allow a file/path to be appended.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	public function parentPath( $file = '' );

	/**
	 * Returns the full directory path for the child theme's domain path
	 * and should allow a file/path to be appended.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	public function childPath( $file = '' );
}
