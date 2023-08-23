<?php
/**
 * @license https://opensource.org/licenses/MIT
 */

namespace Hybrid\Contracts\Container;

use Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends \Exception implements ContainerExceptionInterface {

}
