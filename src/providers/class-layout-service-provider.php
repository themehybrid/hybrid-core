<?php

namespace Hybrid\Providers;

use Hybrid\Common\Collection;

class LayoutServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'layouts', function() {

                        return new Collection();
                } );
        }
}
