<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Config\Repository as RepositoryContract;
use Hybrid\Contracts\Core\Application;
use Hybrid\Tools\Config\Repository;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class LoadConfiguration {

    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function bootstrap( Application $app ) {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if ( file_exists( $cached = $app->getCachedConfigPath() ) ) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance( 'config', $config = new Repository( $items ) );

        if ( ! isset( $loadedFromCache ) ) {
            $this->loadConfigurationFiles( $app, $config );
        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment( static fn() => $config->get( 'app.env', 'production' ) );
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @return void
     * @throws \Exception
     */
    protected function loadConfigurationFiles( Application $app, RepositoryContract $repository ) {
        $files = $this->getConfigurationFiles( $app );

        /*
        if ( ! isset( $files['app'] ) ) {
            throw new \Exception( 'Unable to load the "app" configuration file.' );
        }
        */

        foreach ( $files as $key => $path ) {
            $repository->set( $key, require $path );
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles( Application $app ) {
        $files = [];

        $configPath = realpath( $app->configPath() );

        if ( ! $configPath ) {
            return $files;
        }

        foreach ( Finder::create()->files()->name( '*.php' )->in( $configPath ) as $file ) {
            $directory = $this->getNestedDirectory( $file, $configPath );

            $files[ $directory . basename( $file->getRealPath(), '.php' ) ] = $file->getRealPath();
        }

        ksort( $files, SORT_NATURAL );

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  string $configPath
     * @return string
     */
    protected function getNestedDirectory( SplFileInfo $file, $configPath ) {
        $directory = $file->getPath();

        if ( $nested = trim( str_replace( $configPath, '', $directory ), DIRECTORY_SEPARATOR ) ) {
            $nested = str_replace( DIRECTORY_SEPARATOR, '.', $nested ) . '.';
        }

        return $nested;
    }

}
