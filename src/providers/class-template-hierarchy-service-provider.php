<?php

namespace Hybrid\Providers;

use Hybrid\Template\Hierarchy;

class TemplateHierarchyServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'template_hierarchy', function( $container ) {

                        return new Hierarchy();
                } );
        }

        public function boot() {

                $this->app->get( 'template_hierarchy' );
        }
}
