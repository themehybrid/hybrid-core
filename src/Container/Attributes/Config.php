<?php

namespace Hybrid\Container\Attributes;

use Attribute;
use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualAttribute;

#[Attribute( Attribute::TARGET_PARAMETER )]
class Config implements ContextualAttribute {

    /**
     * Create a new class instance.
     */
    public function __construct( public string $key, public mixed $default = null ) {}

    /**
     * Resolve the configuration value.
     *
     * @return mixed
     */
    public static function resolve( self $attribute, Container $container ) {
        return $container->make( 'config' )->get( $attribute->key, $attribute->default );
    }

}
