<?php

declare(strict_types = 1);

namespace Hybrid\Container\Attributes;

use Attribute;
use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualAttribute;

#[Attribute( Attribute::TARGET_PARAMETER )]
final class Tag implements ContextualAttribute {
    public function __construct(
        public string $tag
    ) {}

    /**
     * Resolve the tag.
     *
     * @param self                                  $attribute
     * @param \Hybrid\Contracts\Container\Container $container
     *
     * @return mixed
     */
    public static function resolve( self $attribute, Container $container ) {
        return $container->tagged( $attribute->tag );
    }
}
