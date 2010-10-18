<?php
/**
 * Post Stylesheets - A WordPress script for post-specific stylesheets.
 *
 * Post Stylesheets allows users and developers to add unique, per-post stylesheets.  This script was 
 * created so that custom stylesheet files could be dropped into a theme's '/css' folder and loaded for 
 * individual posts using the 'Stylesheet' post meta key and the stylesheet name as the post meta value.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostStylesheets
 * @version 0.1.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filters stylesheet_uri with a function for adding a new style. */
add_filter( 'stylesheet_uri', 'post_stylesheets_stylesheet_uri', 10, 2 );

/**
 * Checks if a post (or any post type) has the given meta key of 'Stylesheet' when on the singular view of 
 * the post on the front of the site.  If found, the function checks within the '/css' folder of the stylesheet 
 * directory (child theme) and the template directory (parent theme).  If the file exists, it is used rather 
 * than the typical style.css file.
 *
 * @since 0.1.0
 */
function post_stylesheets_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
	global $wp_query;

	/* Check if viewing a singular post. */
	if ( is_singular() ) {

		/* Allow plugin/theme developers to override the default meta key. */
		$meta_key = apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' );

		/* Get the post ID. */
		$post_id = $wp_query->get_queried_object_id();

		/* Check if the user has set a value for the post stylesheet. */
		$stylesheet = get_post_meta( $post_id, $meta_key, true );

		/* If a meta value was given and the file exists, set $stylesheet_uri to the new file. */
		if ( !empty( $stylesheet ) ) {

			/* If the stylesheet is found in the child theme '/css' folder, use it. */
			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . "css/{$stylesheet}" ) )
				$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . "css/{$stylesheet}";

			/* Else, if the stylesheet is found in the parent theme '/css' folder, use it. */
			elseif ( file_exists( trailingslashit( get_template_directory() ) . "css/{$stylesheet}" ) )
				$stylesheet_uri = trailingslashit( get_template_directory_uri() ) . "css/{$stylesheet}";
		}
	}

	/* Return the stylesheet URI. */
	return $stylesheet_uri;
}

?>