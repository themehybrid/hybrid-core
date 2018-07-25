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

use function Hybrid\Template\locate as locate_template;

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
		$title = author_title();

	} elseif ( is_search() ) {
		$title = search_title();

	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );

	} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
		$title = minute_hour_title();

	} elseif ( get_query_var( 'minute' ) ) {
		$title = minute_title();

	} elseif ( get_query_var( 'hour' ) ) {
		$title = hour_title();

	} elseif ( is_day() ) {
		$title = day_title();

	} elseif ( get_query_var( 'w' ) ) {
		$title = week_title();

	} elseif ( is_month() ) {
		$title = single_month_title( ' ', false );

	} elseif ( is_year() ) {
		$title = year_title();

	} elseif ( is_archive() ) {
		$title = archive_title();
	}

	return apply_filters( 'hybrid/achive/title', $title );
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

	return $new_desc ?: $desc;
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

	return apply_filters( 'hybrid/archive/description', $desc );
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
			' <a href="%s" class="entry__more-link">%s</a>',
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

	// Add a class if the menu item has children.
	if ( in_array( 'menu-item-has-children', $classes ) ) {
		$_classes[] = 'has-children';
	}

	// Add custom user-added classes if we have any.
	$custom = get_post_meta( $item->ID, '_menu_item_classes', true );

	if ( $custom ) {
		$_classes = array_merge( $_classes, (array) $custom );
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
 * Filters the nav menu args when used for a widget.
 *
 * @since  5.0.0
 * @access public
 * @param  array    $args
 * @param  \WP_Term $menu
 * @return array
 */
function widget_nav_menu_args( $args, $menu ) {

	$args['container_class'] = sprintf(
		'menu menu--widget menu--%s',
		sanitize_html_class( $menu->slug )
	);

	$args['container_id'] = '';
	$args['menu_id']      = '';
	$args['menu_class']   = 'menu__items';

	return $args;
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
 * Overrides the default comments template.  This filter allows for a
 * `comments-{$post_type}.php` template based on the post type of the current
 * single post view.  If this template is not found, it falls back to the
 * default `comments.php` template.
 *
 * @since  5.0.0
 * @access public
 * @param  string $template
 * @return string
 */
function comments_template( $template ) {

	$templates = [];

	// Allow for custom templates entered into comments_template( $file ).
	$template = str_replace( trailingslashit( get_stylesheet_directory() ), '', $template );

	if ( 'comments.php' !== $template ) {
		$templates[] = $template;
	}

	// Add a comments template based on the post type.
	$templates[] = sprintf( 'comments/%s.php', get_post_type() );

	// Add the default comments template.
	$templates[] = 'comments/default.php';
	$templates[] = 'comments.php';

	// Return the found template.
	return locate_template( $templates );
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

	// Check to see if we can find a class.
	preg_match( '/((class=[\'"])(.*?)([\'"]))/i', $params[0]['before_widget'], $matches );

	// If we have matches for all 4 captured groups, let's go.
	if ( ! empty( $matches ) && ! array_diff_key( array_flip( [ 1, 2, 3, 4 ] ), $matches ) ) {

		$classes  = explode( ' ', $matches[3] );
		$_classes = [];

		// Create BEM-style widget classes.
		$_classes[] = 'widget';
		$_classes[] = sprintf( 'widget--%s', str_replace( '_', '-', $context ) );

		// Build BEM-style classes from original classes.
		foreach ( $classes as $class ) {

			$class = str_replace( [ 'widget-', 'widget_', 'widget' ], '', $class );

			if ( $class ) {
				$_classes[] = sprintf( 'widget--%s', $class );
			}
		}

		// Merge original classes and make sure there are no duplicates.
		$_classes = array_map(
			'sanitize_html_class',
			array_unique( array_merge( $_classes, $classes ) )
		);

		// Replaces the exact class string we captured earlier with the
		// new class string.
		$params[0]['before_widget'] = str_replace(
			$matches[1],
			$matches[2] . join( ' ', $_classes ) . $matches[4],
			$params[0]['before_widget']
		);
	}

	return $params;
}
