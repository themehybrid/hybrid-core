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

use Closure;
use Hybrid\Container\Container;
use Hybrid\Contracts\Bootable;
use Hybrid\Contracts\Core\Application as ApplicationContract;
use Hybrid\Contracts\Filesystem\Factory;
use Hybrid\Core\Bootstrap\BootProviders;
use Hybrid\Core\Bootstrap\GenerateStorageStructures;
use Hybrid\Core\Bootstrap\LoadConfiguration;
use Hybrid\Core\Bootstrap\LoadEnvironmentVariables;
use Hybrid\Core\Bootstrap\RegisterFacades;
use Hybrid\Core\Bootstrap\RegisterProviders;
use Hybrid\Core\Providers\CoreServiceProvider;
use Hybrid\Events\Dispatcher;
use Hybrid\Events\Provider as EventServiceProvider;
use Hybrid\Filesystem\Filesystem;
use Hybrid\Filesystem\FilesystemManager;
use Hybrid\Filesystem\FilesystemServiceProvider;
use Hybrid\Tools\Arr;
use Hybrid\Tools\Collection;
use Hybrid\Tools\Config\Repository;
use Hybrid\Tools\Env;
use Hybrid\Tools\Str;
use Psr\Container\ContainerInterface;

/**
 * Application class.
 */
class Application extends Container implements ApplicationContract, Bootable {

    /**
     * The current version of the framework.
     */
    const VERSION = '7.0.0-alpha.3';

    /**
     * The base path for the Hybrid installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Indicates if the application has been bootstrapped before.
     *
     * @var bool
     */
    protected $hasBeenBootstrapped = false;

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * The array of booting callbacks.
     *
     * @var array<callable>
     */
    protected $bootingCallbacks = [];

    /**
     * The array of booted callbacks.
     *
     * @var array<callable>
     */
    protected $bootedCallbacks = [];

    /**
     * All of the registered service providers.
     *
     * @var array<\Hybrid\Core\ServiceProvider>
     */
    protected $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = [];

    /**
     * The custom application path defined by the developer.
     *
     * @var string
     */
    protected $appPath;

    /**
     * The custom storage path defined by the developer.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The custom config path defined by the developer.
     *
     * @var string
     */
    protected $configPath;

    /**
     * The custom environment path defined by the developer.
     *
     * @var string
     */
    protected $environmentPath;

    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * Indicates if the application is running in the console.
     *
     * @var bool|null
     */
    protected $isRunningInConsole;

    /**
     * The application namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * The prefixes of absolute cache paths for use during normalization.
     *
     * @var array<string>
     */
    protected $absoluteCachePathPrefixes = [ '/', '\\' ];

