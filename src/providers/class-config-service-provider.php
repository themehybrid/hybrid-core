<?php

namespace Hybrid\Providers;

use Hybrid\Common\Collection;
use function Hybrid\locate_file_path;

class ConfigServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'config', function( $container ) {

                        // Create a new collection to house the view config.
                        $view = new Collection(
                                apply_filters( 'hybrid/config/view', [
                                        'path'    => 'resources/views',
                                        'name'    => 'data',
                                        'extract' => true
                                ] )
                        );

                        // Create and return a new collection of config objects.
                	return new Collection( [ 'view' => $view ] );
                } );
        }
}
