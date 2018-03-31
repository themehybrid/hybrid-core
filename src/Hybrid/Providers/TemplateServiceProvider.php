<?php

namespace Hybrid\Providers;

use Hybrid\Core\Collection;

class TemplateServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'templates', function( $container ) {

                        return new Collection();
                } );
        }
}
