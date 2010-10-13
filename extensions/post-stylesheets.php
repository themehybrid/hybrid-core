<?php
/**
 * Post Stylesheets allows users and developers to add unique, per-post stylesheets.  This script was created so 
 * that custom stylesheet files could be dropped into a theme's '/css' folder and loaded for individual posts using
 * the 'Stylesheet' post meta key and the stylesheet name as the post meta value.
 *
 * @copyright 2010
 * @version 0.1.0
 * @author Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostStylesheets
 */

/* Filters stylesheet_uri with a function for adding a new style. */
add_filter( 'stylesheet_uri', 'post_stylesheets_stylesheet_uri', 10, 2 );

/**
 * Checks if a post (or any post type) has the given meta key of 'Stylesheet' when on the singular view of the post
 * on the front of the site.  If found, the function checks within the '/css' folder of the stylesheet directory 
 * (child theme).  If the file exists, it is used rather than the typical style.css file.
 *
 * @since 0.1.0
 */
function post_stylesheets_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
	global $wp_query;

	/* Check if viewing a singular post. */
	if ( is_singular() ) {

		/* Allow plugin/theme developers to override the default meta key. */
		$meta_key = apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' );

		/* Check if the user has set a value for the post stylesheet. */
		$stylesheet = get_post_meta( $wp_query->post->ID, $meta_key, true );

		/* If a meta value was given and the file exists, set $stylesheet_uri to the new file. */
		if ( !empty( $stylesheet ) && file_exists( get_stylesheet_directory() . "/css/{$stylesheet}" ) )
			$stylesheet_uri = $stylesheet_dir_uri . "/css/{$stylesheet}";
	}

	/* Return the stylesheet URI. */
	return $stylesheet_uri;
}

?>