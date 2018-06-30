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
