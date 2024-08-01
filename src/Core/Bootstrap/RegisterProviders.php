<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Core\Application;
use Hybrid\Core\ServiceProvider;

class RegisterProviders {

    /**
     * The service providers that should be merged before registration.
     *
     * @var array
     */
    protected static $merge = [];

    /**
     * The path to the bootstrap provider configuration file.
     *
     * @var string|null
     */
    protected static $bootstrapProviderPath;

    /**
     * Bootstrap the given application.
     *
     * @param \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function bootstrap( Application $app ) {
        if ( ! $app->bound( 'config_loaded_from_cache' ) || $app->make( 'config_loaded_from_cache' ) === false ) {
            $this->mergeAdditionalProviders( $app );
        }

        $app->registerConfiguredProviders();
    }

    /**
     * Merge the additional configured providers into the configuration.
     *
     * @param \Hybrid\Contracts\Core\Application $app
     */
    protected function mergeAdditionalProviders( Application $app ) {
        if ( static::$bootstrapProviderPath && file_exists( static::$bootstrapProviderPath ) ) {
            $packageProviders = require static::$bootstrapProviderPath;

            foreach ( $packageProviders as $index => $provider ) {
                if ( ! class_exists( $provider ) ) {
                    unset( $packageProviders[ $index ] );
                }
            }
        }

        $app->make( 'config' )->set(
            'app.providers',
            array_merge(
                $app->make( 'config' )->get( 'app.providers' ) ?? ServiceProvider::defaultProviders()->toArray(),
                static::$merge,
                array_values( $packageProviders ?? [] )
            )
        );
    }

    /**
     * Merge the given providers into the provider configuration before registration.
     *
     * @param array       $providers
     * @param string|null $bootstrapProviderPath
     * @return void
     */
    public static function merge( array $providers, ?string $bootstrapProviderPath = null ) {
        static::$bootstrapProviderPath = $bootstrapProviderPath;

        static::$merge = array_values( array_filter( array_unique(
            array_merge( static::$merge, $providers )
        ) ) );
    }

    /**
     * Flush the bootstrapper's global state.
     *
     * @return void
     */
    public static function flushState() {
        static::$bootstrapProviderPath = null;

        static::$merge = [];
    }

}
