<?php
/**
 * Metadata functions used in the core framework.  This file registers meta keys for use in WordPress
 * in a safe manner by setting up a custom sanitize callback.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Register meta on the 'init' hook.
add_action( 'init', 'hybrid_register_meta', 15 );

/**
 * Registers the framework's custom metadata keys and sets up the sanitize callback function.
 *
 * @since  1.3.0
 * @access public
 * @return void
 */
function hybrid_register_meta() {

	// Register meta if the theme supports the 'hybrid-core-template-hierarchy' feature.
	if ( current_theme_supports( 'hybrid-core-template-hierarchy' ) ) {

		foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
			if ( 'page' !== $post_type )
				register_meta( 'post', "_wp_{$post_type}_template", 'sanitize_text_field', '__return_false' );
		}
	}

	// Theme layouts meta.
	if ( current_theme_supports( 'theme-layouts' ) ) {
		register_meta( 'post', hybrid_get_layout_meta_key(), 'sanitize_key', '__return_false' );
		register_meta( 'user', hybrid_get_layout_meta_key(), 'sanitize_key', '__return_false' );
	}

	// Post styles meta.
	register_meta( 'post', hybrid_get_style_meta_key(), 'sanitize_text_field', '__return_false' );
}
