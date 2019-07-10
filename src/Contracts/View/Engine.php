<?php
/**
 * Engine contract.
 *
 * Engine classes are wrappers around the View system.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\View;

/**
 * View interface.
 *
 * @since  5.1.0
 * @access public
 */
interface Engine {

	/**
	 * Returns a View object.
	 *
	 * @since  5.1.0
	 * @access public
	 * @param  string            $name
	 * @param  array|string      $slugs
	 * @param  array|Collection  $data
	 * @return View
	 */
	public function view( $name, $slugs = [], $data = [] );

	/**
	 * Outputs a view template.
	 *
	 * @since  5.1.0
	 * @access public
	 * @param  string            $name
	 * @param  array|string      $slugs
	 * @param  array|Collection  $data
	 * @return void
	 */
	public function display( $name, $slugs = [], $data = [] );

	/**
	 * Returns a view template as a string.
	 *
	 * @since  5.1.0
	 * @access public
	 * @param  string            $name
	 * @param  array|string      $slugs
	 * @param  array|Collection  $data
	 * @return string
	 */
	function render( $name, $slugs = [], $data = [] );
}
