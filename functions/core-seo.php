<?php
/**
 * SEO and header functions.  Not all things in this file are strictly for search engine optimization.  Many 
 * of the functions handle basic <meta> elements for the <head> area of the site.  This file is a catchall file 
 * for adding these types of things to themes.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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
 * @access private
 * @return void
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
 * Generates the meta author.  For singular posts, it uses the post author's display name.  For user/author 
 * archives, it uses the user's display name.
 *
 * @since 0.3.3
 * @access private
 * @return void
 */
function hybrid_meta_author() {

	/* Set an empty $author variable. */
	$author = '';

	/* Get the queried object. */
	$object = get_queried_object();

	/* If viewing a singular post, get the post author's display name. */
	if ( is_singular() )
		$author = get_the_author_meta( 'display_name', $object->post_author );

	/* If viewing a user/author archive, get the user's display name. */
	elseif ( is_author() )
		$author = get_the_author_meta( 'display_name', get_queried_object_id() );

	/* If an author was found, wrap it in the proper HTML and escape the author name. */
	if ( !empty( $author ) )
		$author = '<meta name="author" content="' . esc_attr( $author ) . '" />' . "\n";

	echo apply_atomic( 'meta_author', $author );
}

/**
 * Add the meta tag for copyright information to the header.  Singular posts display the date the post was 
 * published.  All other pages will show the current year. 
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function hybrid_meta_copyright() {

	/* If viewing a singular post, get the post month and year. */
	if ( is_singular() )
		$date = get_the_time( esc_attr__( 'F Y', 'hybrid-core' ) );

	/* For all other views, get the current year. */
	else
		$date = date( esc_attr__( 'Y', 'hybrid-core' ) );

	/* Create the HTML for the copyright meta tag. */
	$copyright = '<meta name="copyright" content="' . sprintf( esc_attr__( 'Copyright (c) %1$s', 'hybrid-core' ), $date ) . '" />' . "\n";

	echo apply_atomic( 'meta_copyright', $copyright );
}

/**
 * Add the revised meta tag on the singular view of posts.  This shows the last time the post was modified. 
 *
 * @since 0.4.0
 * @access private
 * @return void
 */
function hybrid_meta_revised() {

	/* Create an empty $revised variable. */
	$revised = '';

	/* If viewing a singular post, get the last modified date/time to use in the revised meta tag. */
	if ( is_singular() )
		$revised = '<meta name="revised" content="' . get_the_modified_time( esc_attr__( 'l, F jS, Y, g:i a', 'hybrid-core' ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_revised', $revised );
}

/**
 * Generates the meta description based on either metadata or the description for the object.
 *
 * @since 0.2.3
 * @access private
 * @return void
 */
function hybrid_meta_description() {

	/* Set an empty $description variable. */
	$description = '';

	/* If viewing the home/posts page, get the site's description. */
	if ( is_home() ) {
		$description = get_bloginfo( 'description' );
	}

	/* If viewing a singular post. */
	elseif ( is_singular() ) {

		/* Get the meta value for the 'Description' meta key. */
		$description = get_post_meta( get_queried_object_id(), 'Description', true );

		/* If no description was found and viewing the site's front page, use the site's description. */
		if ( empty( $description ) && is_front_page() )
			$description = get_bloginfo( 'description' );

		/* For all other singular views, get the post excerpt. */
		elseif ( empty( $description ) )
			$description = get_post_field( 'post_excerpt', get_queried_object_id() );
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
 * @access private
 * @return void
 */
function hybrid_meta_keywords() {

	/* Set an empty $keywords variable. */
	$keywords = '';

	/* If on a singular post and not a preview. */
	if ( is_singular() && !is_preview() ) {

		/* Get the queried post. */
		$post = get_queried_object();

		/* Get the meta value for the 'Keywords' meta key. */
		$keywords = get_post_meta( get_queried_object_id(), 'Keywords', true );

		/* If no keywords were found. */
		if ( empty( $keywords ) ) {

			/* Get all taxonomies for the current post type. */
			$taxonomies = get_object_taxonomies( $post->post_type );

			/* If taxonomies were found for the post type. */
			if ( is_array( $taxonomies ) ) {

				/* Loop through the taxonomies, getting the terms for the current post. */
				foreach ( $taxonomies as $tax ) {

					if ( $terms = get_the_term_list( get_queried_object_id(), $tax, '', ', ', '' ) )
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