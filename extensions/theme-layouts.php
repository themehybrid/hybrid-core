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
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package ThemeLayouts
 * @version 0.3.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2011, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filters the body_class hook to add a custom class. */
add_filter( 'body_class', 'theme_layouts_body_class' );

/* Set up the custom post layouts. */
add_action( 'admin_menu', 'theme_layouts_admin_setup' );

/**
 * Gets the layout for the current post based off the 'Layout' custom field key if viewing a singular post 
 * entry.  All other pages are given a default layout of 'layout-default'.
 *
 * @since 0.2.0
 * @return string The layout for the given page.
 */
function theme_layouts_get_layout() {

	/* Get the available post layouts. */
	$post_layouts = get_theme_support( 'theme-layouts' );

	/* Set the layout to an empty string. */
	$layout = '';

	/* If viewing a singular post, check if a layout has been specified. */
	if ( is_singular() ) {

		/* Get the current post ID. */
		$post_id = get_queried_object_id();

		/* Get the post layout. */
		$layout = get_post_layout( $post_id );
	}

	/* If viewing a user/author archive, check if a layout has been specified. */
	elseif ( is_author() ) {

		/* Get the current user ID. */
		$user_id = get_queried_object_id();

		/* Get the user layout. */
		$layout = get_user_layout( $user_id );
	}

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( empty( $layout ) || !in_array( $layout, $post_layouts[0] ) )
		$layout = 'default';

	/* @deprecated 0.2.0. Use the 'get_theme_layout' hook. */
	$layout = apply_filters( 'get_post_layout', "layout-{$layout}" );

	/* Return the layout and allow plugin/theme developers to override it. */
	return esc_attr( apply_filters( 'get_theme_layout', $layout ) );
}

/**
 * Get the post layout based on the given post ID.
 *
 * @since 0.2.0
 * @param int $post_id The ID of the post to get the layout for.
 * @return string $layout The name of the post's layout.
 */
function get_post_layout( $post_id ) {

	/* Get the post layout. */
	$layout = get_post_meta( $post_id, apply_filters( 'theme_layouts_meta_key', 'Layout' ), true );

	/* Return the layout if one is found.  Otherwise, return 'default'. */
	return ( !empty( $layout ) ? $layout : 'default' );
}

/**
 * Update/set the post layout based on the given post ID and layout.
 *
 * @since 0.2.0
 * @param int $post_id The ID of the post to set the layout for.
 * @param string $layout The name of the layout to set.
 * @return bool The return value of the update_post_meta() function.
 */
function set_post_layout( $post_id, $layout ) {
	return update_post_meta( $post_id, apply_filters( 'theme_layouts_meta_key', 'Layout' ), $layout );
}

/**
 * Checks if a specific post's layout matches that of the given layout.
 *
 * @since 0.3.0
 * @param string $layout The name of the layout to check if the post has.
 * @param int $post_id The ID of the post to check the layout for.
 * @return bool Whether the given layout matches the post's layout.
 */
function has_post_layout( $layout, $post_id = '' ) {

	/* If no post ID is given, use WP's get_the_ID() to get it and assume we're in the post loop. */
	if ( empty( $post_id ) )
		$post_id = get_the_ID();

	/* Return true/false based on whether the layout matches. */
	return ( $layout == get_post_layout( $post_id ) ? true : false );
}

/**
 * Get the layout for a user/author archive page based on a specific user ID.
 *
 * @since 0.3.0
 * @param int $user_id The ID of the user to get the layout for.
 */
function get_user_layout( $user_id ) {

	/* Get the user layout. */
	$layout = get_user_meta( $user_id, apply_filters( 'theme_layouts_meta_key', 'Layout' ), true );

	/* Return the layout if one is found.  Otherwise, return 'default'. */
	return ( !empty( $layout ) ? $layout : 'default' );
}

/**
 * Update/set the layout for a user/author archive paged based on the user ID.
 *
 * @since 0.3.0
 * @param int $user_id The ID of the user to set the layout for.
 * @param string $layout The name of the layout to set.
 * @return bool The return value of update_user_meta() function.
 */
function set_user_layout( $user_id, $layout ) {
	return update_user_meta( $user_id, apply_filters( 'theme_layouts_meta_key', 'Layout' ), $layout );
}

