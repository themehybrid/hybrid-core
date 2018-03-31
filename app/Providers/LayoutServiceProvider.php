<?php

namespace Hybrid\Providers;

use Hybrid\Collection;
use Hybrid\Admin\PostLayout;
use Hybrid\Admin\TermLayout;

class LayoutServiceProvider extends ServiceProvider {

        public function register() {

                $this->app->singleton( 'layouts', function() {

                        return new Collection();
                } );

                $this->app->singleton( 'admin/post_layout', function() {

        		return new PostLayout();
        	} );

        	$this->app->singleton( 'admin/term_layout', function() {

        		return new TermLayout();
        	} );
        }

        public function boot() {

                if ( is_admin() ) {
                        $this->app->get( 'admin/post_layout' );
                	$this->app->get( 'admin/term_layout' );
                }
        }
}
