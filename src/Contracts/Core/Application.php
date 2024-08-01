<?php
/**
 * Application contract.
 *
 * The Application class should be the be the primary class for working with and
 * launching the app. It extends the `Container` contract.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2024, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Contracts\Core;

use Hybrid\Contracts\Container\Container;

/**
 * Application interface.
 *
 * @since  5.0.0
 */
interface Application extends Container {

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version();

    /**
     * Get the base path of the Hybrid installation.
     *
     * @param string $path
     * @return string
     */
    public function basePath( $path = '' );

    /**
     * Get the path to the bootstrap directory.
     *
     * @param string $path
     * @return string
     */
    public function bootstrapPath( $path = '' );

    /**
     * Get the path to the application configuration files.
     *
     * @param string $path
     * @return string
     */
    public function configPath( $path = '' );

    /**
     * Get the path to the resources directory.
     *
     * @param string $path
     * @return string
     */
    public function resourcePath( $path = '' );

    /**
     * Get or check the current application environment.
     *
     * @param string|array ...$environments
     * @return string|bool
     */
    public function environment( ...$environments );

    /**
     * Determine if the application is running in the console.
     *
     * @return bool
     */
    public function runningInConsole();

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    public function runningUnitTests();

    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders();

    /**
     * Register a service provider with the application.
     *
     * @param \Hybrid\Core\ServiceProvider|string $provider
     * @param bool                                $force
     * @return \Hybrid\Core\ServiceProvider
     */
    public function register( $provider, $force = false );

    /**
     * Register a deferred provider and service.
     *
     * @param string      $provider
     * @param string|null $service
     * @return void
     */
    public function registerDeferredProvider( $provider, $service = null );

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $provider
     * @return \Hybrid\Core\ServiceProvider
     */
    public function resolveProvider( $provider );

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot();

    /**
     * Register a new boot listener.
     *
     * @param callable $callback
     * @return void
     */
    public function booting( $callback );

    /**
     * Register a new "booted" listener.
     *
     * @param callable $callback
     * @return void
     */
    public function booted( $callback );

    /**
     * Run the given array of bootstrap classes.
     *
     * @param array $bootstrappers
     * @return void
     */
    public function bootstrapWith( array $bootstrappers );

    /**
     * Get the application namespace.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getNamespace();

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param \Hybrid\Core\ServiceProvider|string $provider
     * @return array
     */
    public function getProviders( $provider );

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped();

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders();

    /**
     * Register a terminating callback with the application.
     *
     * @param callable|string $callback
     * @return \Hybrid\Contracts\Core\Application
     */
    public function terminating( $callback );

    /**
     * Terminate the application.
     *
     * @return void
     */
    public function terminate();

}
