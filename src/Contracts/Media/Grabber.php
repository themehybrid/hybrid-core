<?php
/**
 * Media grabber interface.
 *
 * Defines the interface that media grabber classes must use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Media;

use Hybrid\Contracts\Renderable;
use Hybrid\Contracts\Displayable;

/**
 * Attributes interface.
 *
 * @since  5.0.0
 * @access public
 */
interface Grabber extends Renderable, Displayable {}
