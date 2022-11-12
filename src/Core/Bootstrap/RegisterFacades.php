<?php

namespace Hybrid\Core\Bootstrap;

use Hybrid\Contracts\Core\Application;
use Hybrid\Core\AliasLoader;
use Hybrid\Core\Facades\Facade;
use Hybrid\Core\PackageManifest;

class RegisterFacades {

    /**
     * Bootstrap the given application.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function bootstrap( Application $app ) {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication( $app );

        $aliases = $app->make( 'config' )->get( 'app.aliases', [] );

        // If app doesn't define 'aliases' in `app.php`,
        // Hybrid Core wouldn't be able to use its default aliases.
        // so will have to initiate it here.
        if ( count( $aliases ) === 0 ) {
            $aliases = Facade::defaultAliases()->merge( [] )->toArray();
        }

        $aliases = array_merge(
            $aliases,
            $app->make( PackageManifest::class )->aliases()
        );

        AliasLoader::getInstance( $aliases )->register();
    }

}
