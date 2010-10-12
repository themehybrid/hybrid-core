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
 * Displays the page's profile URI.
 * @link http://microformats.org/wiki/profile-uris
 *
 * @since 0.6
 */
function hybrid_profile_uri() {
	echo apply_atomic( 'profile_uri', 'http://gmpg.org/xfn/11' );
}

?>