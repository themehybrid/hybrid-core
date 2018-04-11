<?php
/**
 * Language service provider.
 *
 * This is the service provider for the language system, which binds an instance
 * of the framework's `Language` class to the container.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Providers;

use Hybrid\Language;

/**
 * Language provider.
 *
 * @since  5.0.0
 * @access public
 */
class LanguageServiceProvider extends ServiceProvider {

        /**
         * Registration callback that adds a single instance of the language
         * system to the container.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function register() {

                $this->app->singleton( 'language', function( $container ) {

                        return new Language();
                } );
        }

        /**
         * Boot callback that is used to resolve the language instance once all
         * the service providers have been loaded.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function boot() {

                $this->app->resolve( 'language' );
        }
}
