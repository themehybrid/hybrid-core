<?php
/**
 * Sets up some default actions for the Hybrid parent theme.  While theme authors could certainly load
 * this file and use the Hybrid theme defaults, it's not recommended.  You'll probably find yourself 
 * overwriting the defaults more often than not.  Generally, any parent theme would add its own actions to 
 * its functions.php file, but hey, it's my framework.  So, I'm breaking the rules.  But, this file would be a 
 * good guide to follow when setting up your own functions.php file.
 *
 * @package Hybrid
 * @subpackage Functions
 */

require_once( HYBRID_LEGACY . '/hooks-actions.php' );

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'hybrid_setup_theme' );

/**
 * Function for setting up all the Hybrid parent theme default actions and supported features.  This structure 
 * should be followed when creating custom parent themes with the Hybrid Core framework.
 *
 * @since 0.9
 */
function hybrid_setup_theme() {

	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	/* Add support for the core sidebars. */
	add_theme_support( 'hybrid-core-sidebars' );

	/* Add support for the core widgets. */
	add_theme_support( 'hybrid-core-widgets' );

	/* Add support for the core shortcodes. */
	add_theme_support( 'hybrid-core-shortcodes' );

	/* Add support for the core menus. */
	if ( hybrid_get_setting( 'use_menus' ) )
		add_theme_support( 'hybrid-core-menus' );

	/* Add support for the core post meta box. */
	add_theme_support( 'hybrid-core-post-meta-box' );

	/* Add support for the core SEO feature. */
	if ( !hybrid_get_setting( 'seo_plugin' ) )
		add_theme_support( 'hybrid-core-seo' );

	/* Add support for the core drop-downs script. */
	if ( hybrid_get_setting( 'superfish_js' ) )
		add_theme_support( 'hybrid-core-drop-downs' );

	/* Add support for the core print stylesheet. */
	if ( hybrid_get_setting( 'print_style' ) )
		add_theme_support( 'hybrid-core-print-style' );

	/* Add support for core theme settings meta boxes. */
	add_theme_support( 'hybrid-core-meta-box-general' );
	add_theme_support( 'hybrid-core-meta-box-footer' );

	/* Add support for the breadcrumb trail extension. */
	add_theme_support( 'breadcrumb-trail' );

	/* Add support for the custom field series extension. */
	add_theme_support( 'custom-field-series' );

	/* Add support for the Get the Image extension. */
	add_theme_support( 'get-the-image' );

	/* Add support for the Post Stylesheets extension. */
	add_theme_support( 'post-stylesheets' );

	/* If no child theme is active, add support for the Post Layouts and Pagination extensions. */
	if ( 'hybrid' == get_stylesheet() ) {
		add_theme_support( 'post-layouts' );
		add_theme_support( 'loop-pagination' );
	}

	/* Header actions. */
	add_action( "{$prefix}_header", 'hybrid_site_title' );
	add_action( "{$prefix}_header", 'hybrid_site_description' );

	/* Load the correct menu. */
	if ( hybrid_get_setting( 'use_menus' ) )
		add_action( "{$prefix}_after_header", 'hybrid_get_primary_menu' );
	else
		add_action( "{$prefix}_after_header", 'hybrid_page_nav' );

	/* Add the primary and secondary sidebars after the container. */
	add_action( "{$prefix}_after_container", 'hybrid_get_primary' );
	add_action( "{$prefix}_after_container", 'hybrid_get_secondary' );

	/* Add the breadcrumb trail and before content sidebar before the content. */
	add_action( "{$prefix}_before_content", 'hybrid_breadcrumb' );
	add_action( "{$prefix}_before_content", 'hybrid_get_utility_before_content' );

	/* Add the title, byline, and entry meta before and after the entry. */
	add_action( "{$prefix}_before_entry", 'hybrid_entry_title' );
	add_action( "{$prefix}_before_entry", 'hybrid_byline' );
	add_action( "{$prefix}_after_entry", 'hybrid_entry_meta' );

	/* Add the after singular sidebar and custom field series extension after singular views. */
	add_action( "{$prefix}_after_singular", 'hybrid_get_utility_after_singular' );
	add_action( "{$prefix}_after_singular", 'custom_field_series' );

	/* Add the after content sidebar and navigation links after the content. */
	add_action( "{$prefix}_after_content", 'hybrid_get_utility_after_content' );
	add_action( "{$prefix}_after_content", 'hybrid_navigation_links' );

	/* Add the subsidiary sidebar and footer insert to the footer. */
	add_action( "{$prefix}_before_footer", 'hybrid_get_subsidiary' );
	add_action( "{$prefix}_footer", 'hybrid_footer_insert' );

	/* Add the comment avatar and comment meta before individual comments. */
	add_action( "{$prefix}_before_comment", 'hybrid_avatar' );
	add_action( "{$prefix}_before_comment", 'hybrid_comment_meta' );

	/* Add Hybrid theme-specific body classes. */
	add_filter( 'body_class', 'hybrid_theme_body_class' );

	/* Add elements to the <head> area. */
	add_action( "{$prefix}_head", 'hybrid_meta_content_type' );
	add_action( 'wp_head', 'hybrid_favicon' );

	/* Feed links. */
	add_filter( 'feed_link', 'hybrid_feed_link', 1, 2 );
	add_filter( 'category_feed_link', 'hybrid_other_feed_link' );
	add_filter( 'author_feed_link', 'hybrid_other_feed_link' );
	add_filter( 'tag_feed_link', 'hybrid_other_feed_link' );
	add_filter( 'search_feed_link', 'hybrid_other_feed_link' );

	/* Remove WP and plugin functions. */
	add_action( 'wp_print_styles', 'hybrid_disable_styles' );
}

