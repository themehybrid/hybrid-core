<?php

namespace Hybrid\Container\Attributes;

use Attribute;
use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualAttribute;

#[Attribute( Attribute::TARGET_PARAMETER )]
class Storage implements ContextualAttribute {

    /**
     * Create a new class instance.
     */
    public function __construct( public ?string $disk = null ) {}

    /**
     * Resolve the storage disk.
     *
     * @param self                                  $attribute
     * @param \Hybrid\Contracts\Container\Container $container
     * @return \Hybrid\Contracts\Filesystem\Filesystem
     */
    public static function resolve( self $attribute, Container $container ) {
        return $container->make( 'filesystem' )->disk( $attribute->disk );
    }

}
