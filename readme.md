# Hybrid Core: WordPress Theme Framework

Hybrid Core (HC) is a framework for developing WordPress themes.

It is a drop-in library/package that simplifies some of the complexities of theme development.  Its goal is to provide elegant functions, classes, and interfaces for writing code without having to rebuild everything from scratch for each theme.

## Requirements

* PHP 5.6+ (7.0+ recommended).
* [Composer](https://getcomposer.org/) for managing PHP dependencies.

The framework is coded to work on PHP 5.6+, but only 7.0+ is officially supported.

To ease developers into Composer, version 5.0 is a standalone package with no dependencies. Therefore, theme authors can still drop this directly into their theme. However, no such guarantee exists for future versions.

## Documentation

The documentation is handled via Hybrid Core's [wiki](https://github.com/justintadlock/hybrid-core/wiki).  This is community-contributed documentation that I hope you will add to if you use the framework.

### Installation

**Composer**

Use the following command to install the package.

```
composer require justintadlock/hybrid-core
```

If bundling this directly in your theme, you'll need to add the following code to your `functions.php` to autoload the project (and any other dependencies).

```
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once( get_parent_theme_file_path( 'vendor/autoload.php' ) );
}
```

**Drop-in**

If just dropping Hybrid Core into your theme (assuming a folder name of `hybrid-core`), you'll want to add the following line of code to load up the framework.

```php
require_once( get_parent_theme_file_path( 'hybrid-core/src/bootstrap-hybrid.php' ) );
```

You'll also need to register an autoloader to handle loading Hybrid Core's classes. Typically, this is done via Composer. Since you've opted against using Composer, you need to manually handle loading classes.

```php
spl_autoload_register( function( $class ) {

	$namespace = 'Hybrid\\';

	// Bail if the class is not in our namespace.
	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	// Remove the namespace.
	$class = str_replace( $namespace, '', $class );

	// Build the filename.
	$file = str_replace( '\\', '/', $class );
	$file = get_parent_theme_file_path( "hybrid-core/src/{$file}.php" );

	// If the file exists for the class name, load it.
	if ( file_exists( $file ) ) {
		include( $file );
	}
} );
```

### Bootstrapping Hybrid Core

Hybrid Core isn't launched until its `app()` function is called for the first time, which is under the `Hybrid` namespace.  Assuming you're namespacing your code (you should be), this should look something like:

```
namespace ThemeName;

use function Hybrid\app;

# Bootstrap Hybrid Core.
app();
```

## FAQ

### Why was this framework built?

In 2008, I started work on Hybrid Core.  At the time, it was used in only one of my themes on my newly-launched site, [Theme Hybrid](https://themehybrid.com).  The idea was that I'd have a base of reusable scripts that I could use along with any theme I built in the future.  Eventually, others wanted to use it too, so I officially released it to the public.  Basically, I decided to share the framework that had gotten my own business started with the entire WordPress community.

### Who is Hybrid Core for?

Primarily, Hybrid Core is for me, Justin Tadlock.  I use it to build all of my [WordPress themes](https://themehybrid.com/themes) at Theme Hybrid, which is my plugin and theme site.

However, I also maintain it for other theme authors who need a solid framework behind their themes.

### Where can I ask support questions?

I have a membership site at [Theme Hybrid](https://themehybrid.com) where I'll answer any support questions you have via the site's support forums.

### Can I install Hybrid Core as a theme?

No, Hybrid Core is not a theme.  It is a framework that you drop into your theme folder to help you build themes.

### Wait. Aren't frameworks parent themes?

No, not really, they're not.  Unfortunately, many theme authors have co-opted the term "framework" and applied it to themes intended to be used solely as parent themes.  I suppose you could stretch the term framework to pretty much apply to any base code that you can build from, but this has meant for some confusing terminology in the WordPress community.  We already have a term for what these "framework" themes are &mdash; they're called "parent themes".  If you're interested in reading more on this topic, I recommend checking out "[Frameworks? Parent, child, and  grandchild themes?](http://justintadlock.com/archives/2010/08/16/frameworks-parent-child-and-grandchild-themes)" for an in-depth discussion on the subject.

### So, I can't have child themes?

You can't create child themes for Hybrid Core because it's not a theme.  However, Hybrid Core was built so that theme authors could create awesome parent (or standalone) themes.  You, your users, or other theme authors can build child themes for your themes.

I was one of the original theme authors to help pioneer the parent/child theme movement.  I'm a big believer in child themes being the absolute best way to make theme customizations.  Therefore, Hybrid Core is built with this idea in mind.  As you learn more about the framework, you'll understand how it makes child theme development even better.

## Donations

I make little money directly from Hybrid Core.  Developers (those who use the framework) account for only a small percentage of my paying club members.  This means that development time/money comes straight out of my own pocket.  I'm happy to do it and enjoy it.  However, [donations to the project](https://themehybrid.com/donate) are certainly appreciated and help greatly.

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2008&thinsp;&ndash;&thinsp;2018 &copy; [Justin Tadlock](http://justintadlock.com).
