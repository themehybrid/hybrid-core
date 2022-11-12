<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Core\Application;

class BootProviders {

    /**
     * Bootstrap the given application.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function bootstrap( Application $app ) {
        $app->boot();
    }

}