/**
 * Checks if a specific user's layout matches that of the given layout.
 *
 * @since 0.3.0
 * @param string $layout The name of the layout to check if the user has.
 * @param int $user_id The ID of the user to check the layout for.
 * @return bool Whether the given layout matches the user's layout.
 */
function has_user_layout( $layout, $user_id = '' ) {

	/* If no user ID is given, assume we're viewing an author archive page and get the user ID. */
	if ( empty( $user_id ) )
		$user_id = get_query_var( 'author' );

	/* Return true/false based on whether the layout matches. */
	return ( $layout == get_user_layout( $user_id ) ? true : false );
}

/**
 * Adds the post layout class to the WordPress body class in the form of "layout-$layout".  This allows 
 * theme developers to design their theme layouts based on the layout class.  If designing a theme with 
 * this extension, the theme should make sure to handle all possible layout classes.
 *
 * @since 0.2.0
 * @param array $classes
 * @param array $classes
 */
function theme_layouts_body_class( $classes ) {

	/* Adds the layout to array of body classes. */
	$classes[] = sanitize_html_class( theme_layouts_get_layout() );

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
function theme_layouts_strings() {

	/* Set up the default layout strings. */
	$strings = array(
		'default' => 	__( 'Default', theme_layouts_textdomain() ),
		'1c' => 		__( 'One Column', theme_layouts_textdomain() ),
		'2c-l' => 		__( 'Two Columns, Left', theme_layouts_textdomain() ),
		'2c-r' => 	__( 'Two Columns, Right', theme_layouts_textdomain() ),
		'3c-l' => 		__( 'Three Columns, Left', theme_layouts_textdomain() ),
		'3c-r' => 	__( 'Three Columns, Right', theme_layouts_textdomain() ),
		'3c-c' => 	__( 'Three Columns, Center', theme_layouts_textdomain() )
	);

	/* Allow devs to filter the strings for custom layouts. */
	return apply_filters( 'theme_layouts_strings', $strings );
}

/**
 * Get a specific layout's text string.
 *
 * @since 0.2.0
 */
function theme_layouts_get_string( $layout ) {

	/* Get an array of post layout strings. */
	$strings = theme_layouts_strings();

	/* Return the layout's string if it exists. Else, return the layout slug. */
	return ( ( isset( $strings[$layout] ) ) ? $strings[$layout] : $layout );
}

/**
 * Post layouts admin setup.  Registers the post layouts meta box for the post editing screen.  Adds the 
 * metadata save function to the 'save_post' hook.
 *
 * @since 0.2.0
 */
function theme_layouts_admin_setup() {

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type )
		add_meta_box( 'theme-layouts-post-meta-box', __( 'Layout', theme_layouts_textdomain() ), 'theme_layouts_post_meta_box', $type->name, 'side', 'default' );

	/* Saves the post format on the post editing page. */
	add_action( 'save_post', 'theme_layouts_save_post', 10, 2 );

	/* Adds a theme layout <select> element to the attachment edit form. */
	add_filter( 'attachment_fields_to_edit', 'theme_layouts_attachment_fields_to_edit', 10, 2 );

	/* Saves the theme layout for attachments. */
	add_filter( 'attachment_fields_to_save', 'theme_layouts_attachment_fields_to_save', 10, 2 );
}

/**
 * Displays a meta box of radio selectors on the post editing screen, which allows theme users to select 
 * the layout they wish to use for the specific post.
 *
 * @since 0.2.0
 */
