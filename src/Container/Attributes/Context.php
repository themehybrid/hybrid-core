<?php

namespace Hybrid\Container\Attributes;

use Attribute;
use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualAttribute;
use Hybrid\Log\Context\Repository;

#[Attribute( Attribute::TARGET_PARAMETER )]
class Context implements ContextualAttribute {
    /**
     * Create a new attribute instance.
     */
    public function __construct( public string $key, public mixed $default = null, public bool $hidden = false ) {}

    /**
     * Resolve the context value.
     *
     * @param self                                  $attribute
     * @param \Hybrid\Contracts\Container\Container $container
     */
    public static function resolve( self $attribute, Container $container ): mixed {
        $repository = $container->make( Repository::class );

        return match ( $attribute->hidden ) {
            true => $repository->getHidden( $attribute->key, $attribute->default ),
            false => $repository->get( $attribute->key, $attribute->default ),
        };
    }
}
