<?php

namespace Hybrid;

use Hybrid\Providers\ConfigServiceProvider;
use Hybrid\Providers\CustomizeServiceProvider;
use Hybrid\Providers\LanguageServiceProvider;
use Hybrid\Providers\LayoutServiceProvider;
use Hybrid\Providers\MediaMetaServiceProvider;
use Hybrid\Providers\TemplateHierarchyServiceProvider;
use Hybrid\Providers\TemplateServiceProvider;

class Application extends Container {

        const VERSION = '5.0.0';

        protected $providers = [];

        public function __construct() {

                $this->registerPaths();
                $this->registerProviders();
                $this->bootProviders();
        }

        protected function registerPaths() {

                $this->add( 'path', untrailingslashit( HYBRID_DIR ) );
                $this->add( 'uri',  untrailingslashit( HYBRID_URI ) );
        }

        protected function registerProviders() {

                $providers = [
                        ConfigServiceProvider::class,
                        CustomizeServiceProvider::class,
                        LanguageServiceProvider::class,
                        LayoutServiceProvider::class,
                        MediaMetaServiceProvider::class,
                        TemplateHierarchyServiceProvider::class,
                        TemplateServiceProvider::class
                ];

                foreach ( $providers as $provider ) {

                        $this->providers[ $provider ] = new $provider( $this );

                        $this->providers[ $provider ]->register();
                }
        }

        protected function bootProviders() {

                foreach ( $this->providers as $provider ) {
                        $provider->boot();
                }
        }

        public function version() {

                return static::VERSION;
        }
}
