<?php
/**
 * Scripts functions.
 *
 * Functions for handling JavaScript in the framework.  Themes can add support
 * for the 'hybrid-core-scripts' feature to allow the framework to handle loading
 * the stylesheets into the theme header or footer at an appropriate time.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Register scripts.
add_action( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\customize_controls_register_scripts', 0 );
add_action( 'customize_preview_init',             __NAMESPACE__ . '\customize_preview_register_scripts',  0 );

# Enqueue scripts.
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 5 );

/**
 * Registers scripts for use with customize controls.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function customize_controls_register_scripts() {

	$suffix = get_min_suffix();

	wp_register_script(
		'hybrid-customize-controls',
		uri( "resources/scripts/customize-controls{$suffix}.js" ),
		[ 'customize-controls', 'jquery' ],
		app()->version(),
		true
	);
}

/**
 * Registers scripts for use with customize preview.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function customize_preview_register_scripts() {

	$suffix = get_min_suffix();

	wp_register_script(
		'hybrid-customize-preview',
		uri( "resources/scripts/customize-preview{$suffix}.js" ),
		[ 'customize-preview', 'jquery' ],
		app()->version(),
		true
	);
}

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