function theme_layouts_post_meta_box( $post, $box ) {

	/* Get theme-supported theme layouts. */
	$layouts = get_theme_support( 'theme-layouts' );
	$post_layouts = $layouts[0];

	/* Get the current post's layout. */
	$post_layout = get_post_layout( $post->ID ); ?>

	<div class="post-layout">

		<input type="hidden" name="theme_layouts_post_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

		<p><?php _e( 'Layout is a theme-specific structure for the single view of the post.', theme_layouts_textdomain() ); ?></p>

		<div class="post-layout-wrap">
			<ul>
				<li><input type="radio" name="post_layout" id="post_layout_default" value="default" <?php checked( $post_layout, 'default' );?> /> <label for="post_layout_default"><?php echo esc_html( theme_layouts_get_string( 'default' ) ); ?></label></li>

				<?php foreach ( $post_layouts as $layout ) { ?>
					<li><input type="radio" name="post_layout" id="post_layout_<?php echo esc_attr( $layout ); ?>" value="<?php echo esc_attr( $layout ); ?>" <?php checked( $post_layout, $layout ); ?> /> <label for="post_layout_<?php echo esc_attr( $layout ); ?>"><?php echo esc_html( theme_layouts_get_string( $layout ) ); ?></label></li>
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
function theme_layouts_save_post( $post_id, $post ) {

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['theme_layouts_post_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['theme_layouts_post_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the previous post layout. */
	$old_layout = get_post_layout( $post_id );

	/* Get the submitted post layout. */
	$new_layout = esc_attr( $_POST['post_layout'] );

	/* If the old layout doesn't match the new layout, update the post layout meta. */
	if ( $old_layout !== $new_layout )
		set_post_layout( $post_id, $new_layout );
}

/**
 * Adds a select drop-down element to the attachment edit form for selecting the attachment layout.
 *
 * @since 0.3.0
 * @param array $fields Array of fields for the edit attachment form.
 * @param object $post The attachment post object.
 * @return array $fields
 */
function theme_layouts_attachment_fields_to_edit( $fields, $post ) {

	/* Get theme-supported theme layouts. */
	$layouts = get_theme_support( 'theme-layouts' );
	$post_layouts = $layouts[0];

	/* Get the current post's layout. */
	$post_layout = get_post_layout( $post->ID );

	/* Set the default post layout. */
	$select = '<option id="post_layout_default" value="default" ' . selected( $post_layout, 'default', false ) . '>' . esc_html( theme_layouts_get_string( 'default' ) ) . '</option>';

	/* Loop through each theme-supported layout, adding it to the select element. */
	foreach ( $post_layouts as $layout )
		$select .= '<option id="post_layout_' . esc_attr( $layout ) . '" value="' . esc_attr( $layout ) . '" ' . selected( $post_layout, $layout, false ) . '>' . esc_html( theme_layouts_get_string( $layout ) ) . '</option>';

	/* Set the HTML for the post layout select drop-down. */
	$select = '<select name="attachments[' . $post->ID . '][theme-layouts-post-layout]" id="attachments[' . $post->ID . '][theme-layouts-post-layout]">' . $select . '</select>';

	/* Add the attachment layout field to the $fields array. */
	$fields['theme-layouts-post-layout'] = array(
		'label' => __( 'Layout', theme_layouts_textdomain() ),
		'input' => 'html',
		'html' => $select
	);

	/* Return the $fields array back to WordPress. */
	return $fields;
}

/**
 * Saves the attachment layout for the attachment edit form.
 *
 * @since 0.3.0
 * @param array $post The attachment post array (not the post object!).
 * @param array $fields Array of fields for the edit attachment form.
 * @return array $post
 */
function theme_layouts_attachment_fields_to_save( $post, $fields ) {

	/* If the theme layouts field was submitted. */
	if ( isset( $fields['theme-layouts-post-layout'] ) ) {

		/* Get the previous post layout. */
		$old_layout = get_post_layout( $post['ID'] );

		/* Get the submitted post layout. */
		$new_layout = esc_attr( $fields['theme-layouts-post-layout'] );

		/* If the old layout doesn't match the new layout, update the post layout meta. */
		if ( $old_layout !== $new_layout )
			set_post_layout( $post['ID'], $new_layout );
	}

	/* Return the attachment post array. */
	return $post;
}

/**
 * Returns the textdomain used by the script and allows it to be filtered by plugins/themes.
 *
 * @since 0.2.0
 * @returns string The textdomain for the script.
 */
function theme_layouts_textdomain() {
	return apply_filters( 'theme_layouts_textdomain', 'theme-layouts' );
}

/**
 * @since 0.1.0
 * @deprecated 0.2.0 Use theme_layouts_get_layout().
 */
function post_layouts_get_layout() {
	return theme_layouts_get_layout();
}

?>