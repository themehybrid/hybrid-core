<?php

namespace Hybrid\View;

use Hybrid\Core\Collection;
use Hybrid\Core\ServiceProvider;

class ViewServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->add( 'view', function( $container, $params ) {

                        return new View(
                                $params['name'],
                                $params['slugs'],
                                $params['data'] instanceof Collection ? $params['data'] : new Collection( $params['data'] )
                        );
                } );
        }
}
