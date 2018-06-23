<?php
/**
 * Templates API - An API for themes to build templates for users to select.
 *
 * Theme Templates was created to allow theme developers to register custom
 * templates for objects (posts, terms, users). This file merely contains the
 * API function calls at theme developers' disposal.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Run hook for registering templates.
add_action( 'init', __NAMESPACE__ . '\register_templates', 95 );

/**
 * Executes the action hook for themes to register their templates. Themes should
 * always register on this hook.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function register_templates() {

	do_action( 'hybrid/templates/register', app( 'templates' ) );

	// Add a filter to each post type so that we can determine if that post
	// type has any custom templates registered for it.
	foreach ( get_post_types() as $type ) {

		add_filter( "theme_{$type}_templates", __NAMESPACE__ . '\post_templates_filter', 5, 4 );
	}
}

/**
 * Checks if a template exists.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function template_exists( $name ) {

	return app( 'templates' )->has( $name );
}

/**
 * Returns an array of registered template objects.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_templates() {

	return app( 'templates' )->all();
}

/**
 * Returns a template object if it exists.  Otherwise, `FALSE`.
 *
 * @since  5.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function get_template( $name ) {

	return app( 'templates' )->get( $name );
}

/**
 * Gets a post template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function get_post_template( $post_id ) {

	$type     = get_post_type( $post_id );
	$template = get_page_template_slug( $post_id );

	// If not a page template, check for back-compat template.
	if ( ! $template && 'page' !== $type ) {

		// Get old Hybrid Core post template meta.
		$template = get_post_meta( $post_id, "_wp_{$type}_template", true );

		// If old template, run the compat function.
		if ( $template ) {
			post_template_compat( $post_id, $template );
		}
	}

	// Return the template.
	return $template;
}

/**
 * Checks a post if it has a specific template.  If no template is passed in,
 * it'll check if the post has a template at all.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $template
 * @param  int     $post_id
 * @return bool
 */
function has_post_template( $template = '', $post_id = '' ) {

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$has_template = get_post_template( $post_id );

	if ( ! $template && $has_template ) {
		return true;
	}

	return $template === $has_template;
}

/**
 * Filter used on `theme_{$post_type}_templates` to add custom templates to the
 * template drop-down.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $post_templates
 * @param  object  $theme
 * @param  object  $post
 * @param  string  $post_type
 * @return array
 */
function post_templates_filter( $post_templates, $theme, $post, $post_type ) {

	foreach ( app( 'templates' )->all() as $template ) {

		if ( $template->forPostType( $post_type ) ) {

			$post_templates[ $template->filename() ] = esc_html( $template->label() );
		}
	}

	return $post_templates;
}
