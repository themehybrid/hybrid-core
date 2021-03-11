# Hybrid Core: WordPress Theme Framework

Hybrid Core is a framework for developing WordPress themes.

It is a package that simplifies some of the complexities of theme development and helps you write more modern PHP code.  Its goal is to provide elegant functions, classes, and interfaces for writing code without having to rebuild everything from scratch for each theme.

## Requirements

* WordPress 4.9.6+.
* PHP 5.6+ (7.0+ recommended).
* [Composer](https://getcomposer.org/) for managing PHP dependencies.

The framework is coded to work on PHP 5.6+, but only 7.0+ is officially supported.

To ease developers into Composer, version 5.0 is a standalone package with no dependencies. Therefore, theme authors can still drop this directly into their theme and use a custom class autoloader. However, no such guarantee exists for future versions.

## Purchase or donate

Hybrid Core is free.  However, I ask that you purchase a support membership at [Theme Hybrid](https://themehybrid.com).  Even if you don't need support, every purchase helps fund the development of this project.

[Donations to the project](https://themehybrid.com/donate) are also welcome.

## Documentation

The documentation is handled via Hybrid Core's [wiki](https://github.com/justintadlock/hybrid-core/wiki).  This is community-contributed documentation that I hope you will add to if you use the framework.

### Installation

Use the following command from your preferred command line utility to install the package.

```bash
composer require justintadlock/hybrid-core
```

If bundling this directly in your theme, you'll need to add the following code to your `functions.php` to autoload the project (and any other dependencies).

```php
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once( get_parent_theme_file_path( 'vendor/autoload.php' ) );
}
```

### Bootstrapping Hybrid Core

Hybrid Core isn't launched until an instance of its `Hybrid\Core\Application` class is created and booted, which should look something like the following.

```php
// Create a new application.
$themeslug = new \Hybrid\Core\Application();

// Add service providers, bindings, etc.

// Bootstrap the application.
$themeslug->boot();
```

Check out the officially-supported [Mythic starter theme](https://github.com/justintadlock/mythic) for example code or as a good starting point for your HC-based project.

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2008&thinsp;&ndash;&thinsp;2021 &copy; [Justin Tadlock](http://justintadlock.com).
