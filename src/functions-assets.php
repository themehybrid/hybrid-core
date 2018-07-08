<?php
/**
 * Asset functions.
 *
 * Functions for handling scripts and styles in the framework.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Loads the `comment-reply` script when it's needed.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function enqueue_scripts() {

	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Searches for a locale stylesheet.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $slug
 * @return string
 */
function get_locale_style( $slug = '' ) {

	$styles = array_map( function( $hier ) use ( $slug ) {

		return $slug ? "{$slug}-{$hier}.css" : "{$hier}.css";

	}, get_lang_hierarchy() );

	return locate_file_uri( $styles );
}