    /**
     * The bootstrap classes for the application.
     *
     * @var array<string>
     */
    protected $bootstrappers = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        GenerateStorageStructures::class,
        // HandleExceptions::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    /**
     * Create a new Hybrid Core application instance.
     *
     * @param  string|null $basePath
     * @return void
     */
    public function __construct( $basePath = null, $bootstrap = true ) {
        if ( ! $basePath && defined( 'WP_CONTENT_DIR' ) ) {
            $basePath = WP_CONTENT_DIR . '/hybrid-core';
        }

        if ( $basePath ) {
            $this->setBasePath( $basePath );
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();

        /*
         * These bootstrappers load configuration, detect the application environment,
         * and perform other tasks that need to be done before the boot().
         * Typically, these classes handle internal Hybrid Core configuration that you do not need to worry about.
         */
        if ( $bootstrap ) {
            $this->bootstrap();
        }
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version() {
        return self::VERSION;
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings() {
        static::setInstance( $this );

        $this->instance( 'app', $this );

        $this->instance( Container::class, $this );
        $this->singleton( Mix::class );

        $this->singleton(PackageManifest::class, fn() => new PackageManifest(
            new Filesystem(), $this->basePath(), $this->getCachedPackagesPath()
        ));
    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders() {
        $this->register( new CoreServiceProvider( $this ) );
        $this->register( new EventServiceProvider( $this ) );
        $this->register( new FilesystemServiceProvider( $this ) );
    }

    /**
     * Run the given array of bootstrap classes.
     *
     * @param  array<string> $bootstrappers
     * @return void
     */
    public function bootstrapWith( array $bootstrappers ) {
        $this->hasBeenBootstrapped = true;

        foreach ( $bootstrappers as $bootstrapper ) {
            $this['events']->dispatch( 'bootstrapping: ' . $bootstrapper, [ $this ] );

            $this->make( $bootstrapper )->bootstrap( $this );

            $this['events']->dispatch( 'bootstrapped: ' . $bootstrapper, [ $this ] );
        }
    }

    /**
     * Register a callback to run after loading the environment.
     *
     * @return void
     */
    public function afterLoadingEnvironment( Closure $callback ) {
        $this->afterBootstrapping( LoadEnvironmentVariables::class, $callback );
    }

    /**
     * Register a callback to run before a bootstrapper.
     *
     * @param  string $bootstrapper
     * @return void
     */
    public function beforeBootstrapping( $bootstrapper, Closure $callback ) {
        $this['events']->listen( 'bootstrapping: ' . $bootstrapper, $callback );
    }

    /**
     * Register a callback to run after a bootstrapper.
     *
     * @param  string $bootstrapper
     * @return void
     */
    public function afterBootstrapping( $bootstrapper, Closure $callback ) {
        $this['events']->listen( 'bootstrapped: ' . $bootstrapper, $callback );
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped() {
        return $this->hasBeenBootstrapped;
    }

    /**
     * Set the base path for the application.
     *
     * @param  string $basePath
     * @return $this
     */
    public function setBasePath( $basePath ) {
        $this->basePath = rtrim( $basePath, '\/' );

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer() {
        $this->instance( 'path.base', $this->basePath() );
        $this->instance( 'path.config', $this->configPath() );
        $this->instance( 'path.storage', $this->storagePath() );
        $this->instance( 'path.bootstrap', $this->bootstrapPath() );
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param  string $path
     * @return string
     */
    public function path( $path = '' ) {
        $appPath = $this->appPath ?: $this->basePath . DIRECTORY_SEPARATOR . 'app';

        return $appPath . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Set the application directory.
     *
     * @param  string $path
     * @return $this
     */
    public function useAppPath( $path ) {
        $this->appPath = $path;

        $this->instance( 'path', $path );

        return $this;
    }

    /**
     * Get the base path of the Hybrid installation.
     *
     * @param  string $path
     * @return string
     */
    public function basePath( $path = '' ) {
        return $this->basePath . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param  string $path
     * @return string
     */
    public function bootstrapPath( $path = '' ) {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap' . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param  string $path
     * @return string
     */
    public function configPath( $path = '' ) {
        return ( $this->configPath ?: $this->basePath . DIRECTORY_SEPARATOR . 'config' )
            . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath() {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * Get the path to the storage directory.
     *
     * @param  string $path
     * @return string
     */
    public function storagePath( $path = '' ) {
        return ( $this->storagePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'storage' )
            . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Set the storage directory.
     *
     * @param  string $path
     * @return $this
     */
    public function useStoragePath( $path ) {
        $this->storagePath = $path;

        $this->instance( 'path.storage', $path );

        return $this;
    }

    /**
     * Set the config directory.
     *
     * @param  string $path
     * @return $this
     */
    public function useConfigPath( $path ) {
        $this->configPath = $path;

        $this->instance( 'path.config', $path );

        return $this;
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string $path
     * @return string
     */
    public function resourcePath( $path = '' ) {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Get the path to the views directory.
     *
     * This method returns the first configured path in the array of view paths.
     *
     * @param  string $path
     * @return string
     */
    public function viewPath( $path = '' ) {
        if ( ! $this['config']->has( 'view.paths' ) ) {
            return '';
        }

        $basePath = $this['config']->get( 'view.paths' )[0];

        return rtrim( $basePath, DIRECTORY_SEPARATOR ) . ( $path !== '' ? DIRECTORY_SEPARATOR . $path : '' );
    }

    /**
     * Get the path to the environment file directory.
     *
     * @return string
     */
    public function environmentPath() {
        return $this->environmentPath ?: $this->basePath;
    }

    /**
     * Set the directory for the environment file.
     *
     * @param  string $path
     * @return $this
     */
    public function useEnvironmentPath( $path ) {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param  string $file
     * @return $this
     */
    public function loadEnvironmentFrom( $file ) {
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function environmentFile() {
        return $this->environmentFile ?: '.env';
    }

    /**
     * Get the fully qualified path to the environment file.
     *
     * @return string
     */
    public function environmentFilePath() {
        return $this->environmentPath() . DIRECTORY_SEPARATOR . $this->environmentFile();
    }

    /**
     * Get or check the current application environment.
     *
     * @param  string|array $environments
     * @return string|bool
     */
    public function environment( ...$environments ) {
        if ( count( $environments ) > 0 ) {
            $patterns = is_array( $environments[0] ) ? $environments[0] : $environments;

            return Str::is( $patterns, $this['env'] );
        }

        return $this['env'];
    }

    /**
     * Determine if the application is in the local environment.
     *
     * @return bool
     */
    public function isLocal() {
        return $this['env'] === 'local';
    }

    /**
     * Determine if the application is in the production environment.
     *
     * @return bool
     */
    public function isProduction() {
        return $this['env'] === 'production';
    }

    /**
     * Detect the application's current environment.
     *
     * @return string
     */
    public function detectEnvironment( Closure $callback ) {
        $args = $_SERVER['argv'] ?? null;

        return $this['env'] = ( new EnvironmentDetector() )->detect( $callback, $args );
    }

    /**
     * Determine if the application is running in the console.
     *
     * @return bool
     */
    public function runningInConsole() {
        if ( $this->isRunningInConsole === null ) {
            $this->isRunningInConsole = Env::get( 'APP_RUNNING_IN_CONSOLE' ) ?? ( \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg' );
        }

        return $this->isRunningInConsole;
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    public function runningUnitTests() {
        return $this->bound( 'env' ) && $this['env'] === 'testing';
    }

    /**
     * Determine if the application is running with debug mode enabled.
     *
     * @return bool
     */
    public function hasDebugModeEnabled() {
        return (bool) $this['config']->get( 'app.debug' );
    }

    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders() {
        $providers = Collection::make( $this->make( 'config' )->get( 'app.providers' ) )
            ->partition( static fn( $provider ) => str_starts_with( $provider, 'Hybrid\\' ) );

        $providers->splice( 1, 0, [ $this->make( PackageManifest::class )->providers() ] );

        ( new ProviderRepository( $this, new Filesystem(), $this->getCachedServicesPath() ) )
            ->load( $providers->collapse()->toArray() );
    }

    /**
     * Adds a service provider.
     *
     * @param  string|object $provider
     * @return void
     * @deprecated Use register() instead.
     */
    public function provider( $provider ) {
        @trigger_error( __METHOD__ . '() is deprecated, use Application::register().', E_USER_DEPRECATED );

        $this->register( $provider );
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Hybrid\Core\ServiceProvider|string $provider
     * @param  bool                                $force
     * @return \Hybrid\Core\ServiceProvider
     */
    public function register( $provider, $force = false ) {
        if ( ( $registered = $this->getProvider( $provider ) ) && ! $force ) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if ( is_string( $provider ) ) {
            $provider = $this->resolveProvider( $provider );
        }

        $provider->register();

        // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the application, which
        // serves as a convenience layer while registering a lot of bindings.
        if ( property_exists( $provider, 'bindings' ) ) {
            foreach ( $provider->bindings as $key => $value ) {
                $this->bind( $key, $value );
            }
        }

        if ( property_exists( $provider, 'singletons' ) ) {
            foreach ( $provider->singletons as $key => $value ) {
                $key = is_int( $key ) ? $value : $key;

                $this->singleton( $key, $value );
            }
        }

        $this->markAsRegistered( $provider );

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ( $this->isBooted() ) {
            $this->bootProvider( $provider );
        }

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  \Hybrid\Core\ServiceProvider|string $provider
     * @return \Hybrid\Core\ServiceProvider|null
     */
    public function getProvider( $provider ) {
        return array_values( $this->getProviders( $provider ) )[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param  \Hybrid\Core\ServiceProvider|string $provider
     * @return array
     */
    public function getProviders( $provider ) {
        $name = is_string( $provider ) ? $provider : get_class( $provider );

        return Arr::where( $this->serviceProviders, static fn( $value ) => $value instanceof $name );
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string $provider
     * @return \Hybrid\Core\ServiceProvider
     */
    public function resolveProvider( $provider ) {
        return new $provider( $this );
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \Hybrid\Core\ServiceProvider $provider
     * @return void
     */
    protected function markAsRegistered( $provider ) {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[ get_class( $provider ) ] = true;
    }

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders() {
        // We will simply spin through each of the deferred providers and register each
        // one and boot them if the application has booted. This should make each of
        // the remaining services available to this application for immediate use.
        foreach ( $this->deferredServices as $service => $provider ) {
            $this->loadDeferredProvider( $service );
        }

        $this->deferredServices = [];
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param  string $service
     * @return void
     */
    public function loadDeferredProvider( $service ) {
        if ( ! $this->isDeferredService( $service ) ) {
            return;
        }

        $provider = $this->deferredServices[ $service ];

        // If the service provider has not already been loaded and registered we can
        // register it with the application and remove the service from this list
        // of deferred services, since it will already be loaded on subsequent.
        if ( ! isset( $this->loadedProviders[ $provider ] ) ) {
            $this->registerDeferredProvider( $provider, $service );
        }
    }

    /**
     * Register a deferred provider and service.
     *
     * @param  string      $provider
     * @param  string|null $service
     * @return void
     */
    public function registerDeferredProvider( $provider, $service = null ) {
        // Once the provider that provides the deferred service has been registered we
        // will remove it from our local list of the deferred services with related
        // providers so that this container does not try to resolve it out again.
        if ( $service ) {
            unset( $this->deferredServices[ $service ] );
        }

        $this->register( $instance = new $provider( $this ) );

        if ( ! $this->isBooted() ) {
            $this->booting(function () use ( $instance ) {
                $this->bootProvider( $instance );
            });
        }
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make( $abstract, array $parameters = [] ) {
        $this->loadDeferredProviderIfNeeded( $abstract = $this->getAlias( $abstract ) );

        return parent::make( $abstract, $parameters );
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string $abstract
     * @param  array  $parameters
     * @param  bool   $raiseEvents
     * @return mixed
     */
    public function resolve( $abstract, $parameters = [], $raiseEvents = true ) {
        $this->loadDeferredProviderIfNeeded( $abstract = $this->getAlias( $abstract ) );

        return parent::resolve( $abstract, $parameters, $raiseEvents );
    }

    /**
     * Load the deferred provider if the given type is a deferred service and the instance has not been loaded.
     *
     * @param  string $abstract
     * @return void
     */
    protected function loadDeferredProviderIfNeeded( $abstract ) {
        if ( $this->isDeferredService( $abstract ) && ! isset( $this->instances[ $abstract ] ) ) {
            $this->loadDeferredProvider( $abstract );
        }
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string $abstract
     * @return bool
     */
    public function bound( $abstract ) {
        return $this->isDeferredService( $abstract ) || parent::bound( $abstract );
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted() {
        return $this->booted;
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot() {

        if ( $this->isBooted() ) {
            return;
        }

        // Once the application has booted we will also fire some "booted" callbacks
        // for any listeners that need to do work after this initial booting gets
        // finished. This is useful when ordering the boot-up processes we run.
        $this->fireAppCallbacks( $this->bootingCallbacks );

        array_walk($this->serviceProviders, function ( $p ) {
            $this->bootProvider( $p );
        });

        $this->booted = true;

        if ( ! defined( 'HYBRID_BOOTED' ) ) {
            define( 'HYBRID_BOOTED', true );
        }

        $this->fireAppCallbacks( $this->bootedCallbacks );
    }

    /**
     * Boot the given service provider.
     *
     * @return void
     */
    protected function bootProvider( ServiceProvider $provider ) {

        $provider->callBootingCallbacks();

        if ( method_exists( $provider, 'boot' ) ) {
            $this->call( [ $provider, 'boot' ] );
        }

        $provider->callBootedCallbacks();
    }

    /**
     * Bootstrap the application bootstrap classes.
     *
     * @return void
     */
    public function bootstrap() {
        if ( ! $this->hasBeenBootstrapped() ) {
            $this->bootstrapWith( $this->bootstrappers() );
        }
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers() {
        return $this->bootstrappers;
    }

    /**
     * Register a new boot listener.
     *
     * @param  callable $callback
     * @return void
     */
    public function booting( $callback ) {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a new "booted" listener.
     *
     * @param  callable $callback
     * @return void
     */
    public function booted( $callback ) {
        $this->bootedCallbacks[] = $callback;

        if ( $this->isBooted() ) {
            $callback( $this );
        }
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @param  array<callable> $callbacks
     * @return void
     */
    protected function fireAppCallbacks( array &$callbacks ) {
        $index = 0;

        while ( $index < count( $callbacks ) ) {
            $callbacks[ $index ]( $this );

            ++$index;
        }
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath() {
        return $this->normalizeCachePath( 'APP_SERVICES_CACHE', 'cache/services.php' );
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath() {
        return $this->normalizeCachePath( 'APP_PACKAGES_CACHE', 'cache/packages.php' );
    }

    /**
     * Determine if the application configuration is cached.
     *
     * @return bool
     */
    public function configurationIsCached() {
        return is_file( $this->getCachedConfigPath() );
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath() {
        return $this->normalizeCachePath( 'APP_CONFIG_CACHE', 'cache/config.php' );
    }

    /**
     * Determine if the application events are cached.
     *
     * @return bool
     */
    public function eventsAreCached() {
        return $this['files']->exists( $this->getCachedEventsPath() );
    }

    /**
     * Get the path to the events cache file.
     *
     * @return string
     */
    public function getCachedEventsPath() {
        return $this->normalizeCachePath( 'APP_EVENTS_CACHE', 'cache/events.php' );
    }

    /**
     * Normalize a relative or absolute path to a cache file.
     *
     * @param  string $key
     * @param  string $default
     * @return string
     */
    protected function normalizeCachePath( $key, $default ) {
        if ( is_null( $env = Env::get( $key ) ) ) {
            return $this->bootstrapPath( $default );
        }

        return Str::startsWith( $env, $this->absoluteCachePathPrefixes )
            ? $env
            : $this->basePath( $env );
    }

    /**
     * Add new prefix to list of absolute path prefixes.
     *
     * @param  string $prefix
     * @return $this
     */
    public function addAbsoluteCachePathPrefix( $prefix ) {
        $this->absoluteCachePathPrefixes[] = $prefix;

        return $this;
    }

    /**
     * Get the service providers that have been loaded.
     *
     * @return array
     */
    public function getLoadedProviders() {
        return $this->loadedProviders;
    }

    /**
     * Determine if the given service provider is loaded.
     *
     * @return bool
     */
    public function providerIsLoaded( string $provider ) {
        return isset( $this->loadedProviders[ $provider ] );
    }

    /**
     * Get the application's deferred services.
     *
     * @return array
     */
    public function getDeferredServices() {
        return $this->deferredServices;
    }

    /**
     * Set the application's deferred services.
     *
     * @param  array $services
     * @return void
     */
    public function setDeferredServices( array $services ) {
        $this->deferredServices = $services;
    }

    /**
     * Add an array of services to the application's deferred services.
     *
     * @param  array $services
     * @return void
     */
    public function addDeferredServices( array $services ) {
        $this->deferredServices = array_merge( $this->deferredServices, $services );
    }

    /**
     * Determine if the given service is a deferred service.
     *
     * @param  string $service
     * @return bool
     */
    public function isDeferredService( $service ) {
        return isset( $this->deferredServices[ $service ] );
    }

    /**
     * Configure the real-time facade namespace.
     *
     * @param  string $namespace
     * @return void
     */
    public function provideFacades( $namespace ) {
        AliasLoader::setFacadeNamespace( $namespace );
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases() {
        // Register the core class aliases in the container.
        foreach ( [
            'app'        => [ self::class, \Hybrid\Contracts\Container\Container::class, \Hybrid\Contracts\Core\Application::class, ContainerInterface::class ],
            'config'     => [ Repository::class, \Hybrid\Contracts\Config\Repository::class ],
            'events'     => [ Dispatcher::class, \Hybrid\Contracts\Events\Dispatcher::class ],
            'files'      => [ Filesystem::class ],
            'filesystem' => [ FilesystemManager::class, Factory::class ],
        ] as $key => $aliases ) {
            foreach ( $aliases as $alias ) {
                $this->alias( $key, $alias );
            }
        }
    }

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush() {
        parent::flush();

        $this->buildStack                     = [];
        $this->loadedProviders                = [];
        $this->bootedCallbacks                = [];
        $this->bootingCallbacks               = [];
        $this->deferredServices               = [];
        $this->reboundCallbacks               = [];
        $this->serviceProviders               = [];
        $this->resolvingCallbacks             = [];
        $this->terminatingCallbacks           = [];
        $this->beforeResolvingCallbacks       = [];
        $this->afterResolvingCallbacks        = [];
        $this->globalBeforeResolvingCallbacks = [];
        $this->globalResolvingCallbacks       = [];
        $this->globalAfterResolvingCallbacks  = [];
    }

    /**
     * Get the application namespace.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getNamespace() {
        if ( ! is_null( $this->namespace ) ) {
            return $this->namespace;
        }

        $composer = json_decode( file_get_contents( $this->basePath( 'composer.json' ) ), true );

        foreach ( (array) data_get( $composer, 'autoload.psr-4' ) as $namespace => $path ) {
            foreach ( (array) $path as $pathChoice ) {
                if ( realpath( $this->path() ) === realpath( $this->basePath( $pathChoice ) ) ) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new \RuntimeException( 'Unable to detect application namespace.' );
    }

}
