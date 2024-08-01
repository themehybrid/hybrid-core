<?php

namespace Hybrid\Core\Configuration;

use Hybrid\Core\Application;
use Hybrid\Core\Bootstrap\RegisterProviders;
use Hybrid\Events\Provider as AppEventServiceProvider;

class ApplicationBuilder {

    /**
     * The service provider that are marked for registration.
     */
    protected array $pendingProviders = [];

    /**
     * The Folio / page middleware that have been defined by the user.
     */
    protected array $pageMiddleware = [];

    /**
     * Create a new application builder instance.
     */
    public function __construct( protected Application $app ) {}

    /**
     * Register additional service providers.
     *
     * @param array $providers
     * @param bool  $withBootstrapProviders
     * @return $this
     */
    public function withProviders( array $providers = [], bool $withBootstrapProviders = true ) {
        RegisterProviders::merge(
            $providers,
            $withBootstrapProviders
                ? $this->app->getBootstrapProvidersPath()
                : null
        );

        return $this;
    }

    /**
     * Register the core event service provider for the application.
     *
     * @param array|bool $discover
     * @return $this
     */
    public function withEvents( array|bool $discover = [] ) {
        if ( is_array( $discover ) && count( $discover ) > 0 ) {
            AppEventServiceProvider::setEventDiscoveryPaths( $discover );
        }

        if ( false === $discover ) {
            AppEventServiceProvider::disableEventDiscovery();
        }

        if ( ! isset( $this->pendingProviders[ AppEventServiceProvider::class ] ) ) {
            $this->app->booting( function () {
                $this->app->register( AppEventServiceProvider::class );
            } );
        }

        $this->pendingProviders[ AppEventServiceProvider::class ] = true;

        return $this;
    }

    /**
     * Register and configure the application's exception handler.
     *
     * @param callable|null $using
     * @return $this
     */
    public function withExceptions( ?callable $using = null ) {
        $this->app->singleton( \Hybrid\Contracts\Debug\ExceptionHandler::class, \Hybrid\Core\Exceptions\Handler::class );

        $using ??= static fn() => true;

        $this->app->afterResolving(
            \Hybrid\Core\Exceptions\Handler::class,
            static fn( $handler ) => $using( new Exceptions( $handler ) )
        );

        return $this;
    }

    /**
     * Register an array of container bindings to be bound when the application is booting.
     *
     * @param array $bindings
     * @return $this
     */
    public function withBindings( array $bindings ) {
        return $this->registered( static function ( $app ) use ( $bindings ) {
            foreach ( $bindings as $abstract => $concrete ) {
                $app->bind( $abstract, $concrete );
            }
        } );
    }

    /**
     * Register an array of singleton container bindings to be bound when the application is booting.
     *
     * @param array $singletons
     * @return $this
     */
    public function withSingletons( array $singletons ) {
        return $this->registered( static function ( $app ) use ( $singletons ) {
            foreach ( $singletons as $abstract => $concrete ) {
                if ( is_string( $abstract ) ) {
                    $app->singleton( $abstract, $concrete );
                } else {
                    $app->singleton( $concrete );
                }
            }
        } );
    }

    /**
     * Register a callback to be invoked when the application's service providers are registered.
     *
     * @param callable $callback
     * @return $this
     */
    public function registered( callable $callback ) {
        $this->app->registered( $callback );

        return $this;
    }

    /**
     * Register a callback to be invoked when the application is "booting".
     *
     * @param callable $callback
     * @return $this
     */
    public function booting( callable $callback ) {
        $this->app->booting( $callback );

        return $this;
    }

    /**
     * Register a callback to be invoked when the application is "booted".
     *
     * @param callable $callback
     * @return $this
     */
    public function booted( callable $callback ) {
        $this->app->booted( $callback );

        return $this;
    }

    /**
     * Get the application instance.
     *
     * @return \Hybrid\Core\Application
     */
    public function create() {
        return $this->app;
    }

}
