<?php

namespace Hybrid\Contracts\Core;

interface DeferrableProvider {

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides();

}
