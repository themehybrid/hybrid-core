<?php

namespace Hybrid\Providers;

use Hybrid\Common\Collection;

class MediaMetaServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'media_meta', function( $container ) {

                        return new Collection();
                } );
        }

        public function boot() {
        }
}
