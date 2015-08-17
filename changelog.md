# Change Log

You can see the changes made via the [commit log](https://github.com/justintadlock/hybrid-core/commits/master) for the latest release.

## [3.0.0] - 2015-08-17

### Added

* Media grabber now supports the `[gallery]` shortcode.
* Custom attributes can now be passed into `hybrid_attr()`.
* Fix for core WP issues that doesn't allow users to edit the posts page content.
* Separated One Five style into `one-five.css` and `one-five-rtl.css`.
* Added color palette customizer control.
* Added multiple checkbox customizer control.
* Added dropdown terms customizer control.
* Added layout customizer control.
* Added radio image customizer control.
* Added select customizer control that supports `<optgroup>`.
* Added multiple select customizer control.
* Added array map customizer setting.
* Added image data customizer setting.
* Complete overhaul of theme layouts with a new Layouts API and many UI and other improvements.
* The post class now has the `.has-pages` class when it's a paged post.
* New `Hybrid_Chat` class that better handles chat posts.
* New `hybrid_is_plural()` function to go along with core WP's `is_singular()`.
* New `hybrid_get_attachment_type()` and `hybrid_get_attachment_subtype()` functions for getting attachment mime types.
* WordPress.org and theme URLs now use the translated URLs if available so that translators can link to language-specific pages/sites.
* Language-specific PHP files are now loaded from the child theme (first) and parent theme.
* New media metadata class and functions.  This makes it easy to get media file metadata for attachments (limited to audio, video, and images).
* Adds context-based classes to sidebars and menus.
* Post stylesheets extension now built into the framework.  Added extra functions.
* Added extra functions for working with post templates.
* Adds support for WP's `title-tag` feature.

### Changed

* `Hybrid` class now loads a few things at different priorities for more flexibility.
* Files and folders were renamed and restructured to make a bit more sense.
* Only load extensions if both the theme supports them and if the file exists.
* Overhauled the constants.
* Coding style updates.  PHPDoc blocks stay the same.  Single-line comments within functions now use `//` and single-line comments within the file use `#`.
* The theme-specific "template" `<meta>` tag is now a "generator" `<meta>` tag.
* All framework constants have a trailing slash.

### Deprecated

* `hybrid_loop_title()` in favor of WP's `the_archive_title()`.
* `hybrid_loop_description()` in favor of WP's `the_achive_description()`.
* `loop_pagination()` in favor of WP's `the_posts_pagination()`.
* `hybrid_get_attachment_id_from_url()` in favor of WP's `attachment_url_to_postid()`.

### Removed

* Dropped the `hybrid_is_textdomain_loaded()` functionality (wasn't used since 2.0.0).
* `reset.css` stylesheet.
* `hybrid-core-styles` and `hybrid-core-scripts`.  The specific styles/scripts are now registered, leaving the enqueue to theme authors.
* Filter on image size names.
* Filter on id3 keys.
* Several text strings in an effort to make the framework have as few text strings as possible.
* `hybrid_admin_setup()`, which was not really needed.
* Title attributes for most everything for better accessibility.
* bbPress theme support filter removal.
* Atomic hooks functionality.
* Random Custom Background extension.
* Featured Header extension.
* Cleaner Caption extension.
* Custom background customizer control.
* Textarea customizer control.
* Old, unused wrapper for admin theme settings page. Use the customizer!
* Admin widgets screen CSS file.

### Fixed

* Schema.org comment microdata brought up to date.
* Schema.org body/content/post errors.
* Media grabber fix when WP auto-embed returns a shortcode.
* Fixed invalid `itemprop` attribute for comment reply link with no space.

### Security

* Many text strings are now escaped on output to harden security.
* Better escaping over URLs to harden security.

## Past change logs

Versions prior to 3.0.0 do not have change logs. However, you can view the commit logs for each version.

* [2.0.4](https://github.com/justintadlock/hybrid-core/tree/2.0.4)
* [2.0.3](https://github.com/justintadlock/hybrid-core/tree/2.0.3)
* [2.0.2](https://github.com/justintadlock/hybrid-core/tree/2.0.2)
* [2.0.1](https://github.com/justintadlock/hybrid-core/tree/2.0.1)
* [2.0.0](https://github.com/justintadlock/hybrid-core/commits/2.0)
* [1.6.2](https://github.com/justintadlock/hybrid-core/tree/1.6.2)
* [1.6.1](https://github.com/justintadlock/hybrid-core/tree/1.6.1)
* [1.6.0](https://github.com/justintadlock/hybrid-core/tree/1.6.0)
* [1.5.5](https://github.com/justintadlock/hybrid-core/tree/1.5.5)
* [1.5.4](https://github.com/justintadlock/hybrid-core/tree/1.5.4)
* [1.5.3](https://github.com/justintadlock/hybrid-core/tree/1.5.3)
* [1.5.2](https://github.com/justintadlock/hybrid-core/tree/1.5.2)
* [1.5.1](https://github.com/justintadlock/hybrid-core/tree/1.5.1)
* [1.5.0](https://github.com/justintadlock/hybrid-core/tree/1.5)
* [1.4.3](https://github.com/justintadlock/hybrid-core/tree/1.4.3)
* [1.4.2](https://github.com/justintadlock/hybrid-core/tree/1.4.2)
* [1.4.1](https://github.com/justintadlock/hybrid-core/tree/1.4.1)
* [1.4.0](https://github.com/justintadlock/hybrid-core/tree/1.4)
* [1.3.1](https://github.com/justintadlock/hybrid-core/tree/1.3.1)
* [1.3.0](https://github.com/justintadlock/hybrid-core/tree/1.3)
* [1.2.1](https://github.com/justintadlock/hybrid-core/tree/1.2.1)
* [1.2.0](https://github.com/justintadlock/hybrid-core/tree/1.2)
* [1.1.1](https://github.com/justintadlock/hybrid-core/tree/1.1.1)
* [1.1.0](https://github.com/justintadlock/hybrid-core/tree/1.1)