<?php
/**
 * Application class.
 *
 * This class is essentially a wrapper around the `Container` class that's
 * specific to the framework. This class is meant to be used as the single,
 * one-true instance of the framework. It's used to load up service providers
 * that interact with the container.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use Hybrid\Providers\ConfigServiceProvider;
use Hybrid\Providers\CustomizeServiceProvider;
use Hybrid\Providers\LanguageServiceProvider;
use Hybrid\Providers\MediaMetaServiceProvider;
use Hybrid\Providers\ObjectTemplatesServiceProvider;
use Hybrid\Providers\TemplateHierarchyServiceProvider;

/**
 * Application class.
 *
 * @since  5.0.0
 * @access public
 */
class Application extends Container {

        /**
         * The current version of the framework.
         *
         * @since  5.0.0
         * @access public
         * @var    string
         */
        const VERSION = '5.0.0';

        /**
         * Array of service provider objects.
         *
         * @since  5.0.0
         * @access protected
         * @var    array
         */
        protected $providers = [];

        /**
         * Calls the functions to register the framework directory paths,
         * register service providers, and boot the service providers.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function __construct() {

                $this->registerPaths();
                $this->registerProviders();
                $this->bootProviders();
        }

        /**
         * Adds the directory path and URI to the framework. These should
         * initially be defined via the `HYBRID_DIR` and `HYBRID_URI` constants
         * to get the correct results.
         *
         * @since  5.0.0
         * @access protected
         * @return void
         */
        protected function registerPaths() {

                $this->add( 'path', untrailingslashit( HYBRID_DIR ) );
                $this->add( 'uri',  untrailingslashit( HYBRID_URI ) );
        }

        /**
         * Registers the framework's default service providers. At the moment,
         * we're not providing a method to register third-party service providers
         * until things are bit more fleshed out and stable.
         *
         * @since  5.0.0
         * @access protected
         * @return void
         */
        protected function registerProviders() {

                $providers = [
                        ConfigServiceProvider::class,
                        CustomizeServiceProvider::class,
                        LanguageServiceProvider::class,
                        MediaMetaServiceProvider::class,
                        ObjectTemplatesServiceProvider::class,
                        TemplateHierarchyServiceProvider::class
                ];

                foreach ( $providers as $provider ) {

                        $this->providers[ $provider ] = new $provider( $this );

                        $this->providers[ $provider ]->register();
                }
        }

        /**
         * Calls the `boot()` method of all the registered service providers.
         *
         * @since  5.0.0
         * @access protected
         * @return void
         */
        protected function bootProviders() {

                foreach ( $this->providers as $provider ) {
                        $provider->boot();
                }
        }

        /**
         * Returns the framework version number.
         *
         * @since  5.0.0
         * @access public
         * @return string
         */
        public function version() {

                return static::VERSION;
        }
}
