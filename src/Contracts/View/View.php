<?php
/**
 * View contract.
 *
 * View classes represent a template partial, generally speaking. Their purpose
 * should be to find a template file and render or display the output.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\View;

use Hybrid\Contracts\Renderable;
use Hybrid\Contracts\Displayable;

/**
 * View interface.
 *
 * @since  5.0.0
 * @access public
 */
interface View extends Renderable, Displayable {

	/**
	 * Returns the absolute path to the template file.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function template();
}
