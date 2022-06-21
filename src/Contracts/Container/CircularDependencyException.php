<?php

namespace Hybrid\Contracts\Container;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface {
	//
}
