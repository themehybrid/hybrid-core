<?php

namespace Hybrid\Container\Attributes;

use Attribute;
use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualAttribute;

#[Attribute( Attribute::TARGET_PARAMETER )]
class Give implements ContextualAttribute {
    /**
     * Provide a concrete class implementation for dependency injection.
     *
     * @param string     $class
     * @param array|null $params
     */
    public function __construct(
        public string $class,
        public array $params = []
    ) {}

    /**
     * Resolve the dependency.
     *
     * @param self                                  $attribute
     * @param \Hybrid\Contracts\Container\Container $container
     */
    public static function resolve( self $attribute, Container $container ): mixed {
        return $container->make( $attribute->class, $attribute->params );
    }
}
