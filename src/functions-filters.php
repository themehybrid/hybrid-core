<?php
/**
 * Filter functions.
 *
 * Filters for theme-related WordPress features.  These filters are for handling
 * adding or modifying the output of common WordPress template tags to make for
 * a richer theme development experience without having to resort to custom
 * template tags.  Many of the filters are simply for adding HTML5 microdata.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

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

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but
 * they're not registered.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function post_type_support() {

	// Add support for excerpts to the 'page' post type.
	add_post_type_support( 'page', 'excerpt' );

	// Add thumbnail support for audio and video attachments.
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );
}

/**
 * Filters `get_the_archve_title` to add better archive titles than core.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $title
 * @return string
 */
function archive_title_filter( $title ) {

	if ( is_home() && ! is_front_page() ) {
		$title = get_post_field( 'post_title', get_queried_object_id() );

	} elseif ( is_category() ) {
		$title = single_cat_title( '', false );

	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );

	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );

	} elseif ( is_author() ) {
		$title = get_single_author_title();

	} elseif ( is_search() ) {
		$title = get_search_title();

	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );

	} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
		$title = get_single_minute_hour_title();

	} elseif ( get_query_var( 'minute' ) ) {
		$title = get_single_minute_title();

	} elseif ( get_query_var( 'hour' ) ) {
		$title = get_single_hour_title();

	} elseif ( is_day() ) {
		$title = get_single_day_title();

	} elseif ( get_query_var( 'w' ) ) {
		$title = get_single_week_title();

	} elseif ( is_month() ) {
		$title = single_month_title( ' ', false );

	} elseif ( is_year() ) {
		$title = get_single_year_title();

	} elseif ( is_archive() ) {
		$title = get_single_archive_title();
	}

	return apply_filters( 'hybrid/achive_title', $title );
}

/**
 * Filters `get_the_archve_description` to add better archive descriptions than core.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function archive_description_filter( $desc ) {

	$new_desc = '';

	if ( is_home() && ! is_front_page() ) {
		$new_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	} elseif ( is_category() ) {
		$new_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	} elseif ( is_tag() ) {
		$new_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	} elseif ( is_tax() ) {
		$new_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	} elseif ( is_author() ) {
		$new_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	} elseif ( is_post_type_archive() ) {
		$new_desc = get_post_type_object( get_query_var( 'post_type' ) )->description;
	}

	return $new_desc ? $new_desc : $desc;
}

/**
 * Filters `get_the_archve_description` to add custom formatting.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function archive_description_format( $desc ) {

	return apply_filters( 'hybrid/archive_description', $desc );
}

/**
 * The WordPress.org theme review requires that a link be provided to the single
 * post page for untitled posts.  This is a filter on 'the_title' so that an
 * `(Untitled)` title appears in that scenario, allowing for the normal method
 * to work.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $title
 * @return string
 */
function untitled_post( $title ) {

	// Translators: Used as a placeholder for untitled posts on non-singular views.
	if ( ! $title && ! is_singular() && in_the_loop() && ! is_admin() ) {

		$title = esc_html__( '(Untitled)', 'hybrid-core' );
	}

	return $title;
}

/**
 * Filters the excerpt more output with internationalized text and a link to the post.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $text
 * @return string
 */
function excerpt_more( $text ) {

	if ( 0 !== strpos( $text, '<a' ) ) {

		$text = sprintf(
			' <a href="%s" class="more-link">%s</a>',
			esc_url( get_permalink() ),
			trim( $text )
		);
	}

	return $text;
}

/**
 * Simplifies the nav menu class system.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $classes
 * @param  object  $item
 * @return array
 */
function nav_menu_css_class( $classes, $item ) {

	$_classes = [ 'menu__item' ];

	foreach ( [ 'item', 'parent', 'ancestor' ] as $type ) {

		if ( in_array( "current-menu-{$type}", $classes ) || in_array( "current_page_{$type}", $classes ) ) {

			$_classes[] = 'item' === $type ? 'menu__item--current' : "menu__item--{$type}";
		}
	}

	// If the menu item is a post type archive and we're viewing a single
	// post of that post type, the menu item should be an ancestor.
	if ( 'post_type' === $item->type && is_singular( $item->object ) && ! in_array( 'menu__item--ancestor', $_classes ) ) {
		$_classes[] = 'menu__item--ancestor';
	}

	return $_classes;
}

