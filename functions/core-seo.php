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
	if ( !get_option( 'blog_public' ) )
		return;

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

	$author = '';

	if ( is_singular() )
		$author = get_the_author_meta( 'display_name', $wp_query->post->post_author );

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
	$domain = hybrid_get_textdomain();

	if ( is_singular() )
		$date = get_the_time( esc_attr__( 'F Y', $domain ) );
	else
		$date = date( esc_attr__( 'Y', $domain ) );

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
	$revised = '';

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

	$description = '';

	if ( is_home() ) {
		$description = get_bloginfo( 'description' );
	}

	elseif ( is_singular() ) {
		$description = get_metadata( 'post', $wp_query->post->ID, 'Description', true );

		if ( empty( $description ) && is_front_page() )
			$description = get_bloginfo( 'description' );

		elseif ( empty( $description ) )
			$description = get_post_field( 'post_excerpt', $wp_query->post->ID );
	}

	elseif ( is_archive() ) {

		if ( is_author() )
			$description = get_the_author_meta( 'description', get_query_var( 'author' ) );

		elseif ( is_category() || is_tag() || is_tax() )
			$description = term_description( '', get_query_var( 'taxonomy' ) );

		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {
			$post_type = get_post_type_object( get_query_var( 'post_type' ) );
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

	$keywords = '';

	/* If on a single post, check for custom field key Keywords and taxonomies. */
	if ( is_singular() && !is_preview() ) {
		$keywords = get_post_meta( $wp_query->post->ID, 'Keywords', true );

		if ( empty( $keywords ) ) {
			$taxonomies = get_object_taxonomies( $wp_query->post->post_type );

			if ( is_array( $taxonomies ) ) {
				foreach ( $taxonomies as $tax ) {
					if ( $terms = get_the_term_list( $wp_query->post->ID, $tax, '', ', ', '' ) )
						$keywords[] = $terms;
				}
			}

			if ( !empty( $keywords ) )
				$keywords = join( ', ', $keywords );
		}
	}

	/* If we have keywords, join them together into one string and format for output. */
	if ( !empty( $keywords ) )
		$keywords = '<meta name="keywords" content="' . esc_attr( strip_tags( $keywords ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_keywords', $keywords );
}

?>