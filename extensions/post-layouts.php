<?php
/**
 * Post Layouts - A WordPress script for creating post-specific layouts.
 *
 * Post Layouts was created to allow theme developers to easily style themes with post-specific layout 
 * structures.  It gives users the ability to control how each post (or any post type) is displayed on the 
 * front end of the site.  This script is called "post layouts," but developers aren't limited to only creating 
 * layouts for specific posts.  The layout can be filtered for any page of a WordPress site.  
 *
 * The script will filter the WordPress body_class to provide a layout class for the given page.  Themes 
 * must support this hook or its accompanying body_class() function for the Post Layouts script to work. 
 * Themes must also handle the CSS based on the layout class.  This script merely provides the logic.  The 
 * design should be handled on a theme-by-theme basis.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostLayouts
 * @version 0.1.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filters the body_class hook to add a custom class. */
add_filter( 'body_class', 'post_layouts_body_class' );

/**
 * Gets the layout for the current post based off the 'Layout' custom field key if viewing a singular post 
 * entry.  All other pages are given a default layout of 'layout-default'.
 *
 * @since 0.1.0
 * @uses is_singular() Checks if viewing a singular post.
 * @link http://codex.wordpress.org/Function_Reference/is_singular
 * @uses get_post_meta() Gets the post layout metadata for the given post.
 * @link http://codex.wordpress.org/Function_Reference/get_post_meta
 * @return string The layout for the given page.
 */
function post_layouts_get_layout() {
	global $wp_query;

	/* Set the layout to an empty string. */
	$layout = '';

	/* If viewing a singular post, check if a layout has been specified. */
	if ( is_singular() ) {

		/* Get the current post ID. */
		$post_id = $wp_query->get_queried_object_id();

		/* Allow plugin/theme developers to override the default meta key used. */
		$meta_key = apply_filters( 'post_layouts_meta_key', 'Layout' );

		/* Check the post metadata for the layout. */
		$post_layout = get_post_meta( $post_id, $meta_key, true );

		/* If a layout was found, assign it to the $layout variable. */
		if ( !empty( $post_layout ) )
			$layout = esc_attr( $post_layout );
	}

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( empty( $layout ) || !in_array( $layout, post_layouts_available() ) )
		$layout = 'default';

	/* Return the layout and allow plugin/theme developers to override it. */
	return apply_filters( 'get_post_layout', "layout-{$layout}" );
}

/**
 * Adds the post layout class to the WordPress body class in the form of "layout-$layout".  This allows 
 * theme developers to design their theme layouts based on the layout class.  If designing a theme with 
 * this extension, the theme should make sure to handle all possible layout classes.
 *
 * @since 0.1.0
 * @uses post_layouts_get_layout() Gets the layout of the current page.
 * @param array $classes
 * @param array $classes
 */
function post_layouts_body_class( $classes ) {

	/* Adds the layout to array of body classes. */
	$classes[] = post_layouts_get_layout();

	/* Return the $classes array. */
	return $classes;
}

/**
 * Provides a list of available layouts.  Theme developers may overwrite this to create unique layouts or 
 * remove some of the default layouts.  When filtering, always return an array.  If you want to return 
 * an array of no layouts, this extension should not be used.  You should not prefix any new layouts with
 * the 'layout-' prefix.  This will automatically be added when needed.
 *
 * While not in the array of layouts, layout functions will always default to 'layout-default' if no specific
 * layout is specified for the page.
 *
 * @since 0.1.0
 * @return array $layouts Array of layouts available for use within the theme.
 */
function post_layouts_available() {

	/* Creates an array of default layouts. */
	$layouts = array(
		'1c',	// One column
		'2c-l',	// Two columns, content left
		'2c-r',	// Two columns, content right
		'3c-l',	// Three columns, content left
		'3c-r',	// Three columns, content right
		'3c-c'	// Three columns, content centered
	);

	/* Allow developers to overwrite the available layouts. */
	return apply_filters( 'post_layouts_available', $layouts );
}

?>