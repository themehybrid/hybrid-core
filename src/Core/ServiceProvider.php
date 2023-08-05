<?php
/**
 * Base service provider.
 *
 * This is the base service provider class. This is an abstract class that must
 * be extended to create new service providers for the application.
 *
 * @package   HybridCore
 * @link      https://github.com/themehybrid/hybrid-core
 *
 * @author    Theme Hybrid
 * @copyright Copyright (c) 2008 - 2023, Theme Hybrid
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Core;

use Hybrid\Contracts\Core\Application;

/**
 * Service provider class.
 *
 * @since  6.0.0
 *
 * @access public
 */
abstract class ServiceProvider {

    /**
     * Application instance. Sub-classes should use this property to access
     * the application (container) to add, remove, or resolve bindings.
     *
     * @since  6.0.0
     * @var    \Hybrid\Contracts\Core\Application
     *
     * @access protected
     */
    protected $app;

    /**
     * Accepts the application and sets it to the `$app` property.
     *
     * @since  6.0.0
     * @return void
     *
     * @access public
     */
    public function __construct( Application $app ) {
        $this->app = $app;
    }

    /**
     * Callback executed when the `Application` class registers providers.
     *
     * @since  6.0.0
     * @return void
     *
     * @access public
     */
    public function register() {}

    /**
     * Callback executed after all the service providers have been registered.
     * This is particularly useful for single-instance container objects that
     * only need to be loaded once per page and need to be resolved early.
     *
     * @since  6.0.0
     * @return void
     *
     * @access public
     */
    public function boot() {}

}
