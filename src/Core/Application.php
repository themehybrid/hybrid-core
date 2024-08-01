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
 * @copyright Copyright (c) 2008 - 2024, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use Closure;
use Composer\Autoload\ClassLoader;
use Hybrid\Container\Container;
use Hybrid\Contracts\Bootable;
use Hybrid\Contracts\Core\Application as ApplicationContract;
use Hybrid\Contracts\Core\CachesConfiguration;
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
use Hybrid\Tools\Traits\Macroable;
use Psr\Container\ContainerInterface;
use function Hybrid\Filesystem\join_paths;
use function Hybrid\Tools\value;

/**
 * Application class.
 */
class Application extends Container implements ApplicationContract, Bootable, CachesConfiguration {

    use Macroable;

    /**
     * The Hybrid Core framework version.
     */
    const VERSION = '7.0.0-alpha.3';

    /**
     * The base path for the Hybrid Core installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The array of registered callbacks.
     *
     * @var array<callable>
     */
    protected $registeredCallbacks = [];

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
     * The array of terminating callbacks.
     *
     * @var array<callable>
     */
    protected $terminatingCallbacks = [];

    /**
     * All of the registered service providers.
     *
     * @var array<string, \Hybrid\Core\ServiceProvider>
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
     * The custom bootstrap path defined by the developer.
     *
     * @var string
     */
    protected $bootstrapPath;

    /**
     * The custom application path defined by the developer.
     *
     * @var string
     */
    protected $appPath;

    /**
     * The custom configuration path defined by the developer.
     *
     * @var string
     */
    protected $configPath;

    /**
     * The custom public / web path defined by the developer.
     *
     * @var string
     */
    protected $publicPath;

    /**
     * The custom storage path defined by the developer.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The custom resources path defined by the developer.
     *
     * @var string
     */
    protected $resourcesPath;

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
     * Indicates if the framework's base configuration should be merged.
     *
     * @var bool
     */
    protected $mergeFrameworkConfiguration = true;

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
     * @param string|null $basePath
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
     * Begin configuring a new Hybrid Core application instance.
     *
     * @param string|null $basePath
     * @return \Hybrid\Core\Configuration\ApplicationBuilder
     */
    public static function configure( ?string $basePath = null ) {
        $basePath = match ( true ) {
            is_string( $basePath ) => $basePath,
            default => static::inferBasePath(),
        };

        return ( new Configuration\ApplicationBuilder( new static( $basePath ) ) )
            ->withEvents()
            ->withProviders();
    }

    /**
     * Infer the application's base directory from the environment.
     *
     * @return string
     */
    public static function inferBasePath() {
        return match ( true ) {
            isset( $_ENV['HYBRID_CORE_BASE_PATH'] ) => $_ENV['HYBRID_CORE_BASE_PATH'],
            default => dirname( array_keys( ClassLoader::getRegisteredLoaders() )[0] ),
        };
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

        $this->singleton( PackageManifest::class, fn() => new PackageManifest(
            new Filesystem(), $this->basePath(), $this->getCachedPackagesPath()
        ) );
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
        // $this->register( new ContextServiceProvider( $this ) );
    }

    /**
     * Run the given array of bootstrap classes.
     *
     * @param array<string> $bootstrappers
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
     * @param \Closure $callback
     * @return void
     */
    public function afterLoadingEnvironment( Closure $callback ) {
        $this->afterBootstrapping( LoadEnvironmentVariables::class, $callback );
    }

    /**
     * Register a callback to run before a bootstrapper.
     *
     * @param string   $bootstrapper
     * @param \Closure $callback
     * @return void
     */
    public function beforeBootstrapping( $bootstrapper, Closure $callback ) {
        $this['events']->listen( 'bootstrapping: ' . $bootstrapper, $callback );
    }

