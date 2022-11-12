<?php
/**
 * Container Exception class.
 *
 * @package   HybridCore
 * @link      https://themehybrid.com/hybrid-core
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2022, Justin Tadlock
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Container;

use Psr\Container\ContainerExceptionInterface;

class BindingResolutionException extends \Exception implements ContainerExceptionInterface {

}
