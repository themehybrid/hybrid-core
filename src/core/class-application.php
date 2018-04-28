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

use Hybrid\Attributes\AttributesServiceProvider;
use Hybrid\Config\ConfigServiceProvider;
use Hybrid\Customize\CustomizeServiceProvider;
use Hybrid\Language\LanguageServiceProvider;
use Hybrid\Media\MediaMetaServiceProvider;
use Hybrid\Template\ObjectTemplatesServiceProvider;
use Hybrid\Template\TemplateHierarchyServiceProvider;
use Hybrid\View\ViewServiceProvider;

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

                $this->registerDefaultBindings();

                // Register and providers at the earliest hook available to
                // themes. This is so that themes can register service providers
                // if they choose to do so.
                add_action( 'after_setup_theme', [ $this, 'registerProviders' ], ~PHP_INT_MAX );
                add_action( 'after_setup_theme', [ $this, 'bootProviders'     ], ~PHP_INT_MAX );
        }

        /**
         * Registers the default bindings we need to run the framework.
         *
         * @since  5.0.0
         * @access protected
         * @return void
         */
        protected function registerDefaultBindings() {

                // Adds the directory path and URI for the framework. These
                // should initially be defined via the `HYBRID_DIR` and
                // `HYBRID_URI` constants to get the correct results.
                $this->add( 'path', untrailingslashit( HYBRID_DIR ) );
                $this->add( 'uri',  untrailingslashit( HYBRID_URI ) );

                // Add the version for the framework.
                $this->add( 'version', static::VERSION );
        }

        /**
         * Calls the `register()` method of all the available service providers.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function registerProviders() {

                $providers = apply_filters( 'hybrid/app/providers', [
                        AttributesServiceProvider::class,
                        ConfigServiceProvider::class,
                        CustomizeServiceProvider::class,
                        LanguageServiceProvider::class,
                        MediaMetaServiceProvider::class,
                        ObjectTemplatesServiceProvider::class,
                        TemplateHierarchyServiceProvider::class,
                        ViewServiceProvider::class
                ] );

                foreach ( $providers as $provider ) {

                        $this->providers[ $provider ] = new $provider( $this );

                        $this->providers[ $provider ]->register();
                }
        }

        /**
         * Calls the `boot()` method of all the registered service providers.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function bootProviders() {

                foreach ( $this->providers as $provider ) {
                        $provider->boot();
                }
        }
}
