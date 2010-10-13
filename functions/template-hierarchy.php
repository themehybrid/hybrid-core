<?php
/**
 * Functions for loading the correct template in the Hybrid system. Many of the default
 * WordPress templates are overridden to allow for a better template hierarchy, which
 * allows for more customizations and better structure.
 *
 * Other functions in this file are for template-specific outputs, such as the page menu.
 *
 * @package HybridCore
 * @subpackage Functions
 */

/* Filter the date template. */
add_filter( 'date_template', 'hybrid_date_template' );

/* Filter the author/user template. */
add_filter( 'author_template', 'hybrid_user_template' );

/* Filter the tag and category (taxonomy) templates. */
add_filter( 'tag_template', 'hybrid_taxonomy_template' );
add_filter( 'category_template', 'hybrid_taxonomy_template' );

/* Filter the single, page, and attachment (singular) templates. */
add_filter( 'single_template', 'hybrid_singular_template' );
add_filter( 'page_template', 'hybrid_singular_template' );
add_filter( 'attachment_template', 'hybrid_singular_template' );

/**
 * Overrides WP's default template for date-based archives. Better abstraction of 
 * templates than is_date() allows by checking for the year, month, week, day, hour,
 * and minute.
 *
 * @since 0.6
 * @uses locate_template() Checks for template in child and parent theme.
 * @param string $template
 * @return string $template Full path to file.
 */
function hybrid_date_template( $template ) {
	$templates = array();

	if ( is_time() ) {
		if ( get_query_var( 'minute' ) )
			$templates[] = 'minute.php';
		elseif ( get_query_var( 'hour' ) )
			$templates[] = 'hour.php';
		$templates[] = 'time.php';
	}
	elseif ( is_day() )
		$templates[] = 'day.php';
	elseif ( get_query_var( 'w' ) )
		$templates[] = 'week.php';
	elseif ( is_month() )
		$templates[] = 'month.php';
	elseif ( is_year() )
		$templates[] = 'year.php';

	$templates[] = 'date.php';
	$templates[] = 'archive.php';

	return locate_template( $templates );
}

/**
 * Overrides WP's default template for author-based archives. Better abstraction 
 * of templates than is_author() allows by allowing themes to specify templates for 
 * a specific author. The hierarchy is author-$author.php, author.php.
 *
 * @since 0.7
 * @uses locate_template() Checks for template in child and parent theme.
 * @param string $template
 * @return string Full path to file.
 */
function hybrid_user_template( $template ) {
	$templates = array();
	$name = get_the_author_meta( 'user_nicename', get_query_var( 'author' ) );
	$user = new WP_User( absint( get_query_var( 'author' ) ) );

	$templates = array( "user-{$name}.php", "author-{$name}.php" );

	if ( is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			$templates[] = "user-role-{$role}.php";
	}
	$templates[] = 'user.php';
	$templates[] = 'author.php';
	$templates[] = 'archive.php';

	return locate_template( $templates );
}

/**
 * Overrides WP's default template for category- and tag-based archives. This allows 
 * better organization of taxonomy template files by making categories and post tags 
 * work the same way as other taxonomies. The hierarchy is taxonomy-$taxonomy-$term.php,
 * taxonomy-$taxonomy.php, taxonomy.php, archive.php.
 *
 * @since 0.7
 * @uses locate_template() Checks for template in child and parent theme.
 * @param string $template
 * @return string Full path to file.
 */
function hybrid_taxonomy_template( $template ) {
	global $wp_query;
	$term = $wp_query->get_queried_object();
	return locate_template( array( "taxonomy-{$term->taxonomy}-{$term->slug}.php", "taxonomy-{$term->taxonomy}.php", 'taxonomy.php', 'archive.php' ) );
}

/**
 * Overrides the default single (singular post) template.  Post templates can be
 * loaded using a custom post template, by slug, or by ID.
 *
 * Attachment templates are handled slightly differently. Rather than look for the slug
 * or ID, templates can be loaded by attachment-$mime[0]_$mime[1].php, 
 * attachment-$mime[1].php, or attachment-$mime[0].php.
 *
 * @since 0.7
 * @param string $template The default WordPress post template.
 * @return string $template The theme post template after all templates have been checked for.
 */
function hybrid_singular_template( $template ) {
	global $wp_query;

	$templates = array();

	/* Check for a custom post template by custom field key '_wp_post_template'. */
	$custom = get_post_meta( $wp_query->post->ID, "_wp_{$wp_query->post->post_type}_template", true );
	if ( $custom )
		$templates[] = $custom;

	if ( is_attachment() ) {
		/* Split the mime_type into two distinct parts. */
		$type = explode( '/', get_post_mime_type() );

		$templates[] = "attachment-{$type[0]}_{$type[1]}.php";
		$templates[] = "attachment-{$type[1]}.php";
		$templates[] = "attachment-{$type[0]}.php";
	}
	else {
		$templates[] = "{$wp_query->post->post_type}-{$wp_query->post->post_name}.php";
		$templates[] = "{$wp_query->post->post_type}-{$wp_query->post->ID}.php";
	}

	$templates[] = "{$wp_query->post->post_type}.php";
	$templates[] = "singular.php";

	return locate_template( $templates );
}

?>