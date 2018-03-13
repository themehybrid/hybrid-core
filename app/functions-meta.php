<?php
/**
 * Metadata functions used in the core framework.  This file registers meta keys for use in WordPress
 * in a safe manner by setting up a custom sanitize callback.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Register meta on the 'init' hook.
add_action( 'init', __NAMESPACE__ . '\register_meta', 15 );

/**
 * Registers the framework's custom metadata keys and sets up the sanitize callback function.
 *
 * @since  1.3.0
 * @access public
 * @return void
 */
function register_meta() {

	// Template meta.
	\register_meta(
		'term',
		get_template_meta_key(),
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_file_name',
			'auth_callback'     => '__return_false',
			'show_in_rest'      => true
		)
	);

	\register_meta(
		'user',
		get_template_meta_key(),
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_file_name',
			'auth_callback'     => '__return_false',
			'show_in_rest'      => true
		)
	);

	// Theme layouts meta.
	if ( current_theme_supports( 'theme-layouts' ) ) {

		\register_meta(
			'post',
			get_layout_meta_key(),
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => '__return_false',
				'show_in_rest'      => true
			)
		);

		\register_meta(
			'term',
			get_layout_meta_key(),
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => '__return_false',
				'show_in_rest'      => true
			)
		);

		\register_meta(
			'user',
			get_layout_meta_key(),
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => '__return_false',
				'show_in_rest'      => true
			)
		);
	}
}