    /**
     * Register a callback to run after a bootstrapper.
     *
     * @param string   $bootstrapper
     * @param \Closure $callback
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
     * @param string $basePath
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
        $this->instance( 'path', $this->path() );
        $this->instance( 'path.base', $this->basePath() );
        $this->instance( 'path.config', $this->configPath() );
        $this->instance( 'path.public', $this->publicPath() );
        $this->instance( 'path.resources', $this->resourcePath() );
        $this->instance( 'path.storage', $this->storagePath() );

        $this->useBootstrapPath( value( fn() => is_dir( $directory = $this->basePath( '.hybrid-core' ) )
                ? $directory
                : $this->basePath( 'bootstrap' ) ) );
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param string $path
     * @return string
     */
    public function path( $path = '' ) {
        return $this->joinPaths( $this->appPath ?: $this->basePath( 'app' ), $path );
    }

    /**
     * Set the application directory.
     *
     * @param string $path
     * @return $this
     */
    public function useAppPath( $path ) {
        $this->appPath = $path;

        $this->instance( 'path', $path );

        return $this;
    }

    /**
     * Get the base path of the Hybrid Core installation.
     *
     * @param string $path
     * @return string
     */
    public function basePath( $path = '' ) {
        return $this->joinPaths( $this->basePath, $path );
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param string $path
     * @return string
     */
    public function bootstrapPath( $path = '' ) {
        return $this->joinPaths( $this->bootstrapPath, $path );
    }

    /**
     * Get the path to the service provider list in the bootstrap directory.
     *
     * @return string
     */
    public function getBootstrapProvidersPath() {
        return $this->bootstrapPath( 'providers.php' );
    }

    /**
     * Set the bootstrap file directory.
     *
     * @param string $path
     * @return $this
     */
    public function useBootstrapPath( $path ) {
        $this->bootstrapPath = $path;

        $this->instance( 'path.bootstrap', $path );

        return $this;
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param string $path
     * @return string
     */
    public function configPath( $path = '' ) {
        return $this->joinPaths( $this->configPath ?: $this->basePath( 'config' ), $path );
    }

    /**
     * Set the configuration directory.
     *
     * @param string $path
     * @return $this
     */
    public function useConfigPath( $path ) {
        $this->configPath = $path;

        $this->instance( 'path.config', $path );

        return $this;
    }

    /**
     * Get the path to the public / web directory.
     *
     * @param string $path
     * @return string
     */
    public function publicPath( $path = '' ) {
        return $this->joinPaths( $this->publicPath ?: $this->basePath( 'public' ), $path );
    }

    /**
     * Set the public / web directory.
     *
     * @param string $path
     * @return $this
     */
    public function usePublicPath( $path ) {
        $this->publicPath = $path;

        $this->instance( 'path.public', $path );

        return $this;
    }

    /**
     * Get the path to the storage directory.
     *
     * @param string $path
     * @return string
     */
    public function storagePath( $path = '' ) {
        if ( isset( $_ENV['HYBRID_CORE_STORAGE_PATH'] ) ) {
            return $this->joinPaths( $this->storagePath ?: $_ENV['HYBRID_CORE_STORAGE_PATH'], $path );
        }

        if ( isset( $_SERVER['HYBRID_CORE_STORAGE_PATH'] ) ) {
            return $this->joinPaths( $this->storagePath ?: $_SERVER['HYBRID_CORE_STORAGE_PATH'], $path );
        }

        return $this->joinPaths( $this->storagePath ?: $this->basePath( 'storage' ), $path );
    }

    /**
     * Set the storage directory.
     *
     * @param string $path
     * @return $this
     */
    public function useStoragePath( $path ) {
        $this->storagePath = $path;

        $this->instance( 'path.storage', $path );

        return $this;
    }

    /**
     * Get the path to the resources directory.
     *
     * @param string $path
     * @return string
     */
    public function resourcePath( $path = '' ) {
        return $this->joinPaths( $this->resourcesPath ?: $this->basePath( 'resources' ), $path );
    }

    /**
     * Set the resources directory.
     *
     * @param string $path
     * @return $this
     */
    public function useResourcePath( $path ) {
        $this->resourcesPath = $path;

        $this->instance( 'path.resources', $path );

        return $this;
    }

    /**
     * Get the path to the views directory.
     *
     * This method returns the first configured path in the array of view paths.
     *
     * @param string $path
     * @return string
     */
    public function viewPath( $path = '' ) {
        if ( ! $this['config']->has( 'view.paths' ) ) {
            return '';
        }

        $viewPath = rtrim( $this['config']->get( 'view.paths' )[0], DIRECTORY_SEPARATOR );

        return $this->joinPaths( $viewPath, $path );
    }

    /**
     * Join the given paths together.
     *
     * @param string $basePath
     * @param string $path
     * @return string
     */
    public function joinPaths( $basePath, $path = '' ) {
        return join_paths( $basePath, $path );
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
     * @param string $path
     * @return $this
     */
    public function useEnvironmentPath( $path ) {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param string $file
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
     * @param string|array ...$environments
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
        return 'local' === $this['env'];
    }

    /**
     * Determine if the application is in the production environment.
     *
     * @return bool
     */
    public function isProduction() {
        return 'production' === $this['env'];
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
        if ( null === $this->isRunningInConsole ) {
            $this->isRunningInConsole = Env::get( 'HYBRID_CORE_RUNNING_IN_CONSOLE' ) ?? ( \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg' );
        }

        return $this->isRunningInConsole;
    }

    /**
     * Determine if the application is running any of the given console commands.
     *
     * @param string|array ...$commands
     * @return bool
     */
    public function runningConsoleCommand( ...$commands ) {
        if ( ! $this->runningInConsole() ) {
            return false;
        }

        return in_array(
            $_SERVER['argv'][1] ?? null,
            is_array( $commands[0] ) ? $commands[0] : $commands
        );
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    public function runningUnitTests() {
        return $this->bound( 'env' ) && 'testing' === $this['env'];
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
     * Register a new registered listener.
     *
     * @param callable $callback
     * @return void
     */
    public function registered( $callback ) {
        $this->registeredCallbacks[] = $callback;
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

        $this->fireAppCallbacks( $this->registeredCallbacks );
    }

    /**
     * Adds a service provider.
     *
     * @param string|object $provider
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
     * @param \Hybrid\Core\ServiceProvider|string $provider
     * @param bool                                $force
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
     * @param \Hybrid\Core\ServiceProvider|string $provider
     * @return \Hybrid\Core\ServiceProvider|null
     */
    public function getProvider( $provider ) {
        $name = is_string( $provider ) ? $provider : get_class( $provider );

        return $this->serviceProviders[ $name ] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param \Hybrid\Core\ServiceProvider|string $provider
     * @return array
     */
    public function getProviders( $provider ) {
        $name = is_string( $provider ) ? $provider : get_class( $provider );

        return Arr::where( $this->serviceProviders, static fn( $value ) => $value instanceof $name );
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $provider
     * @return \Hybrid\Core\ServiceProvider
     */
    public function resolveProvider( $provider ) {
        return new $provider( $this );
    }

    /**
     * Mark the given provider as registered.
     *
     * @param \Hybrid\Core\ServiceProvider $provider
     * @return void
     */
    protected function markAsRegistered( $provider ) {
        $class = get_class( $provider );

        $this->serviceProviders[ $class ] = $provider;

        $this->loadedProviders[ $class ] = true;
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
     * @param string $service
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
     * @param string      $provider
     * @param string|null $service
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
            $this->booting( function () use ( $instance ) {
                $this->bootProvider( $instance );
            } );
        }
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @param array  $parameters
     * @return mixed
     * @throws \Hybrid\Contracts\Container\BindingResolutionException
     */
    public function make( $abstract, array $parameters = [] ) {
        $this->loadDeferredProviderIfNeeded( $abstract = $this->getAlias( $abstract ) );

        return parent::make( $abstract, $parameters );
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @param array  $parameters
     * @param bool   $raiseEvents
     * @return mixed
     * @throws \Hybrid\Contracts\Container\BindingResolutionException
     * @throws \Hybrid\Contracts\Container\CircularDependencyException
     */
    public function resolve( $abstract, $parameters = [], $raiseEvents = true ) {
        $this->loadDeferredProviderIfNeeded( $abstract = $this->getAlias( $abstract ) );

        return parent::resolve( $abstract, $parameters, $raiseEvents );
    }

    /**
     * Load the deferred provider if the given type is a deferred service and the instance has not been loaded.
     *
     * @param string $abstract
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
     * @param string $abstract
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

        array_walk( $this->serviceProviders, function ( $p ) {
            $this->bootProvider( $p );
        } );

        $this->booted = true;

        if ( ! defined( 'HYBRID_CORE_BOOTED' ) ) {
            define( 'HYBRID_CORE_BOOTED', true );
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
     * @param callable $callback
     * @return void
     */
    public function booting( $callback ) {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a new "booted" listener.
     *
     * @param callable $callback
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
     * @param array<callable> $callbacks
     * @return void
     */
    protected function fireAppCallbacks( array &$callbacks ) {
        $index = 0;

        while ( count( $callbacks ) > $index ) {
            $callbacks[ $index ]( $this );

            ++$index;
        }
    }

    /**
     * Determine if the framework's base configuration should be merged.
     *
     * @return bool
     */
    public function shouldMergeFrameworkConfiguration() {
        return $this->mergeFrameworkConfiguration;
    }

    /**
     * Indicate that the framework's base configuration should not be merged.
     *
     * @return $this
     */
    public function dontMergeFrameworkConfiguration() {
        $this->mergeFrameworkConfiguration = false;

        return $this;
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath() {
        return $this->normalizeCachePath( 'HYBRID_CORE_SERVICES_CACHE', 'cache/services.php' );
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath() {
        return $this->normalizeCachePath( 'HYBRID_CORE_PACKAGES_CACHE', 'cache/packages.php' );
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
        return $this->normalizeCachePath( 'HYBRID_CORE_CONFIG_CACHE', 'cache/config.php' );
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
        return $this->normalizeCachePath( 'HYBRID_CORE_EVENTS_CACHE', 'cache/events.php' );
    }

    /**
     * Normalize a relative or absolute path to a cache file.
     *
     * @param string $key
     * @param string $default
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
     * @param string $prefix
     * @return $this
     */
    public function addAbsoluteCachePathPrefix( $prefix ) {
        $this->absoluteCachePathPrefixes[] = $prefix;

        return $this;
    }

    /**
     * Register a terminating callback with the application.
     *
     * @param callable|string $callback
     * @return $this
     */
    public function terminating( $callback ) {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Terminate the application.
     *
     * @return void
     */
    public function terminate() {
        $index = 0;

        while ( count( $this->terminatingCallbacks ) > $index ) {
            $this->call( $this->terminatingCallbacks[ $index ] );

            ++$index;
        }
    }

    /**
     * Get the service providers that have been loaded.
     *
     * @return array<string, bool>
     */
    public function getLoadedProviders() {
        return $this->loadedProviders;
    }

    /**
     * Determine if the given service provider is loaded.
     *
     * @param string $provider
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
     * @param array $services
     * @return void
     */
    public function setDeferredServices( array $services ) {
        $this->deferredServices = $services;
    }

    /**
     * Add an array of services to the application's deferred services.
     *
     * @param array $services
     * @return void
     */
    public function addDeferredServices( array $services ) {
        $this->deferredServices = array_merge( $this->deferredServices, $services );
    }

    /**
     * Determine if the given service is a deferred service.
     *
     * @param string $service
     * @return bool
     */
    public function isDeferredService( $service ) {
        return isset( $this->deferredServices[ $service ] );
    }

    /**
     * Configure the real-time facade namespace.
     *
     * @param string $namespace
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
