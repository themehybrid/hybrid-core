<?php
/**
 * Filterable content available throughout the theme. This file has many of the theme's
 * filter hooks, but filter hooks are not limited to this one file.
 *
 * Most filter hooks will use the apply_atomic() function, which creates contextual filter hooks.
 *
 * @link http://codex.wordpress.org/Function_Reference/add_filter
 * @link http://themehybrid.com/themes/hybrid/hooks/filters
 *
 * @package Hybrid
 * @subpackage Functions
 */

/**
 * Adds the correct DOCTYPE to the theme. Defaults to XHTML 1.0 Strict.
 * Child themes can overwrite this with the hybrid_doctype filter.
 *
 * @since 0.4
 */
function hybrid_doctype() {
	if ( !preg_match( "/MSIE 6.0/", esc_attr( $_SERVER['HTTP_USER_AGENT'] ) ) )
		$doctype = '<' . '?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . "\n";

	$doctype .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
	echo apply_atomic( 'doctype', $doctype );
}

/**
 * Shows the content type in the header.  Gets the site's defined HTML type 
 * and charset.  Can be overwritten with the hybrid_meta_content_type filter.
 *
 * @since 0.4
 */
function hybrid_meta_content_type() {
	$content_type = '<meta http-equiv="Content-Type" content="' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . '" />' . "\n";
	echo apply_atomic( 'meta_content_type', $content_type );
}

/**
 * Generates the relevant template info.  Adds template meta with theme version.  
 * Uses the theme name and version from style.css.  In 0.6, added the hybrid_meta_template 
 * filter hook.
 *
 * @since 0.4
 */
function hybrid_meta_template() {
	$data = get_theme_data( TEMPLATEPATH . '/style.css' );
	$template = '<meta name="template" content="' . esc_attr( "{$data['Title']} {$data['Version']}" ) . '" />' . "\n";
	echo apply_atomic( 'meta_template', $template );
}

/**
 * Sets the default meta robots setting.  If private, don't send meta info to the 
 * header.  Runs the hybrid_meta_robots filter hook at the end.
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
 * Generates the meta author.  On single posts and pages, use the author's name.
 * On the home page, use all authors.  The hybrid_meta_author filter added in 0.6.
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
 * Add the meta tag for copyright information to the header.  Single 
 * posts and pages should display the date written.  All other pages will 
 * show the current year.  The hybrid_meta_copyright filter added in 0.6.
 *
 * @since 0.4
 */
function hybrid_meta_copyright() {
	$domain = hybrid_get_textdomain();

	if ( is_singular() )
		$date = get_the_time( __( 'F Y', $domain ) );
	else
		$date = date( __( 'Y', $domain ) );

	$copyright = '<meta name="copyright" content="' . sprintf( esc_attr__( 'Copyright (c) %1$s', $domain ), $date ) . '" />' . "\n";
	echo apply_atomic( 'meta_copyright', $copyright );
}

/**
 * Add the revised meta tag on single posts and pages.  This shows the
 * last time the post/page was modified. The hybrid_meta_revised filter
 * added in 0.6.
 *
 * @since 0.4
 */
