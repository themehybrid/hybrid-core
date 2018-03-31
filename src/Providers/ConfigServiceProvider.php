<?php

namespace Hybrid\Providers;

use Hybrid\Collection;
use function Hybrid\locate_file_path;

class ConfigServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'config', function( $container ) {

                        $view_config = locate_file_path( 'config/view.php' );

                        $view = wp_parse_args( $view_config ?: [], [
                                'path'    => 'resources/views',
                                'name'    => 'data',
                                'extract' => true
                        ] );

                	return new Collection( [
                		'view' => new Collection( apply_filters( 'hybrid/config/view', $view ) )
                	] );
                } );
        }
}
