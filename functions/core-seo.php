<?php
/**
 * SEO and header functions.  Not all things in this file are strictly for search engine optimization.  Many 
 * of the functions handle basic <meta> elements for the <head> area of the site.  This file is a catchall file 
 * for adding these types of things to themes.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Add <meta> elements to the <head> area. */
add_action( 'wp_head', 'hybrid_meta_robots', 1 );
add_action( 'wp_head', 'hybrid_meta_author', 1 );
add_action( 'wp_head', 'hybrid_meta_copyright', 1 );
add_action( 'wp_head', 'hybrid_meta_revised', 1 );
add_action( 'wp_head', 'hybrid_meta_description', 1 );
add_action( 'wp_head', 'hybrid_meta_keywords', 1 );

/**
 * Sets the default meta robots setting.  If private, don't send meta info to the header.  Runs the 
 * hybrid_meta_robots filter hook at the end.
 *
 * @since 0.2.3
 */
function hybrid_meta_robots() {

	/* If the blog is set to private, don't show anything. */
	if ( !get_option( 'blog_public' ) )
		return;

	/* Create the HTML for the robots meta tag. */
	$robots = '<meta name="robots" content="index,follow" />' . "\n";

	echo apply_atomic( 'meta_robots', $robots );
}

/**
 * Generates the meta author.  On single posts and pages, use the author's name.  On the home page, use 
 * all authors.  The hybrid_meta_author filter added in 0.6.
 *
 * @since 0.3.3
 */
function hybrid_meta_author() {
	global $wp_query;

	/* Set an empty $author variable. */
	$author = '';

	/* If viewing a singular post, get the post author's display name. */
	if ( is_singular() )
		$author = get_the_author_meta( 'display_name', $wp_query->post->post_author );

	/* If viewing a user/author archive, get the user's display name. */
	elseif ( is_author() )
		$author = get_the_author_meta( 'display_name', $wp_query->get_queried_object_id() );

	/* If an author was found, wrap it in the proper HTML and escape the author name. */
	if ( !empty( $author ) )
		$author = '<meta name="author" content="' . esc_attr( $author ) . '" />' . "\n";

	echo apply_atomic( 'meta_author', $author );
}

/**
 * Add the meta tag for copyright information to the header.  Single posts and pages should display the 
 * date written.  All other pages will show the current year. 
 *
 * @since 0.4.0
 */
function hybrid_meta_copyright() {

	/* Get the theme's textdomain. */
	$domain = hybrid_get_textdomain();

	/* If viewing a singular post, get the post month and year. */
	if ( is_singular() )
		$date = get_the_time( esc_attr__( 'F Y', $domain ) );

	/* For all other views, get the current year. */
	else
		$date = date( esc_attr__( 'Y', $domain ) );

	/* Create the HTML for the copyright meta tag. */
	$copyright = '<meta name="copyright" content="' . sprintf( esc_attr__( 'Copyright (c) %1$s', $domain ), $date ) . '" />' . "\n";

	echo apply_atomic( 'meta_copyright', $copyright );
}

/**
 * Add the revised meta tag on single posts and pages (or any post type).  This shows the last time the post 
 * was modified. 
 *
 * @since 0.4.0
 */
function hybrid_meta_revised() {

	/* Create an empty $revised variable. */
	$revised = '';

	/* If viewing a singular post, get the last modified date/time to use in the revised meta tag. */
	if ( is_singular() )
		$revised = '<meta name="revised" content="' . get_the_modified_time( esc_attr__( 'l, F jS, Y, g:i a', hybrid_get_textdomain() ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_revised', $revised );
}

/**
 * Generates the meta description. Checks theme settings for indexing, title, and meta settings. Customize 
 * this with the hybrid_meta_description filter.
 *
 * @since 0.2.3
 */
function hybrid_meta_description() {
	global $wp_query;

	/* Set an empty $description variable. */
	$description = '';

	/* If viewing the home/posts page, get the site's description. */
	if ( is_home() ) {
		$description = get_bloginfo( 'description' );
	}

	/* If viewing a singular post. */
	elseif ( is_singular() ) {

		/* Get the meta value for the 'Description' meta key. */
		$description = get_post_meta( $wp_query->post->ID, 'Description', true );

		/* If no description was found and viewing the site's front page, use the site's description. */
		if ( empty( $description ) && is_front_page() )
			$description = get_bloginfo( 'description' );

		/* For all other singular views, get the post excerpt. */
		elseif ( empty( $description ) )
			$description = get_post_field( 'post_excerpt', $wp_query->post->ID );
	}

	/* If viewing an archive page. */
	elseif ( is_archive() ) {

		/* If viewing a user/author archive. */
		if ( is_author() ) {

			/* Get the meta value for the 'Description' user meta key. */
			$description = get_user_meta( get_query_var( 'author' ), 'Description', true );

			/* If no description was found, get the user's description (biographical info). */
			if ( empty( $description ) )
				$description = get_the_author_meta( 'description', get_query_var( 'author' ) );
		}

		/* If viewing a taxonomy term archive, get the term's description. */
		elseif ( is_category() || is_tag() || is_tax() )
			$description = term_description( '', get_query_var( 'taxonomy' ) );

		/* If viewing a custom post type archive. */
		elseif ( is_post_type_archive() ) {

			/* Get the post type object. */
			$post_type = get_post_type_object( get_query_var( 'post_type' ) );

			/* If a description was set for the post type, use it. */
			if ( isset( $post_type->description ) )
				$description = $post_type->description;
		}
	}

	/* Format the meta description. */
	if ( !empty( $description ) )
		$description = '<meta name="description" content="' . str_replace( array( "\r", "\n", "\t" ), '', esc_attr( strip_tags( $description ) ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_description', $description );
}

/**
 * Generates meta keywords/tags for the site.
 *
 * @since 0.2.3
 */
function hybrid_meta_keywords() {
	global $wp_query;

	/* Set an empty $keywords variable. */
	$keywords = '';

	/* If on a singular post and not a preview. */
	if ( is_singular() && !is_preview() ) {

		/* Get the meta value for the 'Keywords' meta key. */
		$keywords = get_post_meta( $wp_query->post->ID, 'Keywords', true );

		/* If no keywords were found. */
		if ( empty( $keywords ) ) {

			/* Get all taxonomies for the current post type. */
			$taxonomies = get_object_taxonomies( $wp_query->post->post_type );

			/* If taxonomies wer found for the post type. */
			if ( is_array( $taxonomies ) ) {

				/* Loop through the taxonomies, getting the terms for the current post. */
				foreach ( $taxonomies as $tax ) {

					if ( $terms = get_the_term_list( $wp_query->post->ID, $tax, '', ', ', '' ) )
						$keywords[] = $terms;
				}

				/* If keywords were found, join the array into a comma-separated string. */
				if ( !empty( $keywords ) )
					$keywords = join( ', ', $keywords );
			}
		}
	}

	/* If on a user/author archive page, check for user meta. */
	elseif ( is_author() ) {

		/* Get the meta value for the 'Keywords' user meta key. */
		$keywords = get_user_meta( get_query_var( 'author' ), 'Keywords', true );
	}

	/* If we have keywords, format for output. */
	if ( !empty( $keywords ) )
		$keywords = '<meta name="keywords" content="' . esc_attr( strip_tags( $keywords ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_keywords', $keywords );
}

?>