<?php

namespace Hybrid\Core\Bootstrap;

use Closure;
use Hybrid\Contracts\Config\Repository as RepositoryContract;
use Hybrid\Contracts\Core\Application;
use Hybrid\Tools\Collection;
use Hybrid\Tools\Config\Repository;
use Symfony\Component\Finder\Finder;

class LoadConfiguration {
    /**
     * The closure that resolves the permanent, static configuration if applicable.
     *
     * @var (Closure(Application): array<array-key, mixed>)|null
     */
    protected static ?Closure $alwaysUseConfig = null;

    /**
     * Bootstrap the given application.
     *
     * @param \Hybrid\Contracts\Core\Application $app
     *
     * @return void
     */
    public function bootstrap( Application $app ) {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        $loadedFromCache = false;

        if ( null !== self::$alwaysUseConfig ) {
            $items = $app->call( self::$alwaysUseConfig );

            $loadedFromCache = true;
        } elseif ( file_exists( $cached = $app->getCachedConfigPath() ) ) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        $app->instance( 'config_loaded_from_cache', $loadedFromCache );

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance( 'config', $config = new Repository( $items ) );

        if ( ! $loadedFromCache ) {
            $this->loadConfigurationFiles( $app, $config );
        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment( fn() => $config->get( 'app.env', 'production' ) );

        $app->resolveEnvironmentUsing( $app->environment( ...) );

        // date_default_timezone_set( $config->get( 'app.timezone', 'UTC' ) );

        // mb_internal_encoding( 'UTF-8' );
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param \Hybrid\Contracts\Core\Application  $app
     * @param \Hybrid\Contracts\Config\Repository $repository
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function loadConfigurationFiles( Application $app, RepositoryContract $repository ) {
        $files = $this->getConfigurationFiles( $app );

        $shouldMerge = method_exists( $app, 'shouldMergeFrameworkConfiguration' )
            ? $app->shouldMergeFrameworkConfiguration()
            : true;

        $base = $shouldMerge
            ? $this->getBaseConfiguration()
            : [];

        foreach ( ( new Collection( $base ) )->diffKeys( $files ) as $name => $config ) {
            $repository->set( $name, $config );
        }

        foreach ( $files as $name => $path ) {
            $base = $this->loadConfigurationFile( $repository, $name, $path, $base );
        }

        foreach ( $base as $name => $config ) {
            $repository->set( $name, $config );
        }
    }

    /**
     * Load the given configuration file.
     *
     * @param \Hybrid\Contracts\Config\Repository $repository
     * @param string                              $name
     * @param string                              $path
     * @param array                               $base
     *
     * @return array
     */
    protected function loadConfigurationFile( RepositoryContract $repository, $name, $path, array $base ) {
        $config = ( fn() => require $path )();

        if ( isset( $base[ $name ] ) ) {
            $config = array_merge( $base[ $name ], $config );

            foreach ( $this->mergeableOptions( $name ) as $option ) {
                if ( isset( $config[ $option ] ) ) {
                    $config[ $option ] = array_merge( $base[ $name ][ $option ], $config[ $option ] );
                }
            }

            unset( $base[ $name ] );
        }

        $repository->set( $name, $config );

        return $base;
    }

    /**
     * Get the options within the configuration file that should be merged again.
     *
     * @param string $name
     *
     * @return array
     */
    protected function mergeableOptions( $name ) {
        return [
            'logging' => [ 'channels' ],
        ][ $name ] ?? [];
    }

    /**
     * Get all of the configuration files for the application.
     *
     * ROOT ONLY — this deliberately does not recurse.
     *
     * Root `config/*.php` files (app.php, logging.php, ...) configure the
     * framework. Every package ships them and the last one loaded wins; that is
     * intended. Anything NESTED belongs to the package that shipped it and must
     * be loaded separately under a namespace, or two packages that both have
     * `config/site/some.php` silently overwrite each other. See Config\Loader.
     *
     * @param \Hybrid\Contracts\Core\Application $app
     *
     * @return array
     */
    protected function getConfigurationFiles( Application $app ) {
        $files = [];

        $configPath = realpath( $app->configPath() );

        if ( ! $configPath ) {
            return [];
        }

        foreach ( glob( $configPath . '/*.php' ) as $file ) {
            $files[ basename( $file, '.php' ) ] = $file;
        }

        ksort( $files, SORT_NATURAL );

        return $files;
    }

    /**
     * Get the base configuration files.
     *
     * @return array
     */
    protected function getBaseConfiguration() {
        $config = [];

        $configPath = __DIR__ . '/../../../../config';

        // Check if the configuration directory exists.
        // In Hybrid Core, the base configuration is optional and depends on the application's setup.
        // Return an empty configuration array if the directory does not exist.
        if ( ! is_dir( $configPath ) ) {
            return $config;
        }

        foreach ( Finder::create()->files()->name( '*.php' )->in( $configPath ) as $file ) {
            $config[ basename( $file->getRealPath(), '.php' ) ] = require $file->getRealPath();
        }

        return $config;
    }

    /**
     * Set a callback to return the permanent, static configuration values.
     *
     * @param (Closure(Application): array<array-key, mixed>)|null $alwaysUseConfig
     */
    public static function alwaysUse( ?Closure $alwaysUseConfig ): void {
        static::$alwaysUseConfig = $alwaysUseConfig;
    }
}
