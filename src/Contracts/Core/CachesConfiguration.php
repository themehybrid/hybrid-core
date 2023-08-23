<?php

namespace Hybrid\Contracts\Core;

interface CachesConfiguration {

    /**
     * Determine if the application configuration is cached.
     */
    public function configurationIsCached(): bool;

    /**
     * Get the path to the configuration cache file.
     */
    public function getCachedConfigPath(): string;

    /**
     * Get the path to the cached services.php file.
     */
    public function getCachedServicesPath(): string;

}
