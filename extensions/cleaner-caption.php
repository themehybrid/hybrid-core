<?php
/**
 * Cleaner Caption - Cleans up the WP [caption] shortcode.
 *
 * Note that this extension is deprecated and no longer useful now that WP core supports its features 
 * completely.  Theme authors need to remove code that adds support for `cleaner-caption`.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   CleanerCaption
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2011 - 2015, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add notice that `cleaner-caption` is deprecated in Hybrid Core. */
_deprecated_function( "add_theme_support( 'cleaner-caption' )", '2.1.0', "add_theme_support( 'html5', array( 'caption' ) )" );

/* Filter the caption shortcode output. */
add_filter( 'img_caption_shortcode', 'cleaner_caption', 10, 3 );

/**
 * Cleans up the default WordPress [caption] shortcode.  The main purpose of this function is to remove the 
 * inline styling WP adds, which creates 10px of padding around captioned elements.
 *
 * @since      0.1.0
 * @deprecated 1.0.0
 * @access     public
 * @param      string $output  The output of the default caption (empty string at this point).
 * @param      array  $attr    Array of arguments for the [caption] shortcode.
 * @param      string $content The content placed after the opening [caption] tag and before the closing [/caption] tag.
 * @return     string $output  The formatted HTML for the caption.
 */
function cleaner_caption( $output, $attr, $content ) {

	_deprecated_function( __FUNCTION__, '1.0.0', 'img_caption_shortcode' );

	return $output;
}
