<?php

namespace Hybrid\Core\Facades;

/**
 * @see \Hybrid\Contracts\Core\Application
 *
 * @method static \Hybrid\Contracts\Core\Application loadEnvironmentFrom(string $file)
 * @method static \Hybrid\Core\ServiceProvider register(\Hybrid\Core\ServiceProvider|string $provider, bool $force = false)
 * @method static \Hybrid\Core\ServiceProvider resolveProvider(string $provider)
 * @method static array getProviders(\Hybrid\Core\ServiceProvider|string $provider)
 * @method static mixed make($abstract, array $parameters = [])
 * @method static mixed makeWith($abstract, array $parameters = [])
 * @method static bool configurationIsCached()
 * @method static bool hasBeenBootstrapped()
 * @method static bool isLocal()
 * @method static bool isProduction()
 * @method static string basePath(string $path = '')
 * @method static string bootstrapPath(string $path = '')
 * @method static string configPath(string $path = '')
 * @method static string detectEnvironment(callable $callback)
 * @method static string environmentFile()
 * @method static string environmentFilePath()
 * @method static string environmentPath()
 * @method static void forgetInstance(string $abstract)
 * @method static string getCachedConfigPath()
 * @method static string getCachedPackagesPath()
 * @method static string getCachedRoutesPath()
 * @method static string getCachedServicesPath()
 * @method static string getNamespace()
 * @method static string resourcePath(string $path = '')
 * @method static string storagePath(string $path = '')
 * @method static string version()
 * @method static string|bool environment(string|array ...$environments)
 * @method static void boot()
 * @method static void booted(callable $callback)
 * @method static void booting(callable $callback)
 * @method static void bootstrapWith(array $bootstrappers)
 * @method static void loadDeferredProviders()
 * @method static void registerConfiguredProviders()
 * @method static void registerDeferredProvider(string $provider, string $service = null)
 */
class App extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'app';
    }

}
