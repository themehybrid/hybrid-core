<?php
/**
 * Loop Pagination - A WordPress script for creating paginated links on archive-type pages.
 *
 * Note that this extension is deprecated and no longer useful now that WP core has proper numbered 
 * post pagination features. Theme authors should remove support for the `loop-pagination` feature 
 * and change their `loop_pagination()` function calls to `the_posts_pagination()` to print or to 
 * `get_the_posts_pagination()` to return.  Theme CSS styles will most likely need to be altered as 
 * well to handle the core CSS classes.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   LoopPagination
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2015, Justin Tadlock
 * @link      http://themehybrid.com/docs/tutorials/loop-pagination
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add notice that `loop-pagination` is deprecated in Hybrid Core. */
_deprecated_function( "add_theme_support( 'loop-pagination' )", '2.1.0', '' );

/**
 * Loop pagination function for paginating loops with multiple posts.  This should be used on archive, blog, and 
 * search pages.  It is not for singular views.
 *
 * @since      0.1.0
 * @deprecated 1.0.0
 * @access     public
 * @param      array   $args
 * @return     string
 */
function loop_pagination( $args = array() ) {

	_deprecated_function( __FUNCTION__, '1.0.0', 'the_posts_pagination()' );

	return isset( $args['echo'] ) && false === $args['echo'] ? get_the_posts_pagination( $args ) : the_posts_pagination( $args );
}
