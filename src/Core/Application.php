<?php
/**
 * Application class.
 *
 * This class is essentially a wrapper around the `Container` class that's
 * specific to the framework. This class is meant to be used as the single,
 * one-true instance of the framework. It's used to load up service providers
 * that interact with the container.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use Hybrid\Container\Container;
use Hybrid\Contracts\Bootable;
use Hybrid\Contracts\Core\Application as ApplicationContract;
use Hybrid\Proxies\App;
use Hybrid\Proxies\Proxy;

/**
 * Application class.
 *
 * @since  5.0.0
 *
 * @access public
 */
class Application extends Container implements ApplicationContract, Bootable {

    /**
     * The current version of the framework.
     *
     * @since  5.0.0
     *
     * @access public
     */
    const VERSION = '6.0.1';

    /**
     * Array of service provider objects.
     *
     * @since  5.0.0
     * @var    array
     *
     * @access protected
     */
    protected $providers = [];

    /**
     * Array of static proxy classes and aliases.
     *
     * @since  5.0.0
     * @var    array
     *
     * @access protected
     */
    protected $proxies = [];

    /**
     * Array of booted service providers.
     *
     * @since  6.0.0
     * @var    array
     *
     * @access protected
     */
    protected $booted_providers = [];

    /**
     * Array of registered proxies.
     *
     * @since  6.0.0
     * @var    array
     *
     * @access protected
     */
    protected $registered_proxies = [];

    /**
     * Registers the default bindings, providers, and proxies for the
     * framework.
     *
     * @since  5.0.0
     * @return void
     *
     * @access public
     */
    public function __construct() {
        $this->registerDefaultBindings();
        $this->registerDefaultProxies();
    }

    /**
     * Calls the functions to register and boot providers and proxies.
     *
     * @since  5.0.0
     * @return void
     *
     * @access public
     */
    public function boot() {
        $this->bootProviders();
        $this->registerProxies();

        if ( ! defined( 'HYBRID_BOOTED' ) ) {
            define( 'HYBRID_BOOTED', true );
        }
    }

    /**
     * Registers the default bindings we need to run the framework.
     *
     * @since  5.0.0
     * @return void
     *
     * @access protected
     */
    protected function registerDefaultBindings() {

        // Add the instance of this application.
        $this->instance( 'app', $this );

        // Adds the directory path for the framework.
        $this->instance( 'path', untrailingslashit( __DIR__ . '/..' ) );

        // Add the version for the framework.
        $this->instance( 'version', self::VERSION );
    }

    /**
     * Adds the default static proxy classes.
     *
     * @since  5.0.0
     * @return void
     *
     * @access protected
     */
    protected function registerDefaultProxies() {

        // Makes the `Hybrid\App` class an alias for the app.
        $this->proxy( App::class, '\Hybrid\App' );
    }

    /**
     * Adds a service provider.
     *
     * @since  5.0.0
     * @param  string|object $provider
     * @return void
     *
     * @access public
     */
    public function provider( $provider ) {

        // If passed a class name, resolve provider.
        if ( is_string( $provider ) ) {
            $provider = $this->resolveProvider( $provider );
        }

        // Register the provider.
        $this->registerProvider( $provider );

        // Store the provider.
        $this->providers[] = $provider;
    }

    /**
     * Creates a new instance of a service provider class.
     *
     * @since  5.0.0
     * @param  object $provider
     * @return object
     *
     * @access protected
     */
    protected function resolveProvider( $provider ) {
        return new $provider( $this );
    }

    /**
     * Calls a service provider's `register()` method if it exists.
     *
     * @since  5.0.0
     * @param  object $provider
     * @return void
     *
     * @access protected
     */
    protected function registerProvider( $provider ) {

        if ( method_exists( $provider, 'register' ) ) {
            $provider->register();
        }
    }

    /**
     * Calls a service provider's `boot()` method if it exists.
     *
     * @since  5.0.0
     * @param  object $provider
     * @return void
     *
     * @access protected
     */
    protected function bootProvider( $provider ) {

        $class_name = get_class( $provider );

        // Bail if the provider has already been booted.
        if ( in_array( $class_name, $this->booted_providers ) ) {
            return;
        }

        if ( method_exists( $provider, 'boot' ) ) {
            $provider->boot();
            $this->booted_providers[] = $class_name;
        }
    }

    /**
     * Returns an array of service providers.
     *
     * @since  5.0.0
     * @return array
     *
     * @access protected
     */
    protected function getProviders() {
        return $this->providers;
    }

    /**
     * Calls the `boot()` method of all the registered service providers.
     *
     * @since  5.0.0
     * @return void
     *
     * @access protected
     */
    protected function bootProviders() {

        foreach ( $this->getProviders() as $provider ) {
            $this->bootProvider( $provider );
        }
    }

    /**
     * Adds a static proxy alias. Developers must pass in fully-qualified
     * class name and alias class name.
     *
     * @since  5.0.0
     * @param  string $class
     * @param  string $alias
     * @return void
     *
     * @access public
     */
    public function proxy( $class, $alias ) {
        $this->proxies[ $class ] = $alias;
    }

    /**
     * Registers a static proxy class alias.
     *
     * @since  6.0.0
     * @param  string $class
     * @param  string $alias
     * @return void
     *
     * @access public
     */
    protected function registerProxy( $class, $alias ) {

        if ( ! class_exists( $alias ) ) {
            class_alias( $class, $alias );
        }

        $this->registered_proxies[] = $alias;
    }

    /**
     * Registers the static proxy classes.
     *
     * @since  5.0.0
     * @return void
     *
     * @access protected
     */
    protected function registerProxies() {

        // Only set the container on the first call.
        if ( ! $this->registered_proxies ) {
            Proxy::setContainer( $this );
        }

        foreach ( $this->proxies as $class => $alias ) {

            // Register proxy if not already registered.
            if ( ! in_array( $alias, $this->registered_proxies ) ) {
                $this->registerProxy( $class, $alias );
            }
        }
    }

}