function hybrid_meta_revised() {
	$revised = '';

	if ( is_singular() )
		$revised = '<meta name="revised" content="' . get_the_modified_time( esc_attr__( 'l, F jS, Y, g:i a', hybrid_get_textdomain() ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_revised', $revised );
}

/**
 * Generates the meta description. Checks theme settings for indexing, title,
 * and meta settings. Customize this with the hybrid_meta_description filter.
 *
 * @since 0.2.3
 */
function hybrid_meta_description() {
	global $wp_query;

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
	}

	/* Format the meta description. */
	if ( !empty( $description ) )
		$description = '<meta name="description" content="' . str_replace( array( "\r", "\n", "\t" ), '', esc_attr( strip_tags( $description ) ) ) . '" />' . "\n";

	echo apply_atomic( 'meta_description', $description );
}

/**
 * Generates meta keywords/tags for the site.  Checks theme settings. 
 * Checks indexing settings.  Customize with the hybrid_meta_keywords filter.
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

/**
 * Checks for a user-uploaded favicon in the child theme's /images folder.  If it 
 * exists, display the <link> element for it.
 *
 * @since 0.4
 */
function hybrid_favicon() {
	$favicon = '';

	if ( file_exists( CHILD_THEME_DIR . '/images/favicon.ico' ) )
		$favicon =  '<link rel="shortcut icon" type="image/x-icon" href="' . CHILD_THEME_URI . '/images/favicon.ico" />' . "\n";
	echo apply_atomic( 'favicon', $favicon );
}

/**
 * Displays the pinkback URL.
 *
 * @since 0.4
 */
function hybrid_head_pingback() {
	$pingback = '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
	echo apply_atomic( 'head_pingback', $pingback );
}

/**
 * Dynamic element to wrap the site title in.  If it is the home or front page, wrap
 * it in an <h1> element.  One other pages, wrap it in a <div> element.  This may change
 * once the theme moves from XHTML to HTML 5 because HTML 5 allows for
 * multiple <h1> elements in a single document.
 *
 * @since 0.1
 */
function hybrid_site_title() {
	$tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';

	if ( $title = get_bloginfo( 'name' ) )
		$title = '<' . $tag . ' id="site-title"><a href="' . home_url() . '" title="' . $title . '" rel="home"><span>' . $title . '</span></a></' . $tag . '>';

	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the home or front page,
 * wrap it in an <h2> element.  One other pages, wrap it in a <div> element.  This may
 * change once the theme moves from XHTML to HTML 5 because HTML 5 has the 
 * <hgroup> element.
 *
 * @since 0.1
 */
function hybrid_site_description() {
	$tag = ( is_home() || is_front_page() ) ? 'h2' : 'div';

	if ( $desc = get_bloginfo( 'description' ) )
		$desc = "\n\t\t\t" . '<' . $tag . ' id="site-description"><span>' . $desc . '</span></' . $tag . '>' . "\n";

	echo apply_atomic( 'site_description', $desc );
}

/**
 * Displays the default entry title.  Wraps the title in the appropriate header tag. 
 * Use the hybrid_entry_title filter to customize.
 *
 * @since 0.5
 */
function hybrid_entry_title( $title = '' ) {
	if ( !$title )
		$title =  hybrid_entry_title_shortcode();

	echo apply_atomic_shortcode( 'entry_title', $title );
}

/**
 * Default entry byline for posts.  Shows the author, date, and edit link.  Use the 
 * hybrid_byline filter to customize.
 *
 * @since 0.5
 */
function hybrid_byline( $byline = '' ) {
	global $post;

	if ( $byline )
		$byline = '<p class="byline">' . $byline . '</p>';

	elseif ( 'post' == $post->post_type && 'link_category' !== get_query_var( 'taxonomy' ) )
		$byline = '<p class="byline">' . __( '<span class="byline-prep byline-prep-author">By</span> [entry-author] <span class="byline-prep byline-prep-published">on</span> [entry-published] [entry-edit-link before="| "]', hybrid_get_textdomain() ) . '</p>';

	echo apply_atomic_shortcode( 'byline', $byline );
}

/**
 * Displays the default entry metadata.  Shows the category, tag, and comments 
 * link.  Use the hybrid_entry_meta filter to customize.
 *
 * @since 0.5
 */
function hybrid_entry_meta( $metadata = '' ) {
	global $post;

	$domain = hybrid_get_textdomain();

	if ( $metadata )
		$metadata = '<p class="entry-meta">' . $metadata . '</p>';

	elseif ( 'post' == $post->post_type )
		$metadata = '<p class="entry-meta">[entry-terms taxonomy="category" before="' . __( 'Posted in', $domain ) . ' "] [entry-terms taxonomy="post_tag" before="| ' . __( 'Tagged', $domain ) . ' "] [entry-comments-link before="| "]</p>';

	elseif ( is_page() && current_user_can( 'edit_pages' ) )
		$metadata = '<p class="entry-meta">[entry-edit-link]</p>';

	echo apply_atomic_shortcode( 'entry_meta', $metadata, $post->ID );
}

/**
 * Default entry utility for posts.
 *
 * @since 0.9
 */
function hybrid_entry_utility( $utility = '' ) {

	if ( $utility )
		$utility = '<p class="entry-utility">' . $utility . '</p>';
	else
		$utility = '';

	echo apply_atomic_shortcode( 'entry_utility', $utility );
}

/**
 * Displays the page's profile URI.
 * @link http://microformats.org/wiki/profile-uris
 *
 * @since 0.6
 */
function hybrid_profile_uri() {
	echo apply_atomic( 'profile_uri', 'http://gmpg.org/xfn/11' );
}

/**
 * Filters main feed links for the site.  This changes the feed links  to the user's 
 * alternate feed URL.  This change only happens if the user chooses it from the 
 * theme settings.
 *
 * @since 0.4
 * @param string $output
 * @param string $feed
 * @return string $output
 */
function hybrid_feed_link( $output, $feed ) {

	$url = esc_url( hybrid_get_setting( 'feed_url' ) );

	if ( $url ) {
		$outputarray = array( 'rss' => $url, 'rss2' => $url, 'atom' => $url, 'rdf' => $url, 'comments_rss2' => '' );
		$outputarray[$feed] = $url;
		$output = $outputarray[$feed];
	}

	return $output;
}

/**
 * Filters the category, author, and tag feed links.  This changes all of these feed 
 * links to the user's alternate feed URL.  This change only happens if the user chooses 
 * it from the theme settings.
 *
 * @since 0.4
 * @param string $link
 * @return string $link
 */
function hybrid_other_feed_link( $link ) {

	if ( hybrid_get_setting( 'feeds_redirect' ) && $url = hybrid_get_setting( 'feed_url' ) )
		$link = esc_url( $url );

	return $link;
}

/**
 * Displays the breadcrumb trail.  Calls the get_the_breadcrumb() function.
 * Use the get_the_breadcrumb_args filter hook.  The hybrid_breadcrumb_args 
 * filter is deprecated.
 *
 * @deprecated 0.5 Theme still needs this function.
 * @todo Find an elegant way to transition to breadcrumb_trail() 
 * in child themes and filter breadcrumb_trail_args instead.
 *
 * @since 0.1
 */
function hybrid_breadcrumb() {
	if ( current_theme_supports( 'breadcrumb-trail' ) )
		breadcrumb_trail( array( 'front_page' => false, 'singular_post_taxonomy' => 'category' ) );
}

?>