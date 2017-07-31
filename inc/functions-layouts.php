<?php
/**
 * Layouts API - An API for themes to build layout options.
 *
 * Theme Layouts was created to allow theme developers to easily style themes with dynamic layout
 * structures. This file merely contains the API function calls at theme developers' disposal.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Registers default layouts.
add_action( 'init', 'hybrid_register_layouts', 95 );

# Filters `current_theme_supports( 'theme-layouts', $arg )`.
add_filter( 'current_theme_supports-theme-layouts', 'hybrid_theme_layouts_support', 10, 3 );

# Filters the theme layout.
add_filter( 'hybrid_get_theme_layout', 'hybrid_filter_layout', 5 );

/**
 * Returns the layout registry. Use this function to access the object.
 *
 * @since  4.0.0
 * @access public
 * @return object
 */
function hybrid_layout_registry() {

	return hybrid_registry( 'layout' );
}

/**
 * Registers the default theme layouts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_register_layouts() {

	hybrid_register_layout(
		'default',
		array(
			// Translators: Default theme layout option.
			'label'            => esc_html_x( 'Default', 'theme layout', 'hybrid-core' ),
			'is_global_layout' => false,
			'_builtin'         => true,
			'_internal'        => true,
		)
	);

	// Hook for registering theme layouts. Theme should always register on this hook.
	do_action( 'hybrid_register_layouts' );
}

/**
 * Function for registering a layout.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function hybrid_register_layout( $name, $args = array() ) {

	hybrid_layout_registry()->register( $name, new Hybrid_Layout( $name, $args ) );
}

/**
 * Unregisters a layout.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_unregister_layout( $name ) {

	hybrid_layout_registry()->unregister( $name );
}

/**
 * Checks if a layout exists.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function hybrid_layout_exists( $name ) {

	return hybrid_layout_registry()->exists( $name );
}

/**
 * Returns an array of registered layout objects.
 *
 * @since  3.0.0
 * @access public
 * @return array
 */
function hybrid_get_layouts() {

	return hybrid_layout_registry()->get_collection();
}

/**
 * Returns a layout object if it exists.  Otherwise, `FALSE`.
 *
 * @see    Hybrid_Layout
 * @since  3.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function hybrid_get_layout( $name ) {

	return hybrid_layout_registry()->get_layout( $name );
}

/**
 * Gets the theme layout.  This is the global theme layout defined. Other functions filter the
 * available `theme_mod_theme_layout` hook to overwrite this.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_theme_layout() {

	return apply_filters( 'hybrid_get_theme_layout', hybrid_get_global_layout() );
}

/**
 * Returns the theme mod used for the global layout setting.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_global_layout() {

	return hybrid_get_theme_mod( 'theme_layout', hybrid_get_default_layout() );
}

/**
 * Returns the default layout defined by the theme.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_default_layout() {
	$support = get_theme_support( 'theme-layouts' );

	return isset( $support[0] ) && isset( $support[0]['default'] ) ? $support[0]['default'] : 'default';
}

/**
 * Checks if the current layout matches the layout to check against.
 *
 * @since  4.0.0
 * @access public
 * @param  string  $layout
 * @return bool
 */
function hybrid_is_layout( $layout ) {

	return $layout === hybrid_get_theme_layout();
}

/**
 * Gets a post layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function hybrid_get_post_layout( $post_id ) {

	return get_post_meta( $post_id, hybrid_get_layout_meta_key(), true );
}

/**
 * Sets a post layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @param  string  $layout
 * @return bool
 */
function hybrid_set_post_layout( $post_id, $layout ) {

	return 'default' !== $layout ? update_post_meta( $post_id, hybrid_get_layout_meta_key(), $layout ) : hybrid_delete_post_layout( $post_id );
}

/**
 * Deletes a post layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function hybrid_delete_post_layout( $post_id ) {

	return delete_post_meta( $post_id, hybrid_get_layout_meta_key() );
}

/**
 * Checks a post if it has a specific layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function hybrid_has_post_layout( $layout, $post_id = '' ) {

	if ( ! $post_id )
		$post_id = get_the_ID();

	return $layout == hybrid_get_post_layout( $post_id ) ? true : false;
}

/**
 * Gets a term layout.
 *
 * @since  4.0.0
 * @access public
 * @param  int     $term_id
 * @return bool
 */
