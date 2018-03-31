<?php

namespace Hybrid\Providers;

use Hybrid\Customize\Customize;

class CustomizeServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'customize', function( $container ) {

                        return new Customize();
                } );
        }

        public function boot() {

                $this->app->get( 'customize' );
        }
}
