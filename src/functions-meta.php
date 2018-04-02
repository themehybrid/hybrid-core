<?php
/**
 * Metadata functions.
 *
 * Metadata functions used in the core framework.  This file registers meta keys
 * for use in WordPress in a safe manner by setting up a custom sanitize callback.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Register meta on the 'init' hook.
add_action( 'init', __NAMESPACE__ . '\register_meta', 15 );

/**
 * Registers the framework's custom metadata keys and sets up the sanitize
 * callback function.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function register_meta() {

	// Template meta.
	array_map( function( $type ) {

		\register_meta( $type, get_template_meta_key(), [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_file_name',
			'auth_callback'     => '__return_false',
			'show_in_rest'      => true
		] );

	}, [ 'term', 'user' ] );
}
