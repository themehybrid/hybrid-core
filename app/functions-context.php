<?php
/**
 * Contextual functions.
 *
 * Contextual functions and filters, particularly dealing with the body, post,
 * and comment classes.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

use WP_User;

# Filters the WordPress element classes.
add_filter( 'body_class',    __NAMESPACE__ . '\body_class_filter',    ~PHP_INT_MAX, 2 );
add_filter( 'post_class',    __NAMESPACE__ . '\post_class_filter',    ~PHP_INT_MAX, 3 );
add_filter( 'comment_class', __NAMESPACE__ . '\comment_class_filter', ~PHP_INT_MAX, 4 );

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
	$lang   = get_language( $locale );

	if ( $locale !== $lang ) {
		$classes[] = $lang;
	}

	$classes[] = strtolower( str_replace( '_', '-', $locale ) );

	// Check if the current theme is a parent or child theme.
	$classes[] = is_child_theme() ? 'child-theme' : 'parent-theme';

	// Multisite check adds the 'multisite' class and the blog ID.
	if ( is_multisite() ) {
		$classes[] = 'multisite';
		$classes[] = 'blog-' . get_current_blog_id();
	}
	// Plural/multiple-post view (opposite of singular).
	if ( is_plural() ) {
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
			[ "{$post_type}-template-", "{$post_type}-" ],
			'',
			basename( get_post_template( $post_id ), '.php' )
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

			$slug = 'post_format' === $taxonomy ? clean_post_format_slug( $term->slug ) : $term->slug;

			// Checks for custom template.
			$template = str_replace(
				[ "{$taxonomy}-template-", "{$taxonomy}-" ],
				'',
				basename( get_term_template( $term_id ), '.php' )
			);

			$classes[] = 'taxonomy';
			$classes[] = "taxonomy-{$taxonomy}";
			$classes[] = "taxonomy-{$taxonomy}-" . sanitize_html_class( $slug, $term_id );
			$classes[] = $template ? "{$taxonomy}-template-{$template}" : "{$taxonomy}-template-default";
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

	// Add the '.display-header-text' class if the user chose to display it.
	if ( display_header_text() ) {
		$classes[] = 'display-header-text';
	}

	// Theme layouts.
	if ( current_theme_supports( 'theme-layouts' ) ) {
		$classes[] = sanitize_html_class( 'layout-' . get_theme_layout() );
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
	$taxonomies = apply_filters( app()->namespace . '/post_class_taxonomy', [] );

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
		$classes[] = 'comment--ping';
	}

	// Depth class.
	$classes[] = sprintf( 'comment--depth-%s', $comment_depth ?: 1 );

	// Comment author classes.
	if ( 0 < $comment->user_id && $user = get_userdata( $comment->user_id ) ) {

		$classes[] = sprintf(
			'comment--author-%s',
			sanitize_html_class( $user->user_nicename, $comment->user_id )
		);

		// Set a class with the user's role(s).
		if ( is_array( $user->roles ) ) {

			foreach ( $user->roles as $role ) {
				$classes[] = sprintf( 'comment--role-%s', $role );
			}
		}

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
