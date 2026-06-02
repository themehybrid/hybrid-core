<?php

namespace Hybrid\Container\Attributes;

use Attribute;
use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualAttribute;

#[Attribute( Attribute::TARGET_PARAMETER )]
class Cache implements ContextualAttribute {
    /**
     * Create a new class instance.
     */
    public function __construct( public ?string $store = null ) {}

    /**
     * Resolve the cache store.
     *
     * @param self                                  $attribute
     * @param \Hybrid\Contracts\Container\Container $container
     *
     * @return \Hybrid\Contracts\Cache\Repository
     */
    public static function resolve( self $attribute, Container $container ) {
        return $container->make( 'cache' )->store( $attribute->store );
    }
}
