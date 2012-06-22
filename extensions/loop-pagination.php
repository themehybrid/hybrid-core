<?php
/**
 * Loop Pagination - A WordPress script for creating paginated links on archive-type pages.
 *
 * The Loop Pagination script was designed to give theme authors a quick way to paginate archive-type 
 * (archive, search, and blog) pages without having to worry about which of the many plugins a user might 
 * possibly be using.  Instead, they can simply build pagination right into their themes.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package LoopPagination
 * @version 0.1.5
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2012, Justin Tadlock
 * @link http://devpress.com/blog/loop-pagination-for-theme-developers
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Loop pagination function for paginating loops with multiple posts.  This should be used on archive, blog, and 
 * search pages.  It is not for singular views.
 *
 * @since 0.1.0
 * @access public
 * @uses paginate_links() Creates a string of paginated links based on the arguments given.
 * @param array $args Arguments to customize how the page links are output.
 * @return string $page_links
 */
function loop_pagination( $args = array() ) {
	global $wp_rewrite, $wp_query;

	/* If there's not more than one page, return nothing. */
	if ( 1 >= $wp_query->max_num_pages )
		return;

	/* Get the current page. */
	$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

	/* Get the max number of pages. */
	$max_num_pages = intval( $wp_query->max_num_pages );

	/* Set up some default arguments for the paginate_links() function. */
	$defaults = array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'total' => $max_num_pages,
		'current' => $current,
		'prev_next' => true,
		//'prev_text' => __( '&laquo; Previous' ), // This is the WordPress default.
		//'next_text' => __( 'Next &raquo;' ), // This is the WordPress default.
		'show_all' => false,
		'end_size' => 1,
		'mid_size' => 1,
		'add_fragment' => '',
		'type' => 'plain',
		'before' => '<div class="pagination loop-pagination">', // Begin loop_pagination() arguments.
		'after' => '</div>',
		'echo' => true,
	);

	/* Add the $base argument to the array if the user is using permalinks. */
	if ( $wp_rewrite->using_permalinks() )
		$defaults['base'] = str_replace( 2, '%#%', esc_url( get_pagenum_link( 2 ) ) );
		//$defaults['base'] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );

	/* If we're on a search results page, we need to change this up a bit. */
	if ( is_search() ) {
		$search_permastruct = $wp_rewrite->get_search_permastruct();
		if ( !empty( $search_permastruct ) )
			$defaults['base'] = user_trailingslashit( trailingslashit( get_search_link() ) . 'page/%#%' );
	}

	/* Allow developers to overwrite the arguments with a filter. */
	$args = apply_filters( 'loop_pagination_args', $args );

	/* Merge the arguments input with the defaults. */
	$args = wp_parse_args( $args, $defaults );

	/* Don't allow the user to set this to an array. */
	if ( 'array' == $args['type'] )
		$args['type'] = 'plain';

	/* Get the paginated links. */
	$page_links = paginate_links( $args );

	/* Remove 'page/1' from the entire output since it's not needed. */
	$page_links = str_replace( array( '&#038;paged=1\'', '/page/1\'', '/page/1/\'' ), '\'', $page_links );

	/* Wrap the paginated links with the $before and $after elements. */
	$page_links = $args['before'] . $page_links . $args['after'];

	/* Allow devs to completely overwrite the output. */
	$page_links = apply_filters( 'loop_pagination', $page_links );

	/* Return the paginated links for use in themes. */
	if ( $args['echo'] )
		echo $page_links;
	else
		return $page_links;
}

?>