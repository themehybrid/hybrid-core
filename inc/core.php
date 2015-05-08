<?php
/**
 * The core functions file for the Hybrid framework. Functions defined here are generally used across the 
 * entire framework to make various tasks faster. This file should be loaded prior to any other files 
 * because its functions are needed to run the framework.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Function for setting the content width of a theme.  This does not check if a content width has been set; it 
 * simply overwrites whatever the content width is.
 *
 * @since  1.2.0
 * @access public
 * @global int    $content_width The width for the theme's content area.
 * @param  int    $width         Numeric value of the width to set.
 */
function hybrid_set_content_width( $width = '' ) {
	global $content_width;

	$content_width = absint( $width );
}

/**
 * Function for getting the theme's content width.
 *
 * @since  1.2.0
 * @access public
 * @global int    $content_width The width for the theme's content area.
 * @return int    $content_width
 */
function hybrid_get_content_width() {
	global $content_width;

	return $content_width;
}
