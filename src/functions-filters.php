<?php
/**
 * Filter functions.
 *
 * Filters for theme-related WordPress features.  These filters are for handling
 * adding or modifying the output of common WordPress template tags to make for
 * a richer theme development experience.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

use WP_User;
use Hybrid\Util\Title;
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
 * Adds the meta charset to the header.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function meta_charset() {

	echo apply_filters(
		'hybrid/head/meta/charset',
		sprintf( '<meta charset="%s" />' . "\n", esc_attr( get_bloginfo( 'charset' ) ) )
	);
}

/**
 * Adds the meta viewport to the header.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function meta_viewport() {

	echo apply_filters(
		'hybrid/head/meta/viewport',
		'<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n"
	);
}

/**
 * Adds the theme generator meta tag.  This is particularly useful for checking
 * theme users' version when handling support requests.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function meta_generator() {
	$theme = wp_get_theme( \get_template() );

	$generator = sprintf(
		'<meta name="generator" content="%s %s" />' . "\n",
		esc_attr( $theme->get( 'Name' ) ),
		esc_attr( $theme->get( 'Version' ) )
	);

	echo apply_filters( 'hybrid/head/meta/generator', $generator );
}

/**
 * Adds the pingback link to the header.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function link_pingback() {

	$link = '';

	if ( 'open' === get_option( 'default_ping_status' ) ) {

		$link = sprintf(
			'<link rel="pingback" href="%s" />' . "\n",
			esc_url( get_bloginfo( 'pingback_url' ) )
		);
	}

	echo apply_filters( 'hybrid/head/link/pingback', $link );
}

/**
 * Replacement for the older filter on `wp_title` since WP has moved to a new
 * document title system.  This new filter merely alters the `title` key based
 * on the current page being viewed.  It also makes sure that all tags are
 * stripped, which WP doesn't do by default (it escapes HTML).
 *
 * @since  5.0.0
 * @access public
 * @param  array   $doctitle
 * @return array
 */
