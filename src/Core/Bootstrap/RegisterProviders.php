<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Core\Application;

class RegisterProviders {

    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function bootstrap( Application $app ) {
        $app->registerConfiguredProviders();
    }

}
