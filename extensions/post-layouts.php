<?php
/**
 * Post Layouts - A WordPress script for creating post-specific layouts.
 *
 * Post Layouts was created to allow theme developers to easily style themes with post-specific layout 
 * structures.  It gives users the ability to control how each post (or any post type) is displayed on the 
 * front end of the site.  This script is called "post layouts," but developers aren't limited to only creating 
 * layouts for specific posts.  The layout can be filtered for any page of a WordPress site.  
 *
 * The script will filter the WordPress body_class to provide a layout class for the given page.  Themes 
 * must support this hook or its accompanying body_class() function for the Post Layouts script to work. 
 * Themes must also handle the CSS based on the layout class.  This script merely provides the logic.  The 
 * design should be handled on a theme-by-theme basis.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostLayouts
 * @version 0.2.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filters the body_class hook to add a custom class. */
add_filter( 'body_class', 'post_layouts_body_class' );

/* Set up the custom post layouts. */
add_action( 'admin_menu', 'post_layouts_admin_setup' );

/**
 * Gets the layout for the current post based off the 'Layout' custom field key if viewing a singular post 
 * entry.  All other pages are given a default layout of 'layout-default'.
 *
 * @since 0.1.0
 * @return string The layout for the given page.
 */
function post_layouts_get_layout() {
	global $wp_query;

	/* Get the available post layouts. */
	$post_layouts = get_theme_support( 'post-layouts' );

	/* Set the layout to an empty string. */
	$layout = '';

	/* If viewing a singular post, check if a layout has been specified. */
	if ( is_singular() ) {

		/* Get the current post ID. */
		$post_id = $wp_query->get_queried_object_id();

		/* Get the post layout. */
		$layout = get_post_layout( $post_id );
	}

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( empty( $layout ) || !in_array( $layout, $post_layouts[0] ) )
		$layout = 'default';

	/* Return the layout and allow plugin/theme developers to override it. */
	return esc_attr( apply_filters( 'get_post_layout', "layout-{$layout}" ) );
}

/**
 * Get the post layout based on the given post ID.
 *
 * @since 0.2.0
 */
function get_post_layout( $post_id ) {
	$post_layout = get_post_meta( $post_id, apply_filters( 'post_layouts_meta_key', 'Layout' ), true );
	return ( !empty( $post_layout ) ? $post_layout : 'default' );
}

/**
 * Update/set the post layout based on the given post ID and layout.
 *
 * @since 0.2.0
 */
function set_post_layout( $post_id, $layout ) {
	update_post_meta( $post_id, apply_filters( 'post_layouts_meta_key', 'Layout' ), $layout );
}	

/**
 * Adds the post layout class to the WordPress body class in the form of "layout-$layout".  This allows 
 * theme developers to design their theme layouts based on the layout class.  If designing a theme with 
 * this extension, the theme should make sure to handle all possible layout classes.
 *
 * @since 0.1.0
 * @param array $classes
 * @param array $classes
 */
function post_layouts_body_class( $classes ) {

	/* Adds the layout to array of body classes. */
	$classes[] = post_layouts_get_layout();

	/* Return the $classes array. */
	return $classes;
}

/**
 * Creates default text strings based on the default post layouts.  Theme developers that add custom 
 * layouts should filter 'post_layouts_strings' to add strings to match the custom layouts, but it's not 
 * required.  The layout name will be used if no text string is found.
 *
 * @since 0.2.0
 */
function post_layouts_strings() {

	/* Set up the default layout strings. */
	$strings = array(
		'default' => __( 'Default' ),
		'1c' => __( 'One Column' ),
		'2c-l' => __( 'Two Columns, Left' ),
		'2c-r' => __( 'Two Columns, Right' ),
		'3c-l' => __( 'Three Columns, Left' ),
		'3c-r' => __( 'Three Columns, Right' ),
		'3c-c' => __( 'Three Columns, Center' )
	);

	/* Allow devs to filter the strings for custom layouts. */
	return apply_filters( 'post_layouts_strings', $strings );
}

/**
 * Get a specific layout's text string.
 *
 * @since 0.2.0
 */
function post_layouts_get_string( $layout ) {

	/* Get an array of post layout strings. */
	$strings = post_layouts_strings();

	/* Return the layout's string if it exists. Else, return the layout slug. */
	return ( ( isset( $strings[$layout] ) ) ? $strings[$layout] : $layout );
}

/**
 * Post layouts admin setup.  Registers the post layouts meta box for the post editing screen.  Adds the 
 * metadata save function to the 'save_post' hook.
 *
 * @since 0.2.0
 */
function post_layouts_admin_setup() {

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type )
		add_meta_box( 'post-layouts-meta-box', __( 'Layout' ), 'post_layouts_meta_box', $type->name, 'side', 'default' );

	/* Saves the post format on the post editing page. */
	add_action( 'save_post', 'post_layouts_save_post', 10, 2 );
}

/**
 * Displays a meta box of radio selectors on the post editing screen, which allows theme users to select 
 * the layout they wish to use for the specific post.
 *
 * @since 0.2.0
 */
function post_layouts_meta_box( $post, $box ) {

	/* Get theme-supported post layouts. */
	$layouts = get_theme_support( 'post-layouts' );
	$post_layouts = $layouts[0];

	/* Get the current post's layout. */
	$post_layout = get_post_layout( $post->ID ); ?>

	<div class="post-layout">

		<input type="hidden" name="post_layouts_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

		<p><?php _e( 'Post layouts allow you to select a specific theme layout structure for the post.' ); ?></p>

		<div class="post-layout-wrap">
			<ul>
				<li><input type="radio" name="post_layout" id="post_layout_default" value="default" <?php checked( $post_layout, 'default' );?> /> <label for="post_layout_default"><?php echo esc_html( post_layouts_get_string( 'default' ) ); ?></label></li>

				<?php foreach ( $post_layouts as $layout ) { ?>
					<li><input type="radio" name="post_layout" id="post_layout_<?php echo esc_attr( $layout ); ?>" value="<?php echo esc_attr( $layout ); ?>" <?php checked( $post_layout, $layout ); ?> /> <label for="post_layout_<?php echo esc_attr( $layout ); ?>"><?php echo esc_html( post_layouts_get_string( $layout ) ); ?></label></li>
				<?php } ?>
			</ul>
		</div>
	</div><?php
}

/**
 * Saves the post layout metadata if on the post editing screen in the admin.
 *
 * @since 0.2.0
 */
function post_layouts_save_post( $post_id, $post ) {

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['post_layouts_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['post_layouts_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the previous post layout. */
	$old_layout = get_post_layout( $post_id );

	/* Get the submitted post layout. */
	$new_layout = esc_attr( $_POST['post_layout'] );

	/* If the old layout doesn't match the new layout, update the post layout meta. */
	if ( $old_layout !== $new_layout )
		set_post_layout( $post_id, $new_layout );
}

?>