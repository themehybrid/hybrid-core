# Hybrid Core: WordPress Framework

Hybrid Core is a framework for developing WordPress plugins and themes.

The core package is the application layer that primarily consists of a service container.  It can be used alone or alongside any of the available [Hybrid* packages](https://github.com/themehybrid/).

The framework simplifies many of the complexities of WordPress development and helps you write more modern PHP code.  Its goal is to provide elegant functions, classes, and interfaces for writing code without the need to rebuild everything from scratch with each project.

## Requirements

* WordPress 5.7+.
* PHP 5.6+ (7.0+ recommended).
* [Composer](https://getcomposer.org/) for managing PHP dependencies.

The framework is coded to work on PHP 5.6+, but only 7.0+ is officially supported.

## Purchase or donate

Hybrid Core is free.  However, [donations to the project](https://themehybrid.com/donate) are also welcome.

## Documentation

The documentation is handled via Hybrid Core's [wiki](https://github.com/themehybrid/hybrid-core/wiki).  This is community-contributed documentation that I hope you will add to if you use the framework.

### Installation

Use the following command from your preferred command line utility to install the package.

```bash
composer require themehybrid/hybrid-core
```

If bundling this directly in your plugin, add the following code.

**For plugins:**

```php
if ( file_exists( 'vendor/autoload.php' ) ) {
        require_once 'vendor/autoload.php';
}
```

**For themes:**

```php
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once( get_parent_theme_file_path( 'vendor/autoload.php' ) );
}
```

### Bootstrapping Hybrid Core

Hybrid Core isn't launched until an instance of its `Hybrid\Core\Application` class is created and its `boot()` method has been called.

Because the `Application` class can be called multiple times via both plugins and themes, developers need to check the `Hybrid\booted()` function before attempting to create a new app.  If one exists, they should use the existing instance via the `Hybrid\app()` helper function.

```php
// Create a new application.
$slug = \Hybrid\booted() ? \Hybrid\app() : new \Hybrid\Core\Application();

// Add service providers.

// Bootstrap the application.
$slug->boot();
```

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2008&thinsp;&ndash;&thinsp;2021 &copy; [Justin Tadlock](http://justintadlock.com).
