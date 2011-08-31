<?php
/**
 * Functions for making various theme elements context-aware.  Controls things such as the smart 
 * and logical body, post, and comment CSS classes as well as context-based action and filter hooks.  
 * The functions also integrate with WordPress' implementations of body_class, post_class, and 
 * comment_class, so your theme won't have any trouble with plugin integration.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/**
 * Hybrid's main contextual function.  This allows code to be used more than once without running 
 * hundreds of conditional checks within the theme.  It returns an array of contexts based on what 
 * page a visitor is currently viewing on the site.  This function is useful for making dynamic/contextual
 * classes, action and filter hooks, and handling the templating system.
 *
 * Note that time and date can be tricky because any of the conditionals may be true on time-/date-
 * based archives depending on several factors.  For example, one could load an archive for a specific
 * second during a specific minute within a specific hour on a specific day and so on.
 *
 * @since 0.7.0
 * @global $wp_query The current page's query object.
 * @global $hybrid The global Hybrid object.
 * @return array $hybrid->context Several contexts based on the current page.
 */
function hybrid_get_context() {
	global $hybrid;

	/* If $hybrid->context has been set, don't run through the conditionals again. Just return the variable. */
	if ( isset( $hybrid->context ) )
		return $hybrid->context;

	/* Set some variables for use within the function. */
	$hybrid->context = array();
	$object = get_queried_object();
	$object_id = get_queried_object_id();

	/* Front page of the site. */
	if ( is_front_page() )
		$hybrid->context[] = 'home';

	/* Blog page. */
	if ( is_home() ) {
		$hybrid->context[] = 'blog';
	}

	/* Singular views. */
	elseif ( is_singular() ) {
		$hybrid->context[] = 'singular';
		$hybrid->context[] = "singular-{$object->post_type}";
		$hybrid->context[] = "singular-{$object->post_type}-{$object_id}";
	}

	/* Archive views. */
	elseif ( is_archive() ) {
		$hybrid->context[] = 'archive';

		/* Taxonomy archives. */
		if ( is_tax() || is_category() || is_tag() ) {
			$hybrid->context[] = 'taxonomy';
			$hybrid->context[] = "taxonomy-{$object->taxonomy}";
			$hybrid->context[] = "taxonomy-{$object->taxonomy}-" . sanitize_html_class( $object->slug, $object->term_id );
		}

		/* Post type archives. */
		elseif ( is_post_type_archive() ) {
			$post_type = get_post_type_object( get_query_var( 'post_type' ) );
			$hybrid->context[] = "archive-{$post_type->name}";
		}

		/* User/author archives. */
		elseif ( is_author() ) {
			$hybrid->context[] = 'user';
			$hybrid->context[] = 'user-' . sanitize_html_class( get_the_author_meta( 'user_nicename', $object_id ), $object_id );
		}

		/* Time/Date archives. */
		else {
			if ( is_date() ) {
				$hybrid->context[] = 'date';
				if ( is_year() )
					$hybrid->context[] = 'year';
				if ( is_month() )
					$hybrid->context[] = 'month';
				if ( get_query_var( 'w' ) )
					$hybrid->context[] = 'week';
				if ( is_day() )
					$hybrid->context[] = 'day';
			}
			if ( is_time() ) {
				$hybrid->context[] = 'time';
				if ( get_query_var( 'hour' ) )
					$hybrid->context[] = 'hour';
				if ( get_query_var( 'minute' ) )
					$hybrid->context[] = 'minute';
			}
		}
	}

	/* Search results. */
	elseif ( is_search() ) {
		$hybrid->context[] = 'search';
	}

	/* Error 404 pages. */
	elseif ( is_404() ) {
		$hybrid->context[] = 'error-404';
	}

	return array_map( 'esc_attr', $hybrid->context );
}

/**
 * Creates a set of classes for each site entry upon display. Each entry is given the class of 
 * 'hentry'. Posts are given category, tag, and author classes. Alternate post classes of odd, 
 * even, and alt are added.
 *
 * @since 0.5.0
 * @global $post The current post's DB object.
 * @param string|array $class Additional classes for more control.
 * @return string $class
 */
