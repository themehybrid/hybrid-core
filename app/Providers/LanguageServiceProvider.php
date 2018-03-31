<?php

namespace Hybrid\Providers;

use Hybrid\Language;

class LanguageServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'language', function( $container ) {

                        return new Language();
                } );
        }

        public function boot() {

                $this->app->get( 'language' );
        }
}
