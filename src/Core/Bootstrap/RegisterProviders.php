<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Core\Application;

class RegisterProviders {

    /**
     * Bootstrap the given application.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function bootstrap( Application $app ) {
        $app->registerConfiguredProviders();
    }

}