function document_title_parts( $doctitle ) {

	$doctitle['title'] = Title::current();

	// Return the title and make sure to strip tags.
	return array_map( 'strip_tags', $doctitle );
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

	return apply_filters( 'hybrid/archive/title', Title::current() );
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
		$new_desc = get_the_post_type_description();
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
 * Adds custom classes to the core WP logo.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $logo
 * @return string
 */
function custom_logo_class( $logo ) {

	$logo = preg_replace(
		"/(<a.+?)class=(['\"])(.+?)(['\"])/i",
		'$1class=$2app-header__logo-link $3$4',
		$logo,
		1
	);

	return preg_replace(
		"/(<img.+?)class=(['\"])(.+?)(['\"])/i",
		'$1class=$2app-header__logo $3$4',
		$logo,
		1
	);
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
	if ( 'post_type_archive' === $item->type && is_singular( $item->object ) && ! in_array( 'menu__item--ancestor', $_classes ) ) {
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

	$attr['class'] = 'menu__link';

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

/**
 * Filters the WordPress body class with a better set of classes that are more
 * consistently handled and are backwards compatible with the original body
 * class functionality that existed prior to WordPress core adopting this feature.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $classes
 * @param  array  $class
 * @return array
 */
function body_class_filter( $classes, $class ) {

	$classes = [];

	// Text direction.
	$classes[] = is_rtl() ? 'rtl' : 'ltr';

	// Locale and language.
	$locale = get_locale();
	$lang   = substr( $locale, 0, strpos( $locale, '_' ) );

	if ( $lang && $locale !== $lang ) {
		$classes[] = $lang;
	}

	$classes[] = strtolower( str_replace( '_', '-', $locale ) );

	// Multisite check adds the 'multisite' class and the blog ID.
	if ( is_multisite() ) {
		$classes[] = 'multisite';
		$classes[] = 'blog-' . get_current_blog_id();
	}

	// Plural/multiple-post view (opposite of singular).
	if ( is_home() || is_archive() || is_search() ) {
		$classes[] = 'plural';
	}

	// Front page of the site.
	if ( is_front_page() ) {
		$classes[] = 'home';
	}

	// Blog page.
	if ( is_home() ) {
		$classes[] = 'blog';

	// Singular views.
	} elseif ( is_singular() ) {

		// Get the queried post object.
		$post      = get_queried_object();
		$post_id   = get_queried_object_id();
		$post_type = $post->post_type;

		$classes[] = 'single';
		$classes[] = "single-{$post_type}";
		$classes[] = "single-{$post_type}-{$post_id}";

		// Checks for custom template.
		$template = str_replace(
			[ "{$post_type}-template-", "{$post_type}-", 'template-', 'tmpl-' ],
			'',
			basename( get_page_template_slug( $post_id ), '.php' )
		);

		$classes[] = $template ? "{$post_type}-template-{$template}" : "{$post_type}-template-default";

		// Post format.
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) ) {
			$post_format = \get_post_format( $post_id );

			$classes[] = $post_format && ! is_wp_error( $post_format ) ? "{$post_type}-format-{$post_format}" : "{$post_type}-format-standard";
		}

		// Attachment mime types.
		if ( is_attachment() ) {

			foreach ( explode( '/', get_post_mime_type() ) as $type ) {
				$classes[] = "attachment-{$type}";
			}
		}

	// Archive views.
	} elseif ( is_archive() ) {
		$classes[] = 'archive';

		// Post type archives.
		if ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );

			$classes[] = sprintf(
				'archive-%s',
				is_array( $post_type ) ? reset( $post_type ) : $post_type
			);
		}

		// Taxonomy archives.
		if ( is_tax() || is_category() || is_tag() ) {

			// Get the queried term object.
			$term     = get_queried_object();
			$term_id  = get_queried_object_id();
			$taxonomy = $term->taxonomy;

			$slug = 'post_format' === $taxonomy ? str_replace( 'post-format-', '', $term->slug ) : $term->slug;

			$classes[] = 'taxonomy';
			$classes[] = "taxonomy-{$taxonomy}";
			$classes[] = "taxonomy-{$taxonomy}-" . sanitize_html_class( $slug, $term_id );
		}

		// User/author archives.
		if ( is_author() ) {
			$user_id = get_query_var( 'author' );

			$classes[] = 'author';
			$classes[] = 'author-' . sanitize_html_class( get_the_author_meta( 'user_nicename', $user_id ), $user_id );
		}

		// Date archives.
		if ( is_date() ) {
			$classes[] = 'date';

			if ( is_year() ) {
				$classes[] = 'year';
			}

			if ( is_month() ) {
				$classes[] = 'month';
			}

			if ( get_query_var( 'w' ) ) {
				$classes[] = 'week';
			}

			if ( is_day() ) {
				$classes[] = 'day';
			}
		}

		// Time archives.
		if ( is_time() ) {
			$classes[] = 'time';

			if ( get_query_var( 'hour' ) ) {
				$classes[] = 'hour';
			}

			if ( get_query_var( 'minute' ) ) {
				$classes[] = 'minute';
			}
		}
	}

	// Search results.
	elseif ( is_search() ) {
		$classes[] = 'search';
	}

	// Error 404 pages.
	elseif ( is_404() ) {
		$classes[] = 'error-404';
	}

	// Paged views.
	if ( is_paged() ) {
		$classes[] = 'paged';
		$classes[] = 'paged-' . intval( get_query_var( 'paged' ) );

	// Singular post paged views using <!-- nextpage -->.
	} elseif ( is_singular() && 1 < get_query_var( 'page' ) ) {
		$classes[] = 'paged';
		$classes[] = 'paged-' . intval( get_query_var( 'page' ) );
	}

	// Is the current user logged in.
	$classes[] = is_user_logged_in() ? 'logged-in' : 'logged-out';

	// WP admin bar.
	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
	}

	// Use the '.custom-background' class to integrate with the WP background feature.
	if ( get_background_image() || get_background_color() ) {
		$classes[] = 'custom-background';
	}

	// Add the '.custom-header' class if the user is using a custom header.
	if ( get_header_image() || ( display_header_text() && get_header_textcolor() ) ) {
		$classes[] = 'custom-header';
	}

	// Add the `.custom-logo` class if user is using a custom logo.
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		$classes[] = 'wp-custom-logo';
	}

	// Add the `.wp-embed-responsive` class if the theme supports it.
	if ( current_theme_supports( 'responsive-embeds' ) ) {
		$classes[] = 'wp-embed-responsive';
	}

	// Add the '.display-header-text' class if the user chose to display it.
	if ( display_header_text() ) {
		$classes[] = 'display-header-text';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}

/**
 * Filters the WordPress post class with a better set of classes that are more
 * consistently handled and are backwards compatible with the original post
 * class functionality that existed prior to WordPress core adopting this feature.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $classes
 * @param  array  $class
 * @param  int    $post_id
 * @return array
 */