function hybrid_entry_class( $class = '', $post_id = null ) {
	static $post_alt;

	$post = get_post( $post_id );

	/* Make sure we have a real post first. */
	if ( !empty( $post ) ) {

		$post_id = $post->ID;

		/* Add hentry for microformats compliance, the post type, and post status. */
		$classes = array( 'hentry', $post->post_type, $post->post_status );

		/* Post alt class. */
		$classes[] = 'post-' . ++$post_alt;
		$classes[] = ( $post_alt % 2 ) ? 'odd' : 'even alt';

		/* Author class. */
		$classes[] = 'author-' . sanitize_html_class( get_the_author_meta( 'user_nicename' ), get_the_author_meta( 'ID' ) );

		/* Sticky class (only on home/blog page). */
		if ( is_home() && is_sticky() && !is_paged() )
			$classes[] = 'sticky';

		/* Password-protected posts. */
		if ( post_password_required() )
			$classes[] = 'protected';

		/* Has excerpt. */
		if ( has_excerpt() )
			$classes[] = 'has-excerpt';

		/* Post format. */
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) {
			$post_format = get_post_format( $post_id );
			$classes[] = ( ( empty( $post_format ) || is_wp_error( $post_format ) ) ? 'format-standard' : "format-{$post_format}" );
		}

		/* Add category and post tag terms as classes. */
		if ( 'post' == $post->post_type ) {

			foreach ( array( 'category', 'post_tag' ) as $tax ) {

				foreach ( (array)get_the_terms( $post->ID, $tax ) as $term ) {
					if ( !empty( $term->slug ) )
						$classes[] = $tax . '-' . sanitize_html_class( $term->slug, $term->term_id );
				}
			}
		}
	}

	/* If not a post. */
	else {
		$classes = array( 'hentry', 'error' );
	}

	/* User-created classes. */
	if ( !empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	}

	/* Apply the filters for WP's 'post_class'. */
	$classes = apply_filters( 'post_class', $classes, $class, $post_id );

	/* Join all the classes into one string and echo them. */
	$class = join( ' ', $classes );

	echo apply_atomic( 'entry_class', $class );
}

/**
 * Sets a class for each comment. Sets alt, odd/even, and author/user classes. Adds author, user, 
 * and reader classes. Needs more work because WP, by default, assigns even/odd backwards 
 * (Odd should come first, even second).
 *
 * @since 0.2.0
 * @global $wpdb WordPress DB access object.
 * @global $comment The current comment's DB object.
 */
function hybrid_comment_class( $class = '' ) {
	global $post, $comment, $hybrid;

	/* Gets default WP comment classes. */
	$classes = get_comment_class( $class );

	/* Get the comment type. */
	$classes[] = get_comment_type();

	/* User classes to match user role and user. */
	if ( $comment->user_id > 0 ) {

		/* Create new user object. */
		$user = new WP_User( $comment->user_id );

		/* Set a class with the user's role. */
		if ( is_array( $user->roles ) ) {
			foreach ( $user->roles as $role )
				$classes[] = "role-{$role}";
		}

		/* Set a class with the user's name. */
		$classes[] = 'user-' . sanitize_html_class( $user->user_nicename, $user->ID );
	}

	/* If not a registered user */
	else {
		$classes[] = 'reader';
	}

	/* Comment by the entry/post author. */
	if ( $post = get_post( $post_id ) ) {
		if ( $comment->user_id === $post->post_author )
			$classes[] = 'entry-author';
	}

	/* Get comment types that are allowed to have an avatar. */
	$avatar_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );

	/* If avatars are enabled and the comment types can display avatars, add the 'has-avatar' class. */
	if ( get_option( 'show_avatars' ) && in_array( $comment->comment_type, $avatar_comment_types ) )
		$classes[] = 'has-avatar';

	/* Join all the classes into one string and echo them. */
	$class = join( ' ', $classes );

	echo apply_filters( "{$hybrid->prefix}_comment_class", $class );
}

/**
 * Provides classes for the <body> element depending on page context.
 *
 * @since 0.1.0
 * @uses $wp_query
 * @param string|array $class Additional classes for more control.
 * @return string
 */