/**
 * Adds a custom class to the nav menu link.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $attr;
 * @return array
 */
function nav_menu_link_attributes( $attr ) {

	$attr['class'] = 'menu__anchor';

	return $attr;
}

/**
 * Adds a custom class to the submenus in nav menus.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $classes
 * @return array
 */
function nav_menu_submenu_css_class( $classes ) {

	$classes = [ 'menu__sub-menu' ];

	return $classes;
}

/**
 * Overwrites the HTML classes for the comment form default fields.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $fields
 * @return array
 */
function comment_form_default_fields( $fields ) {

	array_walk( $fields, function( &$field, $key ) {

	 	$field = replace_html_class(
			"comment-respond__field comment-respond__field--{$key}",
			$field
		);
	} );

	return $fields;
}

/**
 * Overwrites the HTML classes for various comment form elements.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $defaults
 * @return array
 */
function comment_form_defaults( $defaults ) {

	// Classes we can set.
	$defaults['class_form']   = 'comment-respond__form';
	$defaults['class_submit'] = 'comment-respond__submit';

	// Field wrappers.
	$defaults['comment_field'] = replace_html_class( 'comment-respond__field comment-respond__field--comment', $defaults['comment_field'] );
	$defaults['submit_field']  = replace_html_class( 'comment-respond__field comment-respond__field--submit',  $defaults['submit_field']  );

	// Other elements.
	$defaults['must_log_in']          = replace_html_class( 'comment-respond__must-log-in',  $defaults['must_log_in']          );
	$defaults['logged_in_as']         = replace_html_class( 'comment-respond__logged-in-as', $defaults['logged_in_as']         );
	$defaults['comment_notes_before'] = replace_html_class( 'comment-respond__notes',        $defaults['comment_notes_before'] );
	$defaults['title_reply_before']   = replace_html_class( 'comment-respond__reply-title',  $defaults['title_reply_before']   );

	return $defaults;
}

/**
 * Fix for users who want to display content on the posts page above the posts
 * list, which is a theme feature common to themes built from the framework.
 *
 * @since  5.0.0
 * @access public
 * @param  object  $post
 * @return void
 */
function enable_posts_page_editor( $post ) {

	if ( get_option( 'page_for_posts' ) != $post->ID ) {
		return;
	}

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( $post->post_type, 'editor' );
}

/**
 * Attempts to fix widget class naming woes. If the theme author uses the
 * `widget--%1$s` class, we'll strip the widget instance. If the theme author
 * uses the `widget--%2$s` class, we'll fix any double `widget--widget` problems.
 * And, if the author does use a widget ID in the class, we'll try to add that in.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $params
 * @return array
 */
function widget_class_filter( $params ) {

	$widget_id = $params[0]['widget_id'];
	$instance  = $params[1]['number'];
	$context   = str_replace( "-{$instance}", '', $widget_id );

	// If the theme author is emplying BEM-style classes with the widget ID,
	// let's remove the widget instance from the class.
	if ( false !== strpos( $params[0]['before_widget'], " widget--{$widget_id}" ) ) {

		$pattern = " widget--{$widget_id}";
		$replace = sprintf( " widget--%s", sanitize_html_class( $context ) );

		$params[0]['before_widget'] = str_replace( $pattern, $replace, $params[0]['before_widget'] );

	// If the theme author isn't employing BEM-style classes with the widget
	// ID, let's add that in.
	} elseif ( false === strpos( $params[0]['before_widget'], " widget--{$context}" ) ) {

		$pattern = "widget ";
		$replace = sprintf( 'widget widget--%s ', sanitize_html_class( $context ) );

		$params[0]['before_widget'] = str_replace( $pattern, $replace, $params[0]['before_widget'] );
	}

	// If we get a double `widget--widget` class name, let's fix that.
	if ( false !== strpos( $params[0]['before_widget'], ' widget--widget' ) ) {

		$pattern = [ ' widget--widget_', ' widget--widget-' ];
		$replace = ' widget--';

		$params[0]['before_widget'] = str_replace( $pattern, $replace, $params[0]['before_widget'] );
	}

	return $params;
}
