<?php
/**
 * Filter/Action bootstrap.
 *
 * Any actions/filters added off-the-bat get added here so that they're only
 * called in the WP environment.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Attributes for a few default elements.
add_filter( 'hybrid/attr/html/defaults',    __NAMESPACE__ . '\Attr\filter_html',    ~PHP_INT_MAX );
add_filter( 'hybrid/attr/body/defaults',    __NAMESPACE__ . '\Attr\filter_body',    ~PHP_INT_MAX );
add_filter( 'hybrid/attr/post/defaults',    __NAMESPACE__ . '\Attr\filter_post',    ~PHP_INT_MAX );
add_filter( 'hybrid/attr/entry/defaults',   __NAMESPACE__ . '\Attr\filter_post',    ~PHP_INT_MAX ); // Alternate for "post".
add_filter( 'hybrid/attr/comment/defaults', __NAMESPACE__ . '\Attr\filter_comment', ~PHP_INT_MAX );

# Filters the WordPress element classes.
add_filter( 'body_class',    __NAMESPACE__ . '\body_class_filter',    ~PHP_INT_MAX, 2 );
add_filter( 'post_class',    __NAMESPACE__ . '\post_class_filter',    ~PHP_INT_MAX, 3 );
add_filter( 'comment_class', __NAMESPACE__ . '\comment_class_filter', ~PHP_INT_MAX, 4 );

# Add extra support for post types.
add_action( 'init', __NAMESPACE__ . '\post_type_support', 15 );

# Filters the archive title and description.
add_filter( 'get_the_archive_title',       __NAMESPACE__ . '\archive_title_filter',       5           );
add_filter( 'get_the_archive_description', __NAMESPACE__ . '\archive_description_filter', 0           );
add_filter( 'get_the_archive_description', __NAMESPACE__ . '\archive_description_format', PHP_INT_MAX );

# Use same default filters as 'the_content' with a little more flexibility.
add_filter( 'hybrid/archive_description', [ $GLOBALS['wp_embed'], 'run_shortcode' ],   5  );
add_filter( 'hybrid/archive_description', [ $GLOBALS['wp_embed'], 'autoembed'     ],   5  );
add_filter( 'hybrid/archive_description',                         'wptexturize',       10 );
add_filter( 'hybrid/archive_description',                         'convert_smilies',   15 );
add_filter( 'hybrid/archive_description',                         'convert_chars',     20 );
add_filter( 'hybrid/archive_description',                         'wpautop',           25 );
add_filter( 'hybrid/archive_description',                         'do_shortcode',      30 );
add_filter( 'hybrid/archive_description',                         'shortcode_unautop', 35 );

# Don't strip tags on single post titles.
remove_filter( 'single_post_title', 'strip_tags' );

# Filters the title for untitled posts.
add_filter( 'the_title', __NAMESPACE__ . '\untitled_post' );

# Default excerpt more.
add_filter( 'excerpt_more', __NAMESPACE__ . '\excerpt_more', 5 );

# Adds custom CSS classes to nav menu items.
add_filter( 'nav_menu_css_class',         __NAMESPACE__ . '\nav_menu_css_class',         5, 2 );
add_filter( 'nav_menu_submenu_css_class', __NAMESPACE__ . '\nav_menu_submenu_css_class', 5    );
add_filter( 'nav_menu_link_attributes',   __NAMESPACE__ . '\nav_menu_link_attributes',   5    );

# Adds custom CSS classes to the comment form fields.
add_filter( 'comment_form_default_fields', __NAMESPACE__ . '\comment_form_default_fields', ~PHP_INT_MAX );
add_filter( 'comment_form_defaults',       __NAMESPACE__ . '\comment_form_defaults',       ~PHP_INT_MAX );

# Allow the posts page to be edited.
add_action( 'edit_form_after_title', __NAMESPACE__ . '\enable_posts_page_editor', 0 );

# Filters widget classes.
add_filter( 'dynamic_sidebar_params', __NAMESPACE__ . '\widget_class_filter', ~PHP_INT_MAX );

# Add support for structured post formats.
add_action( 'wp_loaded', __NAMESPACE__ . '\structured_post_formats', 0 );

# Adds common theme items to <head>.
add_action( 'wp_head', __NAMESPACE__ . '\meta_charset',   0 );
add_action( 'wp_head', __NAMESPACE__ . '\meta_viewport',  1 );
add_action( 'wp_head', __NAMESPACE__ . '\meta_generator', 1 );
add_action( 'wp_head', __NAMESPACE__ . '\link_pingback',  3 );

# Filter the WordPress title.
add_filter( 'document_title_parts', __NAMESPACE__ . '\document_title_parts', 5 );

# Remove the default emoji styles. We'll handle this in the stylesheet.
remove_action( 'wp_print_styles', 'print_emoji_styles' );

# Run hook for registering templates.
add_action( 'init', __NAMESPACE__ . '\Template\register_templates', 95 );

# Filter theme post templates to add registered templates.
add_filter( 'theme_templates', __NAMESPACE__ . '\Template\post_templates_filter', 5, 4 );

# Filter the comments template.
add_filter( 'comments_template', __NAMESPACE__ . '\comments_template', 5 );
