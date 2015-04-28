<?php
/**
 * Theme Layouts - A WordPress script for creating dynamic layouts.
 *
 * Theme Layouts was created to allow theme developers to easily style themes with dynamic layout 
 * structures.  It gives users the ability to control how each post (or any post type) is displayed on the 
 * front end of the site.  The layout can also be filtered for any page of a WordPress site.  
 *
 * The script will filter the WordPress body_class to provide a layout class for the given page.  Themes 
 * must support this hook or its accompanying body_class() function for the Theme Layouts script to work. 
 * Themes must also handle the CSS based on the layout class.  This script merely provides the logic.  The 
 * design should be handled on a theme-by-theme basis.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   ThemeLayouts
 * @version   1.0.0-beta-1
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2015, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Sets up and runs the theme layouts feature.
 *
 * @since  3.0.0
 * @access public
 */
final class Hybrid_Theme_Layouts {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  3.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Sets up the required actions/filters for theme layouts.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Register metadata with WordPress. */
		add_action( 'init', array( $this, 'register_meta' ) );

		/* Add post type support for theme layouts. */
		add_action( 'init', array( $this, 'post_type_support' ), 15 );

		/* Filters the theme layout mod. */
		add_filter( 'theme_mod_theme_layout', array( $this, 'filter_layout' ), 5 );

		/* Filters the body_class hook to add a custom class. */
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Registers post and user meta keys.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function register_meta() {
		register_meta( 'post', hybrid_get_layout_meta_key(), 'sanitize_key' );
		register_meta( 'user', hybrid_get_layout_meta_key(), 'sanitize_key' );
	}

	/**
	 * Adds post type support for specific post types. Theme and plugin authors can further add 
	 * support via the `supports` argument when registering their post type or via the 
	 * `add_post_type_support()` function.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function post_type_support() {

		/* Core WordPress post types. */
		add_post_type_support( 'post',              'theme-layouts' );
		add_post_type_support( 'page',              'theme-layouts' );
		add_post_type_support( 'attachment',        'theme-layouts' );

		/* Plugin post types. */
		add_post_type_support( 'forum',             'theme-layouts' );
		add_post_type_support( 'literature',        'theme-layouts' );
		add_post_type_support( 'portfolio_item',    'theme-layouts' );
		add_post_type_support( 'portfolio_project', 'theme-layouts' );
		add_post_type_support( 'product',           'theme-layouts' );
		add_post_type_support( 'restaurant_item',   'theme-layouts' );
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
	public function filter_layout( $theme_layout ) {

		/* If viewing a singular post, get the post layout. */
		if ( is_singular() )
			$layout = hybrid_get_post_layout( get_queried_object_id() );

		/* If viewing an author archive, get the user layout. */
		elseif ( is_author() )
			$layout = hybrid_get_user_layout( get_queried_object_id() );

		return !empty( $layout ) ? $layout : $theme_layout;
	}

	/**
	 * Filters the `<body>` class to add our theme layout class.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function body_class( $classes ) {

		/* Adds the layout to array of body classes. */
		$classes[] = sanitize_html_class( 'layout-' . hybrid_get_layout() );

		/* Return the $classes array. */
		return $classes;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

/* Let's roll! */
Hybrid_Theme_Layouts::get_instance();

/**
 * Returns an array of the available theme layouts.
 *
 * @since  3.0.0
 * @access public
 * @return array
 */
function hybrid_get_layouts() {
	$layouts = get_theme_support( 'theme-layouts' );
	return isset( $layouts[0] ) ? array_keys( $layouts[0] ) : array_keys( hybrid_get_layout_labels() );
}

/**
 * Parse the theme layouts arguments and return them.
 *
 * @since  3.0.0
 * @access public
 * @return array
 */
function hybrid_get_layouts_args() {

	$defaults = array( 
		'customize' => true, 
		'post_meta' => true, 
		'default'   => 'default' 
	);

	$layouts = get_theme_support( 'theme-layouts' );

	$args = isset( $layouts[1] ) ? $layouts[1] : array();

	return apply_filters( 'hybrid_get_theme_layouts_args', wp_parse_args( $args, $defaults ) );
}

/**
 * Gets the theme layout.  This is the global theme layout defined. Other functions filter the 
 * available `theme_mod_theme_layout` hook to overwrite this.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_layout() {
	return get_theme_mod( 'theme_layout', hybrid_get_default_layout() );
}

/**
 * Returns the default layout defined by the theme.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function hybrid_get_default_layout() {
	$args = hybrid_get_layouts_args();
	return $args['default'];
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

	if ( !$post_id )
		$post_id = get_the_ID();

	return $layout == hybrid_get_post_layout( $post_id ) ? true : false;
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

	if ( !$user_id )
		$user_id = get_query_var( 'author' );

	return $layout == hybrid_get_user_layout( $user_id ) ? true : false;
}

/**
 * Prints the layout label to the screen.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $layout
 * @return void
 */
function hybrid_layout_label( $layout ) {
	echo hybrid_get_layout_label( $layout );
}

/**
 * Returns the layout label.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $layout
 * @return string
 */
function hybrid_get_layout_label( $layout ) {

	/* Get an array of post layout strings. */
	$strings = hybrid_get_layout_labels();

	/* Return the layout's string if it exists. Else, return the layout slug. */
	return isset( $strings[ $layout ] ) ? $strings[ $layout ] : $layout;
}

/**
 * Returns an array of all layout labels.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $layout
 * @return void
 */
function hybrid_get_layout_labels() {

	/* Set up the default layout strings. */
	$strings = array(
		/* Translators: Default theme layout option. */
		'default' => _x( 'Default', 'theme layout', 'hybrid-core' )
	);

	/* Get theme-supported layouts. */
	$layouts = get_theme_support( 'theme-layouts' );

	/* Assign the strings passed in by the theme author. */
	if ( isset( $layouts[0] ) )
		$strings = array_merge( $layouts[0], $strings );

	/* Allow devs to filter the strings for custom layouts. */
	return apply_filters( 'hybrid_layout_labels', $strings );
}

/**
 * Returns an array of layout choices in key (layout) => value (label) pairs. For use in forms to 
 * select the layout.
 *
 * @since  3.0.0
 * @access public
 * @return array
 */
function hybrid_get_layout_choices() {

	$layouts = hybrid_get_layouts();

	/* Set up an array for the layout choices and add in the 'default' layout. */
	$layout_choices = array();

	$layout_choices['default'] = hybrid_get_layout_label( 'default' );

	/* Loop through each of the layouts and add it to the choices array with proper key/value pairs. */
	foreach ( $layouts as $layout )
		$layout_choices[ $layout ] = hybrid_get_layout_label( $layout );

	return $layout_choices;
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