function post_class_filter( $classes, $class, $post_id ) {

	if ( is_admin() ) {
		return $classes;
	}

	$classes = [];
	$post    = get_post( $post_id );

	// Entry class.
	$classes[] = 'entry';

	// Post field classes.
	$classes[] = sprintf( 'entry--%s',      $post_id        );
	$classes[] = sprintf( 'entry--type-%s', get_post_type() );

	// Status class.
	$classes[] = sprintf( 'entry--status-%s', get_post_status() );

	// Author class.
	$classes[] = sprintf(
		'entry--author-%s',
		sanitize_html_class( get_the_author_meta( 'user_nicename' ), get_the_author_meta( 'ID' ) )
	);

	// Add post formt class.
	if ( post_type_supports( get_post_type(), 'post-formats' ) ) {

		$format = \get_post_format();

		$classes[] = sprintf(
			'entry--format-%s',
			$format && ! is_wp_error( $format ) ? $format : 'standard'
		);
	}

	// Add taxonomy term classes.  By default, no taxonomies (except for
	// post formats added above) are added.
	$taxonomies = apply_filters( 'hybrid/attr/post/class/taxonomy', [] );

	foreach ( (array) $taxonomies as $taxonomy ) {

		if ( is_object_in_taxonomy( get_post_type(), $taxonomy ) ) {

			$terms = get_the_terms( $post_id, $taxonomy );

			foreach ( (array) $terms as $term ) {

				$name = 'post_tag' === $taxonomy ? 'tag' : $taxonomy;
				$slug = sanitize_html_class( $term->slug, $term->term_id );

				$classes[] = sprintf( 'entry--%s-%s', $name, $slug );
			}
		}
	}

	// Sticky posts.
	if ( is_home() && ! is_paged() && is_sticky( $post_id ) ) {
		$classes[] = 'sticky';
	}

	// Password-protected posts.
	if ( post_password_required( $post_id ) ) {
		$classes[] = 'post-password-required';
	} elseif ( $post->post_password ) {
		$classes[] = 'post-password-protected';
	}

	// Post thumbnails.
	if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $post_id ) ) {
		$classes[] = 'has-post-thumbnail';
	}

	// Has excerpt.
	if ( post_type_supports( get_post_type(), 'excerpt' ) && has_excerpt() ) {
		$classes[] = 'has-excerpt';
	}

	// Has <!--more--> link.
	if ( ! is_singular() && false !== strpos( $post->post_content, '<!--more' ) ) {
		$classes[] = 'has-more-link';
	}

	// Has <!--nextpage--> links.
	if ( false !== strpos( $post->post_content, '<!--nextpage' ) ) {
		$classes[] = 'has-pages';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}

/**
 * Adds custom classes to the WordPress comment class.
 *
 * @since  5.0.0
 * @access public
 * @param  array        $classes
 * @param  string|array $class
 * @param  int          $comment_id
 * @global int          $comment_depth
 * @return array
 */
function comment_class_filter( $classes, $class, $comment_id, $post_id ) {
	global $comment_depth;

	if ( is_admin() ) {
		return $classes;
	}

	$comment = get_comment( $comment_id );
	$classes = [];

	// Base comment class.
	$classes[] = 'comment';

	// Comment type class.
	$classes[] = sprintf( 'comment--type-%s', $comment->comment_type ?: 'comment' );

	if ( in_array( $comment->comment_type, [ 'pingback', 'trackback'] ) ) {
		$classes[] = 'comment--type-ping';
	}

	// Status class. Note that status can be `null`.
	if ( $status = wp_get_comment_status( $comment_id ) ) {
		$classes[] = sprintf( 'comment--status-%s', $status );
	}

	// Depth class.
	$classes[] = sprintf( 'comment--depth-%s', $comment_depth ?: 1 );

	// Comment author classes.
	if ( 0 < $comment->user_id && $user = get_userdata( $comment->user_id ) ) {

		$classes[] = sprintf(
			'comment--author-%s',
			sanitize_html_class( $user->user_nicename, $comment->user_id )
		);

		// Add a class if the comment author is also the post author.
		$post = get_post( $post_id );

		if ( $comment->user_id == $post->post_author ) {
			$classes[] = 'bypostauthor';
		}
	}

	// Get comment types that are allowed to have an avatar.
	$avatar_types = apply_filters( 'get_avatar_comment_types', [ 'comment' ] );

	// If avatars are enabled and the comment types can display avatars, add the 'has-avatar' class.
	if ( get_option( 'show_avatars' ) && in_array( $comment->comment_type, $avatar_types ) ) {
		$classes[] = 'has-avatar';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}
