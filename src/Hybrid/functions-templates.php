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

use Hybrid\Template\ObjectTemplate;

# Run hook for registering templates.
add_action( 'init', __NAMESPACE__ . '\register_templates', 95 );

/**
 * Returns the template registry. Use this function to access the object.
 *
 * @since  5.0.0
 * @access public
 * @return object
 */
function templates() {

	return app()->get( 'templates' );
}

/**
 * Executes the action hook for themes to register their templates.  Themes should
 * always register on `hybrid/register_templates`.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function register_templates() {

	do_action( 'hybrid/register_templates' );
}

/**
 * Function for registering a template.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function register_template( $name, array $args = [] ) {

	templates()->add( $name, new ObjectTemplate( $name, $args ) );
}

/**
 * Unregisters a template.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function unregister_template( $name ) {

	templates()->remove( $name );
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

	return templates()->has( $name );
}

/**
 * Returns an array of registered template objects.
 *
 * @since  5.0.0
 * @access public
 * @return array
 */
function get_templates() {

	return templates()->all();
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

	return templates()->get( $name );
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

	// If there's a template or `page` is the post type, return.
	if ( $template || 'page' === $type ) {
		return $template;
	}

	// Get old Hybrid Core post template meta.
	$template = get_post_meta( $post_id, "_wp_{$type}_template", true );

	// If old template, run the compat function.
	if ( $template ) {
		post_template_compat( $post_id, $template );
	}

	// Return the template.
	return $template;
}

/**
 * Sets a post template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $post_id
 * @param  string  $template
 * @return bool
 */
function set_post_template( $post_id, $template ) {

	return 'default' !== $template
	       ? update_post_meta( $post_id, '_wp_page_template', $template )
	       : delete_post_template( $post_id );
}

/**
 * Deletes a post template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function delete_post_template( $post_id ) {

	return delete_post_meta( $post_id, '_wp_page_template' );
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

	return check_template_match( $template, get_post_template( $post_id ) );
}

/**
 * Gets a term template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $term_id
 * @return bool
 */
function get_term_template( $term_id ) {

	return get_term_meta( $term_id, get_template_meta_key(), true );
}

/**
 * Sets a term template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $term_id
 * @param  string  $template
 * @return bool
 */
function set_term_template( $term_id, $template ) {

	return 'default' !== $template
	       ? update_term_meta( $term_id, get_template_meta_key(), $template )
	       : delete_term_template( $term_id );
}

/**
 * Deletes a term template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $term_id
 * @return bool
 */
function delete_term_template( $term_id ) {

	return delete_term_meta( $term_id, get_template_meta_key() );
}

/**
 * Checks a term if it has a specific template.  If no template is passed in,
 * it'll check if the term has a template at all.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $term_id
 * @return bool
 */
function has_term_template( $template = '', $term_id = '' ) {

	if ( ! $term_id ) {
		$term_id = get_queried_object_id();
	}

	return check_template_match( $template, get_term_template( $term_id ) );
}

/**
 * Gets a user template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $user_id
 * @return bool
 */
function get_user_template( $user_id ) {

	return get_user_meta( $user_id, get_template_meta_key(), true );
}

/**
 * Sets a user template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $user_id
 * @param  string  $template
 * @return bool
 */
function set_user_template( $user_id, $template ) {

	return 'default' !== $template
	       ? update_user_meta( $user_id, get_template_meta_key(), $template )
	       : delete_user_template( $user_id );
}

/**
 * Deletes user template.
 *
 * @since  5.0.0
 * @access public
 * @param  int     $user_id
 * @return bool
 */
function delete_user_template( $user_id ) {

	return delete_user_meta( $user_id, get_template_meta_key() );
}

/**
 * Checks if a user/author has a specific template.  If no template is passed in,
 * it'll check if the user has a template at all.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $template
 * @param  int     $user_id
 * @return bool
 */
function has_user_template( $template = '', $user_id = '' ) {

	if ( ! $user_id ) {
		$user_id = absint( get_query_var( 'author' ) );
	}

	return check_template_match( $template, get_user_template( $user_id ) );
}

/**
 * Helper function for use with the `hybrid_has_*_template()` functions.  Theme
 * authors should not use this function directly.  Instead, use the appropriate
 * conditional function.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $template
 * @param  string  $filename
 * @return bool
 */
function check_template_match( $template, $filename ) {

	// Check if the template is the filename. This is the most likely
	// scenario because templates should be stored by their filenames.
	if ( $template && $template === $filename ) {
		return true;
	}

	// Check if the template matches a template object by filename.
	if ( $template && $filename ) {

		$templates = wp_list_filter( get_templates(), [ 'filename' => $filename ] );

		if ( $templates ) {

			$template_object = array_shift( $templates );

			return $template_object->name === $template;
		}
	}

	// Return whether we have a template at all.
	return ! empty( $filename );
}

/**
 * Wrapper function for returning the metadata key used for objects that can
 * use templates.
 *
 * @since  5.0.0
 * @access public
 * @return string
 */
function get_template_meta_key() {

	return apply_filters( 'hybrid/template_meta_key', 'template' );
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

	$args = [ 'is_post_template' => false, 'filename' => '' ];

	$templates = wp_list_filter( get_templates(), $args, 'NOT' );

	foreach ( $templates as $template ) {

		if ( ! $template->post_types || in_array( $post_type, $template->post_types ) ) {

			$post_templates[ $template->filename ] = esc_html( $template->label );
		}
	}

	return $post_templates;
}
