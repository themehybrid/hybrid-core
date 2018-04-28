<?php

namespace Hybrid\Attributes;

use Hybrid\Core\ServiceProvider;

class AttributesServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->add( 'attr', function( $container, $params ) {

                        return new Attributes( $params['name'], $params['context'], $params['attr'] );
                } );
        }
}
