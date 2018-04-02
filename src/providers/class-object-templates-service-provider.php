<?php

namespace Hybrid\Providers;

use Hybrid\Common\Collection;

class ObjectTemplatesServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'object_templates', function( $container ) {

                        return new Collection();
                } );
        }
}
