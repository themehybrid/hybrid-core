<?php
/**
 * @license https://opensource.org/licenses/MIT
 */

namespace Hybrid\Container;

use Hybrid\Contracts\Container\Container;
use Hybrid\Contracts\Container\ContextualBindingBuilder as ContextualBindingBuilderContract;
use Hybrid\Util;

class ContextualBindingBuilder implements ContextualBindingBuilderContract {

    /**
     * The underlying container instance.
     *
     * @var \Hybrid\Contracts\Container\Container
     */
    protected $container;

    /**
     * The concrete instance.
     *
     * @var string|array
     */
    protected $concrete;

    /**
     * The abstract target.
     *
     * @var string
     */
    protected $needs;

    /**
     * Create a new contextual binding builder.
     *
     * @param \Hybrid\Contracts\Container\Container $container
     * @param string|array                          $concrete
     */
    public function __construct( Container $container, $concrete ) {
        $this->concrete  = $concrete;
        $this->container = $container;
    }

    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string $abstract
     * @return $this
     */
    public function needs( $abstract ) {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * Define the implementation for the contextual binding.
     *
     * @param  \Closure|string|array $implementation
     * @return void
     */
    public function give( $implementation ) {
        foreach ( Util::arrayWrap( $this->concrete ) as $concrete ) {
            $this->container->addContextualBinding( $concrete, $this->needs, $implementation );
        }
    }

    /**
     * Define tagged services to be used as the implementation for the contextual binding.
     *
     * @param  string $tag
     * @return void
     */
    public function giveTagged( $tag ) {
        $this->give( static function ( $container ) use ( $tag ) {
            $taggedServices = $container->tagged( $tag );

            return is_array( $taggedServices ) ? $taggedServices : iterator_to_array( $taggedServices );
        } );
    }

    /**
     * Specify the configuration item to bind as a primitive.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return void
     */
    public function giveConfig( $key, $default = null ) {
        $this->give( static fn( $container ) => $container->get( 'config' )->get( $key, $default ) );
    }

}
