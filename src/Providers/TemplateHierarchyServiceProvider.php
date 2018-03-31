<?php

namespace Hybrid\Providers;

use Hybrid\TemplateHierarchy;

class TemplateHierarchyServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'template_hierarchy', function( $container ) {

                        return new TemplateHierarchy();
                } );
        }

        public function boot() {

                $this->app->get( 'template_hierarchy' );
        }
}
