<?php

namespace Hybrid\Providers;

use Hybrid\Common\Collection;

class TemplateServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'templates', function( $container ) {

                        return new Collection();
                } );
        }
}
