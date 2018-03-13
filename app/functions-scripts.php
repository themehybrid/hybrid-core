<?php
/**
 * Functions for handling JavaScript in the framework.  Themes can add support for the
 * 'hybrid-core-scripts' feature to allow the framework to handle loading the stylesheets into
 * the theme header or footer at an appropriate time.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Register Hybrid Core scripts.
//add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_scripts', 0 );

# Load Hybrid Core scripts.
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 5 );

/**
 * Registers JavaScript files for the framework.  This function merely registers scripts with WordPress using
 * the wp_register_script() function.  It does not load any script files on the site.  If a theme wants to register
 * its own custom scripts, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since  1.2.0
 * @access public
 * @return void
 */
function register_scripts() {}

/**
 * Tells WordPress to load the scripts needed for the framework using the wp_enqueue_script() function.
 *
 * @since  1.2.0
 * @access public
 * @return void
 */
function enqueue_scripts() {

	// Load the comment reply script on singular posts with open comments if threaded comments are supported.
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );
}
