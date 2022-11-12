<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Core\Application;
use Hybrid\Filesystem\Filesystem;

class GenerateStorageStructures {

    /**
     * Bootstrap the given application.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function bootstrap( Application $app ) {

        $directories = [
            'bootstrap/cache',
            'storage/app/public',
            'storage/framework/cache',
            'storage/framework/views',
            'storage/logs',
        ];

        $rootDirectory = $app->resolve( 'path.base' );

        foreach ( $directories as $directory ) {

            $directory = $rootDirectory . '/' . $directory;

            if ( ( new Filesystem() )->exists( $directory ) ) {
                continue;
            }

            ( new Filesystem() )->ensureDirectoryExists( $directory );
        }
    }

}
