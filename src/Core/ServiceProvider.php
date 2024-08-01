<?php
/**
 * Base service provider.
 *
 * This is the base service provider class. This is an abstract class that must
 * be extended to create new service providers for the application.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2024, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use Hybrid\Contracts\Core\Application;
use Hybrid\Contracts\Core\CachesConfiguration;
use Hybrid\Contracts\Core\DeferrableProvider;

/**
 * Service provider class.
 */
abstract class ServiceProvider {

    /**
     * The application instance.
     *
     * @var \Hybrid\Contracts\Core\Application
     */
    protected $app;

    /**
     * All of the registered booting callbacks.
     *
     * @var array
     */
    protected $bootingCallbacks = [];

    /**
     * All of the registered booted callbacks.
     *
     * @var array
     */
    protected $bootedCallbacks = [];

    /**
     * Create a new service provider instance.
     *
     * @param \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function __construct( Application $app ) {
        $this->app = $app;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Register a booting callback to be run before the "boot" method is called.
     *
     * @param \Closure $callback
     * @return void
     */
    public function booting( Closure $callback ) {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a booted callback to be run after the "boot" method is called.
     *
     * @param \Closure $callback
     * @return void
     */
    public function booted( Closure $callback ) {
        $this->bootedCallbacks[] = $callback;
    }

    /**
     * Call the registered booting callbacks.
     *
     * @return void
     */
    public function callBootingCallbacks() {
        $index = 0;

        while ( count( $this->bootingCallbacks ) > $index ) {
            $this->app->call( $this->bootingCallbacks[ $index ] );

            ++$index;
        }
    }

    /**
     * Call the registered booted callbacks.
     *
     * @return void
     */
    public function callBootedCallbacks() {
        $index = 0;

        while ( count( $this->bootedCallbacks ) > $index ) {
            $this->app->call( $this->bootedCallbacks[ $index ] );

            ++$index;
        }
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param string $path
     * @param string $key
     * @return void
     */
    protected function mergeConfigFrom( $path, $key ) {
        if ( ! ( $this->app instanceof CachesConfiguration && $this->app->configurationIsCached() ) ) {
            $config = $this->app->make( 'config' );

            $config->set( $key, array_merge(
                require $path, $config->get( $key, [] )
            ) );
        }
    }

    /**
     * Replace the given configuration with the existing configuration recursively.
     *
     * @param string $path
     * @param string $key
     * @return void
     */
    protected function replaceConfigRecursivelyFrom( $path, $key ) {
        if ( ! ( $this->app instanceof CachesConfiguration && $this->app->configurationIsCached() ) ) {
            $config = $this->app->make( 'config' );

            $config->set( $key, array_replace_recursive(
                require $path, $config->get( $key, [] )
            ) );
        }
    }

    /**
     * Register a view file namespace.
     *
     * @param string|array $path
     * @param string       $namespace
     * @return void
     */
    protected function loadViewsFrom( $path, $namespace ) {
        $this->callAfterResolving( 'view', function ( $view ) use ( $path, $namespace ) {
            if ( isset( $this->app->config['view']['paths'] ) && is_array( $this->app->config['view']['paths'] ) ) {
                foreach ( $this->app->config['view']['paths'] as $viewPath ) {
                    if ( is_dir( $appPath = $viewPath . '/vendor/' . $namespace ) ) {
                        $view->addNamespace( $namespace, $appPath );
                    }
                }
            }

            $view->addNamespace( $namespace, $path );
        } );
    }

    /**
     * Setup an after resolving listener, or fire immediately if already resolved.
     *
     * @param string   $name
     * @param callable $callback
     * @return void
     */
    protected function callAfterResolving( $name, $callback ) {
        $this->app->afterResolving( $name, $callback );

        if ( $this->app->resolved( $name ) ) {
            $callback( $this->app->make( $name ), $this->app );
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when() {
        return [];
    }

    /**
     * Determine if the provider is deferred.
     *
     * @return bool
     */
    public function isDeferred() {
        return $this instanceof DeferrableProvider;
    }

    /**
     * Get the default providers for a Laravel application.
     *
     * @return \Hybrid\Core\DefaultProviders
     */
    public static function defaultProviders() {
        return new DefaultProviders();
    }

    /**
     * Add the given provider to the application's provider bootstrap file.
     *
     * @param string $provider
     * @param string $path
     * @return bool
     */
    public static function addProviderToBootstrapFile( string $provider, ?string $path = null ) {
        $path ??= app()->getBootstrapProvidersPath();

        if ( ! file_exists( $path ) ) {
            return false;
        }

        if ( function_exists( 'opcache_invalidate' ) ) {
            opcache_invalidate( $path, true );
        }

        $providers = collect( require $path )
            ->merge( [ $provider ] )
            ->unique()
            ->sort()
            ->values()
            ->map( static fn( $p ) => '    ' . $p . '::class,' )
            ->implode( PHP_EOL );

        $content = '<?php

return [
' . $providers . '
];';

        file_put_contents( $path, $content . PHP_EOL );

        return true;
    }

}