function hybrid_get_term_layout( $term_id ) {

	return get_term_meta( $term_id, hybrid_get_layout_meta_key(), true );
}

/**
 * Sets a term layout.
 *
 * @since  4.0.0
 * @access public
 * @param  int     $term_id
 * @param  string  $layout
 * @return bool
 */
function hybrid_set_term_layout( $term_id, $layout ) {

	return 'default' !== $layout ? update_term_meta( $term_id, hybrid_get_layout_meta_key(), $layout ) : hybrid_delete_term_layout( $term_id );
}

/**
 * Deletes a term layout.
 *
 * @since  4.0.0
 * @access public
 * @param  int     $term_id
 * @return bool
 */
function hybrid_delete_term_layout( $term_id ) {

	return delete_term_meta( $term_id, hybrid_get_layout_meta_key() );
}

/**
 * Checks a term if it has a specific layout.
 *
 * @since  4.0.0
 * @access public
 * @param  int     $term_id
 * @return bool
 */
function hybrid_has_term_layout( $layout, $term_id = '' ) {

	if ( ! $term_id )
		$term_id = get_queried_object_id();

	return $layout == hybrid_get_term_layout( $term_id ) ? true : false;
}

/**
 * Gets a user layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $user_id
 * @return bool
 */
function hybrid_get_user_layout( $user_id ) {

	return get_user_meta( $user_id, hybrid_get_layout_meta_key(), true );
}

/**
 * Sets a user layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $user_id
 * @param  string  $layout
 * @return bool
 */
function hybrid_set_user_layout( $user_id, $layout ) {

	return 'default' !== $layout ? update_user_meta( $user_id, hybrid_get_layout_meta_key(), $layout ) : hybrid_delete_user_layout( $user_id );
}

/**
 * Deletes user layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $user_id
 * @return bool
 */
function hybrid_delete_user_layout( $user_id ) {

	return delete_user_meta( $user_id, hybrid_get_layout_meta_key() );
}

/**
 * Checks if a user/author has a specific layout.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $layout
 * @param  int     $user_id
 * @return bool
 */
function hybrid_has_user_layout( $layout, $user_id = '' ) {

	if ( ! $user_id )
		$user_id = absint( get_query_var( 'author' ) );

	return $layout == hybrid_get_user_layout( $user_id ) ? true : false;
}

/**
 * Default filter on the `theme_mod_theme_layout` hook.  By default, we'll check for per-post
 * or per-author layouts saved as metadata.  If set, we'll filter.  Else, just return the
 * global layout.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $theme_layout
 * @return string
 */
function hybrid_filter_layout( $theme_layout ) {

	// If viewing a singular post, get the post layout.
	if ( is_singular() )
		$layout = hybrid_get_post_layout( get_queried_object_id() );

	// If viewing an author archive, get the user layout.
	elseif ( is_author() )
		$layout = hybrid_get_user_layout( get_queried_object_id() );

	// If viewing a term archive, get the term layout.
	elseif ( is_tax() || is_category() || is_tag() )
		$layout = hybrid_get_term_layout( get_queried_object_id() );

	return ! empty( $layout ) && hybrid_layout_exists( $layout ) && 'default' !== $layout ? $layout : $theme_layout;
}

/**
 * Returns an array of the available theme layouts.
 *
 * @since  3.0.0
 * @access public
 * @param  bool   $supports
 * @param  array  $args
 * @param  array  $feature
 * @return bool
 */
function hybrid_theme_layouts_support( $supports, $args, $feature ) {

	if ( isset( $args[0] ) && in_array( $args[0], array( 'customize', 'post_meta', 'term_meta' ) ) ) {

		if ( is_array( $feature[0] ) && isset( $feature[0][ $args[0] ] ) && false === $feature[0][ $args[0] ] )
			$supports = false;
	}

	return $supports;
}

/**
 * Wrapper function for returning the metadata key used for objects that can use layouts.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_layout_meta_key() {

	return apply_filters( 'hybrid_layout_meta_key', 'Layout' );
}