function hybrid_body_class( $class = '' ) {
	global $wp_query;

	/* Text direction (which direction does the text flow). */
	$classes = array( 'wordpress', get_bloginfo( 'text_direction' ), get_locale() );

	/* Check if the current theme is a parent or child theme. */
	$classes[] = ( is_child_theme() ? 'child-theme' : 'parent-theme' );

	/* Multisite check adds the 'multisite' class and the blog ID. */
	if ( is_multisite() ) {
		$classes[] = 'multisite';
		$classes[] = 'blog-' . get_current_blog_id();
	}

	/* Date classes. */
	$time = time() + ( get_option( 'gmt_offset' ) * 3600 );
	$classes[] = strtolower( gmdate( '\yY \mm \dd \hH l', $time ) );

	/* Is the current user logged in. */
	$classes[] = ( is_user_logged_in() ) ? 'logged-in' : 'logged-out';

	/* WP admin bar. */
	if ( is_admin_bar_showing() )
		$classes[] = 'admin-bar';

	/* Merge base contextual classes with $classes. */
	$classes = array_merge( $classes, hybrid_get_context() );

	/* Singular post (post_type) classes. */
	if ( is_singular() ) {

		/* Get the queried post object. */
		$post = get_queried_object();

		/* Checks for custom template. */
		$template = str_replace( array ( "{$post->post_type}-template-", "{$post->post_type}-", '.php' ), '', get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true ) );
		if ( !empty( $template ) )
			$classes[] = "{$post->post_type}-template-{$template}";

		/* Post format. */
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) {
			$post_format = get_post_format( get_queried_object_id() );
			$classes[] = ( ( empty( $post_format ) || is_wp_error( $post_format ) ) ? "{$post->post_type}-format-standard" : "{$post->post_type}-format-{$post_format}" );
		}

		/* Attachment mime types. */
		if ( is_attachment() ) {
			foreach ( explode( '/', get_post_mime_type() ) as $type )
				$classes[] = "attachment-{$type}";
		}
	}

	/* Paged views. */
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 )
		$classes[] = 'paged paged-' . intval( $page );

	/* Input class. */
	if ( !empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	}

	/* Apply the filters for WP's 'body_class'. */
	$classes = apply_filters( 'body_class', $classes, $class );

	/* Join all the classes into one string. */
	$class = join( ' ', $classes );

	/* Print the body class. */
	echo apply_atomic( 'body_class', $class );
}

/**
 * Function for handling what the browser/search engine title should be. Attempts to handle every 
 * possible situation WordPress throws at it for the best optimization.
 *
 * @since 0.1.0
 * @global $wp_query
 */
function hybrid_document_title() {
	global $wp_query;

	/* Set up some default variables. */
	$domain = hybrid_get_textdomain();
	$doctitle = '';
	$separator = ':';

	/* If viewing the front page and posts page of the site. */
	if ( is_front_page() && is_home() )
		$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

	/* If viewing the posts page or a singular post. */
	elseif ( is_home() || is_singular() ) {

		$doctitle = get_post_meta( get_queried_object_id(), 'Title', true );

		if ( empty( $doctitle ) && is_front_page() )
			$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

		elseif ( empty( $doctitle ) )
			$doctitle = single_post_title( '', false );
	}

	/* If viewing any type of archive page. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_category() || is_tag() || is_tax() ) {
			$doctitle = single_term_title( '', false );
		}

		/* If viewing a post type archive. */
		elseif ( is_post_type_archive() ) {
			$post_type = get_post_type_object( get_query_var( 'post_type' ) );
			$doctitle = $post_type->labels->name;
		}

		/* If viewing an author/user archive. */
		elseif ( is_author() ) {
			$doctitle = get_user_meta( get_query_var( 'author' ), 'Title', true );

			if ( empty( $doctitle ) )
				$doctitle = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
		}

		/* If viewing a date-/time-based archive. */
		elseif ( is_date () ) {
			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'g:i a', $domain ) ) );

			elseif ( get_query_var( 'minute' ) )
				$doctitle = sprintf( __( 'Archive for minute %1$s', $domain ), get_the_time( __( 'i', $domain ) ) );

			elseif ( get_query_var( 'hour' ) )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'g a', $domain ) ) );

			elseif ( is_day() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'F jS, Y', $domain ) ) );

			elseif ( get_query_var( 'w' ) )
				$doctitle = sprintf( __( 'Archive for week %1$s of %2$s', $domain ), get_the_time( __( 'W', $domain ) ), get_the_time( __( 'Y', $domain ) ) );

			elseif ( is_month() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), single_month_title( ' ', false) );

			elseif ( is_year() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'Y', $domain ) ) );
		}

		/* For any other archives. */
		else {
			$doctitle = __( 'Archives', $domain );
		}
	}

	/* If viewing a search results page. */
	elseif ( is_search() )
		$doctitle = sprintf( __( 'Search results for &quot;%1$s&quot;', $domain ), esc_attr( get_search_query() ) );

	/* If viewing a 404 not found page. */
	elseif ( is_404() )
		$doctitle = __( '404 Not Found', $domain );

	/* If the current page is a paged page. */
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 )
		$doctitle = sprintf( __( '%1$s Page %2$s', $domain ), $doctitle . $separator, number_format_i18n( $page ) );

	/* Apply the wp_title filters so we're compatible with plugins. */
	$doctitle = apply_filters( 'wp_title', $doctitle, $separator, '' );

	/* Print the title to the screen. */
	echo apply_atomic( 'document_title', esc_attr( $doctitle ) );
}

?>