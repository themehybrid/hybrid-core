<?php
/**
 * Base customize control.
 *
 * This is a base customize control class for our other controls to extend.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Customize\Controls;

use WP_Customize_Control;

/**
 * Multiple checkbox customize control class.
 *
 * @since  5.0.0
 * @access public
 */
abstract class Control extends WP_Customize_Control {

	/**
	 * This is the PHP callback for fetching the control content. JS-based
	 * controls require this method to be empty. Because most of our classes
	 * utilize JS templates, we're defining this in the base class to not
	 * worry about it in our sub-classes.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return bool
	 */
	protected function fetch_content() {}
}
