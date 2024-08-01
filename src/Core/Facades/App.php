<?php

namespace Hybrid\Core\Facades;

/**
 * @see \Hybrid\Contracts\Core\Application
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
 *
 * @method static \Hybrid\Core\Configuration\ApplicationBuilder configure(string|null $basePath = null)
 * @method static string inferBasePath()
 * @method static \Hybrid\Core\Application loadEnvironmentFrom(string $file)
 * @method static \Hybrid\Core\ServiceProvider register(\Hybrid\Core\ServiceProvider|string $provider, bool $force = false)
 * @method static \Hybrid\Core\ServiceProvider|null getProvider(\Hybrid\Core\ServiceProvider|string $provider)
 * @method static \Hybrid\Core\ServiceProvider resolveProvider(string $provider)
 * @method static array getProviders(\Hybrid\Core\ServiceProvider|string $provider)
 * @method static mixed make($abstract, array $parameters = [])
 * @method static bool bound(string $abstract)
 * @method static mixed makeWith(string|callable $abstract, array $parameters = [])
 * @method static mixed get(string $id)
 * @method static mixed build(\Closure|string $concrete)
 * @method static void resolving(\Closure|string $abstract, \Closure|null $callback = null)
 * @method static void beforeResolving(\Closure|string $abstract, \Closure|null $callback = null)
 * @method static void afterResolving(\Closure|string $abstract, \Closure|null $callback = null)
 * @method static void afterResolvingAttribute(string $attribute, \Closure $callback)
 * @method static array getBindings()
 * @method static string getAlias(string $abstract)
 * @method static void forgetExtenders(string $abstract)
 * @method static bool configurationIsCached()
 * @method static void afterLoadingEnvironment(\Closure $callback)
 * @method static void beforeBootstrapping(string $bootstrapper, \Closure $callback)
 * @method static void afterBootstrapping(string $bootstrapper, \Closure $callback)
 * @method static bool hasBeenBootstrapped()
 * @method static \Hybrid\Core\Application setBasePath(string $basePath)
 * @method static string path(string $path = '')
 * @method static \Hybrid\Core\Application useAppPath(string $path)
 * @method static bool isLocal()
 * @method static bool isProduction()
 * @method static string basePath(string $path = '')
 * @method static string bootstrapPath(string $path = '')
 * @method static string getBootstrapProvidersPath()
 * @method static \Hybrid\Core\Application useBootstrapPath(string $path)
 * @method static string configPath(string $path = '')
 * @method static \Hybrid\Core\Application useConfigPath(string $path)
 * @method static \Hybrid\Core\Application usePublicPath(string $path)
 * @method static string detectEnvironment(\Closure $callback)
 * @method static bool runningInConsole()
 * @method static bool runningConsoleCommand(string|array ...$commands)
 * @method static bool runningUnitTests()
 * @method static bool hasDebugModeEnabled()
 * @method static void registered(callable $callback)
 * @method static string environmentFile()
 * @method static string environmentFilePath()
 * @method static string environmentPath()
 * @method static \Hybrid\Core\Application useEnvironmentPath(string $path)
 * @method static void forgetInstance(string $abstract)
 * @method static void forgetInstances()
 * @method static void forgetScopedInstances()
 * @method static \Hybrid\Core\Application getInstance()
 * @method static \Hybrid\Contracts\Container\Container|\Hybrid\Core\Application setInstance(\Hybrid\Contracts\Container\Container|null $container = null)
 * @method static string getCachedConfigPath()
 * @method static string getCachedPackagesPath()
 * @method static string getCachedRoutesPath()
 * @method static string getCachedServicesPath()
 * @method static bool eventsAreCached()
 * @method static string getCachedEventsPath()
 * @method static \Hybrid\Core\Application addAbsoluteCachePathPrefix(string $prefix)
 * @method static \Hybrid\Core\Application terminating(callable|string $callback)
 * @method static void terminate()
 * @method static array getLoadedProviders()
 * @method static bool providerIsLoaded(string $provider)
 * @method static void setDeferredServices(array $services)
 * @method static void addDeferredServices(array $services)
 * @method static bool isDeferredService(string $service)
 * @method static void provideFacades(string $namespace)
 * @method static void registerCoreContainerAliases()
 * @method static array getDeferredServices()
 * @method static void flush()
 * @method static string getNamespace()
 * @method static \Hybrid\Contracts\Container\ContextualBindingBuilder when(array|string $concrete)
 * @method static bool has(string $id)
 * @method static void whenHasAttribute(string $attribute, \Closure $handler)
 * @method static bool isShared(string $abstract)
 * @method static bool isAlias(string $name)
 * @method static void bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false)
 * @method static bool hasMethodBinding(string $method)
 * @method static void bindMethod(array|string $method, \Closure $callback)
 * @method static mixed callMethodBinding(string $method, mixed $instance)
 * @method static void addContextualBinding(string $concrete, string $abstract, \Closure|string $implementation)
 * @method static void bindIf(string $abstract, \Closure|string|null $concrete = null, bool $shared = false)
 * @method static void singleton(string $abstract, \Closure|string|null $concrete = null)
 * @method static void singletonIf(string $abstract, \Closure|string|null $concrete = null)
 * @method static void scoped(string $abstract, \Closure|string|null $concrete = null)
 * @method static void scopedIf(string $abstract, \Closure|string|null $concrete = null)
 * @method static void extend(string $abstract, \Closure $closure)
 * @method static mixed instance(string $abstract, mixed $instance)
 * @method static void tag(array|string $abstracts, array|mixed $tags)
 * @method static iterable tagged(string $tag)
 * @method static void alias(string $abstract, string $alias)
 * @method static mixed rebinding(string $abstract, \Closure $callback)
 * @method static mixed refresh(string $abstract, mixed $target, string $method)
 * @method static \Closure wrap(\Closure $callback, array $parameters = [])
 * @method static mixed call(callable|string $callback, array $parameters = [], string|null $defaultMethod = null)
 * @method static \Closure factory(string $abstract)
 * @method static string resourcePath(string $path = '')
 * @method static string storagePath(string $path = '')
 * @method static \Hybrid\Core\Application useStoragePath(string $path)
 * @method static \Hybrid\Core\Application useResourcePath(string $path)
 * @method static string viewPath(string $path = '')
 * @method static string joinPaths(string $basePath, string $path = '')
 * @method static string version()
 * @method static string|bool environment(string|array ...$environments)
 * @method static bool isBooted()
 * @method static void boot()
 * @method static void booted(callable $callback)
 * @method static void booting(callable $callback)
 * @method static void bootstrapWith(array<string> $bootstrappers)
 * @method static void loadDeferredProviders()
 * @method static void loadDeferredProvider(string $service)
 * @method static void registerConfiguredProviders()
 * @method static void registerDeferredProvider(string $provider, string $service = null)
 * @method static void macro(string $name, object|callable $macro, object|callable $macro = null)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static int handleCommand(\Symfony\Component\Console\Input\InputInterface $input)
 * @method static bool shouldMergeFrameworkConfiguration()
 * @method static \Hybrid\Core\Application dontMergeFrameworkConfiguration()
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