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

use Hybrid\Container\Container;
use Hybrid\Contracts\Application as ApplicationContract;
use Hybrid\Contracts\Bootable;
use Hybrid\Providers\Attributes;
use Hybrid\Providers\Config;
use Hybrid\Providers\Customize;
use Hybrid\Providers\Language;
use Hybrid\Providers\MediaMeta;
use Hybrid\Providers\Templates;
use Hybrid\Providers\TemplateHierarchy;
use Hybrid\Providers\View;

/**
 * Application class.
 *
 * @since  5.0.0
 * @access public
 */
class Application extends Container implements ApplicationContract, Bootable {

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
	public function boot() {

		$this->registerDefaultBindings();
		$this->registerDefaultProviders();
		$this->bootstrapFilters();

		// Register and boot providers at the earliest hook available to
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

		// Adds the directory path for the framework.
		$this->add( 'path', untrailingslashit( HYBRID_DIR  ) );

		// Add the version for the framework.
		$this->add( 'version', static::VERSION );
	}

	/**
	 * Adds the default service providers for the framework.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerDefaultProviders() {

		array_map( function( $provider ) {
			$this->provider( $provider );
		}, [
			Attributes::class,
			Config::class,
			Customize::class,
			Language::class,
			MediaMeta::class,
			Templates::class,
			TemplateHierarchy::class,
			View::class
		] );
	}

	/**
	 * Bootstrap action/filter hook calls.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function bootstrapFilters() {

		require_once( $this->path . '/bootstrap-filters.php' );
	}

	/**
	 * Adds a service provider.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string|object  $provider
	 * @return void
	 */
	public function provider( $provider ) {

		if ( is_string( $provider ) ) {
			$provider = $this->resolveProvider( $provider );
		}

		$this->providers[] = $provider;
	}

	/**
	 * Creates a new instance of a service provider class.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $provider
	 * @return object
	 */
	protected function resolveProvider( $provider ) {

		return new $provider( $this );
	}

	/**
	 * Calls a service provider's `register()` method if it exists.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $provider
	 * @return void
	 */
	protected function registerProvider( $provider ) {

		if ( method_exists( $provider, 'register' ) ) {
			$provider->register();
		}
	}

	/**
	 * Calls a service provider's `boot()` method if it exists.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $provider
	 * @return void
	 */
	protected function bootProvider( $provider ) {

		if ( method_exists( $provider, 'boot' ) ) {
			$provider->boot();
		}
	}

	/**
	 * Returns an array of service providers.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return array
	 */
	protected function getProviders() {

		return $this->providers;
	}

	/**
	 * Calls the `register()` method of all the available service providers.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function registerProviders() {

		foreach ( $this->getProviders() as $provider ) {
			$this->registerProvider( $provider );
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

		foreach ( $this->getProviders() as $provider ) {
			$this->bootProvider( $provider );
		}
	}
}
