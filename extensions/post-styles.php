<?php
/**
 * Post Styles - Theme-defined styles/formats for posts.
 *
 * Long description.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostFormats
 * @version 0.1.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register the 'post_style' taxonomy. */
add_action( 'init', 'post_styles_register_taxonomies' );

/* Removes support of 'post-formats'. */
add_action( 'init', 'post_styles_theme_support' );

/* Sets up the admin functionality. */
add_action( 'admin_menu', 'post_styles_admin_setup' );

/* Adds a post style class. */
add_filter( 'post_class', 'post_styles_post_class' );

/**
 * Registers the 'post_style' taxonomy and adds post type support of the 'post-styles' feature.
 *
 * @since 0.1.0
 */
function post_styles_register_taxonomies() {

	/* Add support for the 'post-styles' feature to the 'post' post type. */
	add_post_type_support( 'post', 'post-styles' );

	/* Register the 'post_style' taxonomy. */
	register_taxonomy(
		'post_style',
		array( 'post' ),
		array(
			'labels' => array(
				'name' => __( 'Post Styles' ),
				'singular_name' => __( 'Post Style' )
			),
			'rewrite' => array(
				'slug' => 'styles',
				'with_front' => false
			),
			'hierarchical' => false,
			'query_var' => 'post_style',
			'show_ui' => false,
			'show_in_nav_menus' => false
		)
	);
}

/**
 * Function for deregistering the WordPress post formats feature if a theme adds support for it.  Post 
 * formats and post styles should not be used together.  Theme developers should choose one or the other
 * for the theme.
 *
 * @since 0.1.0
 */
function post_styles_theme_support() {

	/* Remove theme support for 'post-formats'. */
	remove_theme_support( 'post-formats' );
}

/**
 * Gets the post's style, which is technically a term of the 'post_style' taxonomy.
 *
 * @since 0.1.0
 */
function get_post_style( $post = null ) {

	/* Get the post object. */
	$post = get_post( $post );

	/* Get the post styles for the post. */
	$post_style = get_the_terms( $post->ID, 'post_style' );

	/* If no post style is found, return the post format. */
	if ( empty( $post_style ) )
		return ( function_exists( 'get_post_format' ) ? get_post_format( $post ) : false );

	/* Get the first term from the post styles array. */
	$style = array_shift( $post_style );

	/* Replace the 'post-style-' prefix with an empty string and return the style. */
	return ( str_replace( 'post-style-', '', $style->name ) );
}

/**
 * Sets the given style as a term of the post_style taxonomy for the post.
 *
 * @since 0.1.0
 */
function set_post_style( $post, $style ) {

	/* Get the post object. */
	$post = get_post( $post );

	/* If no post is found, return false. */
	if ( empty( $post ) )
		return false;

	if ( function_exists( 'get_post_format_strings' ) ) {
		$format_strings = get_post_format_strings();

		if ( !empty( $style ) && array_key_exists( $style, $format_strings ) )
			set_post_format( $post, $style );
		else
			set_post_format( $post, false );
	}

	/* Prefix the style with 'post-style-' and sanitize the input style name. */
	$style = 'post-style-' . sanitize_key( $style );

	/* Set the style for the post as a term of the 'post_style' taxonomy. */
	return wp_set_post_terms( $post->ID, $style, 'post_style' );
}

/**
 * Checks if a post has a specific style and returns true/false.
 *
 * @since 0.1.0
 */
function has_post_style( $style, $post = null ) {
	return has_term( 'post-style-' . sanitize_key( $style ), 'post_style', $post );
}

/**
 * Adds the post style to the post CSS class.
 *
 * @since 0.1.0
 */
function post_styles_post_class( $classes ) {
	global $post;

	$post_style = get_post_style( $post );

	$classes[] = ( !empty( $post_style ) ? 'post-style-' . sanitize_html_class( $post_style ) : 'post-style-default' );

	return $classes;
}

/**
 * Gets the strings/label for the post style.  If no string is found, the given slug is used.
 *
 * @since 0.1.0
 */
function get_post_style_string( $slug ) {
	$strings = get_post_style_strings();
	return ( isset( $strings[$slug] ) ) ? $strings[$slug] : '';
}

/**
 * Returns an array of the default post style text strings/labels.
 *
 * @since 0.1.0
 */