/**
 * Function for adding Hybrid theme <body> classes.
 *
 * @since 0.9
 */
function hybrid_theme_body_class( $classes ) {
	global $wp_query, $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;

	/* Singular post classes (deprecated). */
	if ( is_singular() ) {

		if ( is_page() )
			$classes[] = "page-{$wp_query->post->ID}"; // Use singular-page-ID

		elseif ( is_singular( 'post' ) )
			$classes[] = "single-{$wp_query->post->ID}"; // Use singular-post-ID
	}
	elseif ( is_tax() || is_category() || is_tag() ) {
		$term = $wp_query->get_queried_object();
		$classes[] = "taxonomy-{$term->taxonomy}";
		$classes[] = "taxonomy-{$term->taxonomy}-" . sanitize_html_class( $term->slug, $term->term_id );
	}

	/* Browser detection. */
	$browsers = array( 'gecko' => $is_gecko, 'opera' => $is_opera, 'lynx' => $is_lynx, 'ns4' => $is_NS4, 'safari' => $is_safari, 'chrome' => $is_chrome, 'msie' => $is_IE );
	foreach ( $browsers as $key => $value ) {
		if ( $value ) {
			$classes[] = $key;
			break;
		}
	}

	/* Hybrid theme widgets detection. */
	foreach ( array( 'primary', 'secondary', 'subsidiary' ) as $sidebar )
		$classes[] = ( is_active_sidebar( $sidebar ) ) ? "{$sidebar}-active" : "{$sidebar}-inactive";

	if ( in_array( 'primary-inactive', $classes ) && in_array( 'secondary-inactive', $classes ) && in_array( 'subsidiary-inactive', $classes ) )
		$classes[] = 'no-widgets';

	return $classes;
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
 * Disables stylesheets for particular plugins to allow the theme to easily write its own
 * styles for the plugins' features.
 *
 * @since 0.7
 * @link http://wordpress.org/extend/plugins/wp-pagenavi
 */
function hybrid_disable_styles() {
	/* Deregister the WP PageNavi plugin style. */
	wp_deregister_style( 'wp-pagenavi' );
}

?>