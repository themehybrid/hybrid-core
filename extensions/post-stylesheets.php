<?php
/**
 * Functions for displaying a series of posts linked together
 * by a custom field called 'Series'.  Each post is listed that
 * belong to the same series of posts.
 *
 * @copyright 2010
 * @version 0.1
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

add_filter( 'stylesheet_uri', 'post_stylesheets', 10, 2 );

function post_stylesheets( $stylesheet_uri, $stylesheet_dir_uri ) {
	global $wp_query;

	if ( is_singular() ) {

		$stylesheet = get_post_meta( $wp_query->post->ID, apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' ), true );

		if ( !empty( $stylesheet ) ) {
			if ( file_exists( get_stylesheet_directory() . "/css/{$stylesheet}" ) )
				$stylesheet_uri = $stylesheet_dir_uri . "/css/{$stylesheet}";

			elseif ( file_exists( get_template_directory() . "/css/{$stylesheet}" ) )
				$stylesheet_uri = get_template_directory_uri() . "/css/{$stylesheet}";
		}
	}

	return $stylesheet_uri;
}


















?>