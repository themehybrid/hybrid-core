<?php

namespace Hybrid\Core\Facades;

use Closure;
use function Hybrid\Tools\collect;

abstract class Facade {

    /**
     * The application instance being facaded.
     *
     * @var \Hybrid\Contracts\Core\Application
     */
    protected static $app;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;

    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = true;

    /**
     * Run a Closure when the facade has been resolved.
     *
     * @return void
     */
    public static function resolved( Closure $callback ) {
        $accessor = static::getFacadeAccessor();

        if ( static::$app->resolved( $accessor ) === true ) {
            $callback( static::getFacadeRoot() );
        }

        static::$app->afterResolving($accessor, static function ( $service ) use ( $callback ) {
            $callback( $service );
        });
    }

    /**
     * Hotswap the underlying instance behind the facade.
     *
     * @param  mixed $instance
     * @return void
     */
    public static function swap( $instance ) {
        static::$resolvedInstance[ static::getFacadeAccessor() ] = $instance;

        if ( isset( static::$app ) ) {
            static::$app->instance( static::getFacadeAccessor(), $instance );
        }
    }

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot() {
        return static::resolveFacadeInstance( static::getFacadeAccessor() );
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor() {
        throw new \RuntimeException( 'Facade does not implement getFacadeAccessor method.' );
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string $name
     * @return mixed
     */
    protected static function resolveFacadeInstance( $name ) {
        if ( isset( static::$resolvedInstance[ $name ] ) ) {
            return static::$resolvedInstance[ $name ];
        }

        if ( static::$app ) {
            if ( static::$cached ) {
                return static::$resolvedInstance[ $name ] = static::$app[ $name ];
            }

            return static::$app[ $name ];
        }
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param  string $name
     * @return void
     */
    public static function clearResolvedInstance( $name ) {
        unset( static::$resolvedInstance[ $name ] );
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances() {
        static::$resolvedInstance = [];
    }

    /**
     * Get the application default aliases.
     *
     * @return \Hybrid\Tools\Collection
     */
    public static function defaultAliases() {
        return collect([
            'Hybrid\App' => App::class,
        ]);
    }

    /**
     * Get the application instance behind the facade.
     *
     * @return \Hybrid\Contracts\Core\Application
     */
    public static function getFacadeApplication() {
        return static::$app;
    }

    /**
     * Set the application instance.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public static function setFacadeApplication( $app ) {
        static::$app = $app;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throws \RuntimeException
     */
    public static function __callStatic( $method, $args ) {
        $instance = static::getFacadeRoot();

        if ( ! $instance ) {
            throw new \RuntimeException( 'A facade root has not been set.' );
        }

        return $instance->$method( ...$args );
    }

}
