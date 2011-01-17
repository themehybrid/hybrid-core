<?php
/**
 * Custom Field Series - A script for creating a series of posts by custom field.
 *
 * Custom Field Series was created to allow users to add individual posts to a larger series of posts.  It was 
 * created before WordPress made it easy for developers to create new taxonomies.  Ideally, one would use a 
 * taxonomy to handle this functionality.  However, this method is lighter, provides an extremely simple 
 * method for adding posts to a series, and offers backwards compatibility for people that have used this 
 * method before.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CustomFieldSeries
 * @version 0.2.3
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2007 - 2010, Justin Tadlock
 * @link http://justintadlock.com/archives/2007/11/01/wordpress-custom-fields-listing-a-series-of-posts
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Checks for a series of posts by the current post's metadata.  The function grabs the meta value for the 
 * 'Series' meta key and checks if any posts have been given the same value.  If posts are found with this 
 * meta key/value pair, the function adds them to an unordered list.
 *
 * @since 0.1
 * @param array $args Array of arguments.
 */
function custom_field_series( $args = array() ) {
	global $post;

	/* Set up a default textdomain. */
	$textdomain = apply_filters( 'custom_field_series_textdomain', 'custom-field-series' );

	/* Set $series to an empty string. */
	$series = '';

	/* Allow developers to overwrite the meta key used for the series name. */
	$meta_key = apply_filters( 'custom_field_series_meta_key', 'Series' );

	/* Get the series meta value for the post. */
	$meta_value = get_post_meta( $post->ID, $meta_key, true );

	/* If a meta value was found, create a list of posts in the series. */
	if ( !empty( $meta_value ) ) {

		/* Set up the default post query arguments. */
		$defaults = array(
			'order' => 'DESC',
			'orderby' => 'ID',
			'include' => '',
			'exclude' => '',
			'post_type' => 'any',
			'numberposts' => -1,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value,
			'echo' => true
		);

		/* Allow developers to override the arguments used. */
		$args = apply_filters( 'custom_field_series_args', $args );
		$args = wp_parse_args( $args, $defaults );

		/* Get all posts in the current series. */
		$series_posts = get_posts( $args );

		/* If posts were found, display them. */
		if ( !empty( $series_posts ) ) {

			/* Format the series class with the name of the series. */
			$class = str_replace( array( '_', ' ', '&nbsp;' ) , '-', $series_meta );
			$class = preg_replace('/[^A-Za-z0-9-]/', '', $class );
			$class = strtolower( $class );

			/* Create the opening wrapper div, title, and list element. */
			$series = '<div class="series series-' . esc_attr( $class ) . '">';
			$series .= '<h4 class="series-title">' . __( 'Articles in this series', $textdomain) . '</h4>';
			$series .= '<ul>';

			/* Loop through the posts. */
			foreach ( $series_posts as $serial ) {

				/* If the current post in the loop matches the post we're viewing, don't link to it. */
				if ( $serial->ID == $post->ID )
					$series .= '<li class="current-post">' . $serial->post_title . '</li>';

				/* Display a link to the post. */
				else
					$series .= '<li><a href="' . get_permalink( $serial->ID ) . '" title="' . esc_attr( $serial->post_title ) . '">' . $serial->post_title . '</a></li>';
			}

			/* Close the unordered list and wrapper div. */
			$series .= '</ul></div>';
		}
	}

	/* Allow developers to overwrite the HTML of the series. */
	$series = apply_filters( 'custom_field_series', $series );

	/* If $echo is set to true, display the series. */
	if ( !empty( $args['echo'] ) )
		echo $series;

	/* If $echo is not set to true, return the series. */
	else
		return $series;
}

?>