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
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use Hybrid\Contracts\Core\Application;
use Hybrid\Contracts\Core\CachesConfiguration;

/**
 * Service provider class.
 *
 * @since  6.0.0
 *
 * @access public
 */
abstract class ServiceProvider {

    /**
     * The application instance.
     *
     * @since  6.0.0
     * @var    \Hybrid\Contracts\Core\Application
     *
     * @access protected
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
     * Accepts the application and sets it to the `$app` property.
     *
     * @since  6.0.0
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     *
     * @access public
     */
    public function __construct( Application $app ) {
        $this->app = $app;
    }

    /**
     * Register any application services.
     *
     * @since  6.0.0
     * @return void
     *
     * @access public
     */
    public function register() {}

    /**
     * Callback executed after all the service providers have been registered.
     * This is particularly useful for single-instance container objects that
     * only need to be loaded once per page and need to be resolved early.
     *
     * @since  6.0.0
     * @return void
     *
     * @access public
     */
    public function boot() {}

    /**
     * Register a booting callback to be run before the "boot" method is called.
     *
     * @param  \Closure $callback
     * @return void
     */
    public function booting( Closure $callback ) {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a booted callback to be run after the "boot" method is called.
     *
     * @param  \Closure $callback
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

        while ( $index < count( $this->bootingCallbacks ) ) {
            $this->app->call( $this->bootingCallbacks[ $index ] );

            $index++;
        }
    }

    /**
     * Call the registered booted callbacks.
     *
     * @return void
     */
    public function callBootedCallbacks() {
        $index = 0;

        while ( $index < count( $this->bootedCallbacks ) ) {
            $this->app->call( $this->bootedCallbacks[ $index ] );

            $index++;
        }
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string $path
     * @param  string $key
     * @return void
     */
    protected function mergeConfigFrom( $path, $key ) {
        if ( ! ( $this->app instanceof CachesConfiguration && $this->app->configurationIsCached() ) ) {
            $config = $this->app->make( 'config' );

            $config->set($key, array_merge(
                require $path, $config->get( $key, [] )
            ));
        }
    }

    /**
     * Register a view file namespace.
     *
     * @param  string|array $path
     * @param  string       $namespace
     * @return void
     */
    protected function loadViewsFrom( $path, $namespace ) {
        $this->callAfterResolving('view', function ( $view ) use ( $path, $namespace ) {
            if ( isset( $this->app->config['view']['paths'] ) && is_array( $this->app->config['view']['paths'] ) ) {
                foreach ( $this->app->config['view']['paths'] as $viewPath ) {
                    if ( is_dir( $appPath = $viewPath . '/vendor/' . $namespace ) ) {
                        $view->addNamespace( $namespace, $appPath );
                    }
                }
            }

            $view->addNamespace( $namespace, $path );
        });
    }

    /**
     * Setup an after resolving listener, or fire immediately if already resolved.
     *
     * @param  string   $name
     * @param  callable $callback
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

}