function get_post_style_strings() {

	$strings = array(
		'default' => 	_x( 'Default', 'Post style' ),
		'aside' => 	_x( 'Aside', 'Post style' ),
		'audio' => 	_x( 'Audio', 'Post style' ),
		'chat' => 		_x( 'Chat', 'Post style' ),
		'code' => 	_x( 'Code', 'Post style' ),
		'document' => 	_x( 'Document', 'Post style' ),
		'gallery' => 	_x( 'Gallery', 'Post style' ),
		'image' => 	_x( 'Image', 'Post style' ),
		'link' => 		_x( 'Link', 'Post style' ),
		'list' => 		_x( 'List', 'Post style' ),
		'portfolio' => 	_x( 'Portfolio', 'Post style' ),
		'quote' => 	_x( 'Quote', 'Post style' ),
		'review' => 	_x( 'Review', 'Post style' ),
		'slideshow' => 	_x( 'Slideshow', 'Post style' ),
		'status' => 	_x( 'Status', 'Post style' ),
		'video' => 	_x( 'Video', 'Post style' ),
	);

	return apply_filters( 'post_styles_strings', $strings );
}

/**
 * Adds the post formats meta box and hooks the save function to 'save_post'.
 *
 * @since 0.1.0
 */
function post_styles_admin_setup() {

	/* Return early if 'custom-post-formats' is not supported. */
	if ( !current_theme_supports( 'post-styles' ) )
		return;

	/* Filter the post states list on the edit posts screen. */
	add_filter( 'display_post_states', 'post_styles_display_post_states' );

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type ) {

		if ( post_type_supports( $type->name, 'post-styles' ) )
			add_meta_box( 'post-styles-meta-box', __( 'Post Style' ), 'post_styles_meta_box', $type->name, 'side', 'default' );
	}

	/* Saves the post format on the post editing page. */
	add_action( 'save_post', 'post_styles_save_post', 10, 2 );
}

/**
 * Adds the post style to the list of post states afther the title on the edit posts screen in the admin.
 *
 * @since 0.1.0
 */
function post_styles_display_post_states( $states ) {
	global $post;

	if ( !current_theme_supports( 'post-styles' ) )
		return $states;

	$post_style = get_post_style( $post );

	if ( !empty( $post_style ) ) {

		/* Get the current post's format. */
		$format = ( function_exists( 'get_post_format' ) ? get_post_format( $post ) : false );

		$format_string = ( function_exists( 'get_post_format_string' ) ? get_post_format_string( $format ) : false );

		if ( empty( $format ) || empty( $format_string ) )
			$states[] = '<span>[</span>' . get_post_style_string( $post_style ) . '<span>]</span>';
	}

	return $states;
}

/**
 * Displays the post formats meta box on the post editor screen in the admin.
 *
 * @since 0.1.0
 */
function post_styles_meta_box( $post, $box ) {

	/* Check if the current post type supports 'post-formats' before displaying. */
	if ( post_type_supports( $post->post_type, 'post-styles' ) ) {

		/* Get the post formats supported by the theme. */
		$post_styles = get_theme_support( 'post-styles' );

		/* Make sure the available post formats are an array. */
		if ( is_array( $post_styles[0] ) ) {

			/* Get the current post style. */
			$post_style = get_post_style( $post );

			/* If there's no post format, assume it's the default. */
			if ( empty( $post_style ) )
				$post_style = 'default';

			/* If the post format's not one of theme-supported formats, add it to the formats array. */
			elseif ( !in_array( $post_style, $post_styles[0] ) )
				$post_styles[0][] = $post_style; ?>

			<div class="custom-post-format">

				<p><?php _e( 'Post formats are theme-defined formatting for specific types of posts.' ); ?></p>

				<input type="hidden" name="post_styles_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

				<div class="custom-post-format-wrap">
				<ul>
					<li><input type="radio" name="post_style" id="post_style_default" value="default" <?php checked( $post_style, 'default' );?> /> <label for="post_style_default"><?php echo esc_html( get_post_style_string( 'default' ) ); ?></label></li>

					<?php foreach ( $post_styles[0] as $style ) { ?>
						<li><input type="radio" name="post_style" id="post_style_<?php echo esc_attr( $style ); ?>" value="<?php echo esc_attr( $style ); ?>" <?php checked( $post_style, $style ); ?> /> <label for="post_style_<?php echo esc_attr( $style ); ?>"><?php echo esc_html( get_post_style_string( $style ) ); ?></label></li>
					<?php } ?>
				</ul>
				</div>

			</div><?php
		}
	}
}

/**
 * Saves the post format for the post when the post is updated.
 *
 * @since 0.1.0
 */
function post_styles_save_post( $post_id, $post ) {

	/* Check that the current post type supports 'custom-post-formats'. */
	if ( !post_type_supports( $post->post_type, 'post-styles' ) )
		return $post_id;

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['post_styles_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['post_styles_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the current post format. */
	$old_style = get_post_style( $post );

	/* Get the user-selected post format. */
	$new_style = esc_attr( $_POST['post_style'] );

	if ( 'default' == $new_style || empty( $new_style ) )
		set_post_style( $post, false );

	elseif ( $old_style !== $new_style )
		set_post_style( $post, $new_style );
}

?>