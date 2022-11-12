<?php

namespace Hybrid\Core\Providers;

class CoreServiceProvider extends AggregateServiceProvider {

    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [];

    /**
     * The singletons to register into the container.
     *
     * @var array
     */
    public $singletons = [];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        parent::register();
    }

}
