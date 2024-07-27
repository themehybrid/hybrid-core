<?php

namespace Hybrid\Core\Providers;

class CoreServiceProvider extends AggregateServiceProvider {

    /**
     * The provider class names.
     *
     * @var array<string>
     */
    protected $providers = [];

    /**
     * The singletons to register into the container.
     *
     * @var array
     */
    public $singletons = [];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

}
