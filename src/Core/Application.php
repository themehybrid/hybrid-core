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

use Hybrid\Attr\AttrServiceProvider;
use Hybrid\Container\Container;
use Hybrid\Contracts\Core\Application as ApplicationContract;
use Hybrid\Contracts\Bootable;
use Hybrid\Lang\LanguageServiceProvider;
use Hybrid\Media\MetaServiceProvider;
use Hybrid\Proxies\Proxy;
use Hybrid\Proxies\App;
use Hybrid\Template\HierarchyServiceProvider;
use Hybrid\Template\TemplatesServiceProvider;
use Hybrid\View\ViewServiceProvider;

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
	const VERSION = '5.0.1';

	/**
	 * Array of service provider objects.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $providers = [];

	/**
	 * Array of static proxy classes and aliases.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $proxies = [];

	/**
	 * Registers the default bindings, providers, and proxies for the
	 * framework.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->registerDefaultBindings();
		$this->registerDefaultProviders();
		$this->registerDefaultProxies();
		$this->bootstrapFilters();
	}

	/**
	 * Calls the functions to register and boot providers and proxies.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		$this->registerProviders();
		$this->bootProviders();
		$this->registerProxies();
	}

	/**
	 * Registers the default bindings we need to run the framework.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerDefaultBindings() {

		// Add the instance of this application.
		$this->instance( 'app', $this );

		// Adds the directory path for the framework.
		$this->instance( 'path', untrailingslashit( HYBRID_DIR  ) );

		// Add the version for the framework.
		$this->instance( 'version', static::VERSION );
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
			AttrServiceProvider::class,
			LanguageServiceProvider::class,
			MetaServiceProvider::class,
			TemplatesServiceProvider::class,
			HierarchyServiceProvider::class,
			ViewServiceProvider::class
		] );
	}

	/**
	 * Adds the default static proxy classes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerDefaultProxies() {

		$this->proxy( App::class, '\Hybrid\App' );
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
	 * @access protected
	 * @return void
	 */
	protected function registerProviders() {

		foreach ( $this->getProviders() as $provider ) {
			$this->registerProvider( $provider );
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

		foreach ( $this->getProviders() as $provider ) {
			$this->bootProvider( $provider );
		}
	}

	/**
	 * Adds a static proxy alias. Developers must pass in fully-qualified
	 * class name and alias class name.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $class_name
	 * @param  string  $alias
	 * @return void
	 */
	public function proxy( $class_name, $alias ) {

		$this->proxies[ $class_name ] = $alias;
	}

	/**
	 * Registers the static proxy classes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerProxies() {

		Proxy::setContainer( $this );

		foreach ( $this->proxies as $class => $alias ) {
			class_alias( $class, $alias );
		}
	}
}
