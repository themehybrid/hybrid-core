<?php
/**
 * Templates manager.
 *
 * This class is just a wrapper around the `Collection` class for adding a
 * specific type of data.  Essentially, we make sure that anything added to the
 * collection is in fact a `Template`.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Template;

use Hybrid\Tools\Collection;

/**
 * Template collection class.
 *
 * @since  5.0.0
 * @access public
 */
class Templates extends Collection {

	/**
	 * Add a new template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @param  mixed   $value
	 * @return void
	 */
	 public function add( $name, $value ) {

		parent::add( $name, new Template( $name, $value ) );
	}
}
