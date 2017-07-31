# Change Log

You can see the changes made via the [commit log](https://github.com/justintadlock/hybrid-core/commits/master) for the latest release.

## [4.0.0] - 2017-07-31

### Added

* `.wp-custom-logo` body class.
* Allow the shortcode, autoembeds, embedded, and attached methods of the media grabber to be enabled/disabled.
* New Fonts API.  This allows developers to register, enqueue, etc. fonts just like scripts and styles.  The API natively supports font stylesheets bundled with the theme or the Google Fonts API.
* Introduces the `hybrid_get_theme_mod()` wrapper function, which provides a `hybrid_theme_mod_{$name}_default` filter hook for filtering the default theme mod.
* Adds a the `hybrid_is_layout()` conditional tag for checking if the current page has a specific layout.
* Adds a `hybrid_is_script_debug()` conditional function for checking if `WP_SCRIPT_DEBUG` is set.
* Adds the `hybrid_sprintf_theme_dir()` and `hybrid_sprintf_theme_uri()` functions. These functions replace `%1$s` with the template and `%2$s` with the stylesheet directory path and URI, respectively.
* Introduces `hybrid_get_embed_template()` and an embed template part system.  This allows theme authors to create embed templates based on the post type, format, etc.
* Full admin support for taxonomy term layouts.
* Added the `hybrid()` wrapper function for returning the instance of the `Hybrid` object.
* Adds the `hybrid_widget_exists()` conditional function for checking if a widget (by class name) exists.
* Introduces the `hybrid_comment_parent_link()` and `hybrid_get_comment_parent_link()` functions for displaying or getting the comment parent link of a child comment.
* Creates a new `hybrid_get_template_part()` function, which is a more robust version of the core WP `get_template_part()` function.
* Introduces `hybrid_verify_nonce_post()` and `hybrid_verify_nonce_request()` for easier nonce checking in the admin.
* Adds the `hybrid_get_content_hierarchy()` helper function for content and embed templates.  It retrieves the hierarchy based on post type, format, etc.
* New `.menu-item-parent-archive` nav menu item class when viewing a post and the post type archive item is in the menu.
* New template registration system.  This is post templates on steroids while being completely compatible with core WP's post template system.  Term and user templates are supported but no UI is yet implemented.
* New filter on `document_title_parts`.  This is actually a replacement for the old filter on `wp_title`.  This brings us up to date with new doc title hooks and uses less code.
* Adds the `hybrid_post_media()` and `hybrid_get_post_media()` wrapper functions for displaying and getting post media.  These are wrappers for the media grabber.
* New registry system for registering and storing collections of data on the page.  This replaces some of the old factory/registry methods from before and combines the functionality into a single class.
* Added the `hybrid_attr` hook for a more global filter on attributes.
* Added the `hybrid_attr_{$slug}_class` hook for theme authors to filter just the `class` attribute.

### Changed

* Split the archive description filter into two distinct functions:  one for filtering changing the text output and a later function for formatting.
* Registers Hybrid's styles on the front end, embeds, login page, and admin for use.
* Attribute system now allows for `0` or `'0'` as valid attribute values.  `false` is for explicit empty values.
* Overhauled the attributes (`hybrid_attr()`) system.  The framework no longer defines Schema.org as a default.  It leaves this up to theme authors.  Additionally, it only sets defaults for the body, post, and comment attributes.  The framework is just providing the foundation for theme authors to build what they want without getting in their way.
* The `Hybrid` class is now a singleton class.  Theme authors should no longer call `new Hybrid()`.  The framework will launch itself.
* User-selected page templates now overrule the theme author's choice if the theme author included a `front-page.php`.
* Adds the Hybrid Core version to its own stylesheets when registering.
* The template hierarchy system now uses the `*_template_hierarchy` filter hooks as opposed to the `*_template` filter hooks. This means that our code runs before looking up templates, which speeds things up.
* Updated the meta registration calls to be in line with the new WP method of registration.

### Removed

* All deprecated functions have been completely removed or added to the "removed" section.  Version 4.0.0 is a major release and not concerned with back-compat.
* Remove old post template admin and functionality since core WP now supports this.  It only took WP 7 years to catch up to the awesomeness that Hybrid users have long been enjoying.
* Mobile toggle script was removed in favor of just letting theme authors handle it.
* The global `$hybrid` variable is now dead.  Good riddance.
* Add the `render_content()` method for JS-based customizer controls to make sure we overwrite the `WP_Customize_Control` method.
* The Post Styles feature is now gone.  It was rarely used and just added bloat.
* Old filter on `wp_title`.
* Media meta, template, and layout hybrid factory-registry classes.

### Fixed

* Logic bug with post layout check.
* Post format body class error check.
* Make sure filters on `hybrid_content_template` work correctly.
* Remove 100% max width default for embeds, which caused the WP visual editor to infinitely grow in height when embedding an audio/video file.
* Make sure we have a nav menu object before attempting to get its name in `hybrid_get_menu_name()`.
* Missing `href` attribute for the `hybrid_get_image_size_links()` function.
* Make sure a layout exists before attempting to use it as the layout.
* Fixed incorrect call when unregistering a theme layout.
* Load the minified admin CSS file.
* Use `https` for the WordPress.org link.
* Adds the missing `.page-template-default` body class when no custom template is in use.
* Change the search results title to `Search results for: %s`. This avoids double quotation marks.

### Security

* Run `wp_kses_post()` over the content when splitting media from it in the media grabber.

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
