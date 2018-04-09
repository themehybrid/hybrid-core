<?php
/**
 * Template hierarchy service provider.
 *
 * This is the service provider for the template hierarchy. It's used to register
 * the template hierarchy with the container and boot it when needed.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Providers;

use Hybrid\Template\Hierarchy;

/**
 * Template hierarchy provider class.
 *
 * @since  5.0.0
 * @access public
 */
class TemplateHierarchyServiceProvider extends ServiceProvider {

        /**
         * Registration callback that adds a single instance of the template
         * hierarchy to the container.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function register() {

                $this->app->singleton( 'template_hierarchy', function( $container ) {

                        return new Hierarchy();
                } );
        }

        /**
         * Boot callback that is used to resolve the template hierarchy instance
         * once all the service providers have been loaded.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function boot() {

                $this->app->get( 'template_hierarchy' );
        }
}
