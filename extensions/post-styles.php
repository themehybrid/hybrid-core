<?php
/**
 * Post Schemes - Theme-defined schemes/formats for posts.
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

/* Register the 'post_scheme' taxonomy. */
add_action( 'init', 'post_schemes_register_taxonomies' );

/* Removes support of 'post-formats'. */
add_action( 'init', 'post_schemes_theme_support' );

/* Sets up the admin functionality. */
add_action( 'admin_menu', 'post_schemes_admin_setup' );

/* Adds a post scheme class. */
add_filter( 'post_class', 'post_schemes_post_class' );

/**
 * Registers the 'post_scheme' taxonomy and adds post type support of the 'post-schemes' feature.
 *
 * @since 0.1.0
 */
function post_schemes_register_taxonomies() {

	/* Add support for the 'post-schemes' feature to the 'post' post type. */
	add_post_type_support( 'post', 'post-schemes' );

	$args = array(
		'labels' => array(
			'name' => __( 'Post Schemes' ),
			'singular_name' => __( 'Post Scheme' )
		),
		'rewrite' => array(
			'slug' => 'schemes',
			'with_front' => true
		),
		'hierarchical' => false,
		'query_var' => 'post_scheme',
		'public' => true,
		'show_ui' => false,
		'show_in_nav_menus' => false
	);

	/* Register the 'post_scheme' taxonomy. */
	register_taxonomy( 'post_scheme', array( 'post' ), apply_filters( 'post_schemes_taxonomy_args', $args ) );
}

/**
 * Function for deregistering the WordPress post formats feature if a theme adds support for it.  Post 
 * formats and post schemes should not be used together.  Theme developers should choose one or the other
 * for the theme.
 *
 * @since 0.1.0
 */
function post_schemes_theme_support() {

	/* Remove theme support for 'post-formats'. */
	remove_theme_support( 'post-formats' );
}

/**
 * Gets the post's scheme, which is technically a term of the 'post_scheme' taxonomy.
 *
 * @since 0.1.0
 */
function get_post_scheme( $post = null ) {

	/* Get the post object. */
	$post = get_post( $post );

	/* Get the post schemes for the post. */
	$post_scheme = get_the_terms( $post->ID, 'post_scheme' );

	/* Get the first term from the post schemes array. */
	if ( !empty( $post_scheme ) ) {
		$scheme = array_shift( $post_scheme );

		/* Replace the 'post-scheme-' prefix with an empty string and return the scheme. */
		return ( str_replace( 'post-scheme-', '', $scheme->name ) );
	}

	/* If no post scheme is found, return the post format. */
	if ( empty( $post_scheme ) ) {
		$format = ( function_exists( 'get_post_format' ) ? get_post_format( $post ) : false );

		if  ( !empty( $format ) ) {
			set_post_scheme( $post, $format );
			return $format;
		}
	}

	return false;
}

/**
 * Sets the given scheme as a term of the post_scheme taxonomy for the post.
 *
 * @since 0.1.0
 */
function set_post_scheme( $post, $scheme ) {

	/* Get the post object. */
	$post = get_post( $post );

	/* If no post is found, return false. */
	if ( empty( $post ) )
		return false;

	if ( function_exists( 'get_post_format_strings' ) ) {
		$format_strings = get_post_format_strings();

		if ( !empty( $scheme ) && array_key_exists( $scheme, $format_strings ) )
			set_post_format( $post, $scheme );
		else
			set_post_format( $post, false );
	}

	/* Prefix the scheme with 'post-scheme-' and sanitize the input scheme name. */
	$scheme = 'post-scheme-' . sanitize_key( $scheme );

	/* Set the scheme for the post as a term of the 'post_scheme' taxonomy. */
	return wp_set_post_terms( $post->ID, $scheme, 'post_scheme' );
}

/**
 * Checks if a post has a specific scheme and returns true/false.
 *
 * @since 0.1.0
 */
function has_post_scheme( $scheme, $post = null ) {
	return has_term( 'post-scheme-' . sanitize_key( $scheme ), 'post_scheme', $post );
}

/**
 * Adds the post scheme to the post CSS class.
 *
 * @since 0.1.0
 */
function post_schemes_post_class( $classes ) {
	global $post;

	$post_scheme = get_post_scheme( $post );

	$classes[] = ( !empty( $post_scheme ) ? 'post-scheme-' . sanitize_html_class( $post_scheme ) : 'post-scheme-default' );

	return $classes;
}


add_filter( 'term_links-post_scheme', 'post_schemes_term_links' );

function post_schemes_term_links( $term_links ) {
	return preg_replace( '/>(.*?)<\/a>/e', "'>' . post_scheme_convert_scheme_name( '\\1' ) . '</a>'", $term_links );
}

function post_scheme_convert_scheme_name( $scheme ) {

	$scheme = str_replace( 'post-scheme-', '', $scheme );
	return get_post_scheme_string( $scheme );
}

add_filter( 'single_term_title', 'post_schemes_single_term_title' );

function post_schemes_single_term_title( $term_name ) {

	if ( strstr( $term_name, 'post-scheme-' ) )
		$term_name = post_scheme_convert_scheme_name( $term_name );

	return $term_name;
}

add_filter( 'term_link', 'post_schemes_term_link', 10, 3 );

function post_schemes_term_link( $link, $term, $taxonomy ) {
	global $wp_rewrite;
	if ( 'post_scheme' != $taxonomy )
		return $link;
	$slugs = get_post_scheme_slugs();
	if ( $wp_rewrite->get_extra_permastruct( $taxonomy ) ) {
		return str_replace( "/{$term->slug}", '/' . $slugs[ str_replace( 'post-scheme-', '', $term->slug ) ], $link );
	} else {
		$link = remove_query_arg( 'scheme', $link );
		return add_query_arg( 'scheme', str_replace( 'post-scheme-', $term->slug ), $link );
	}
}

add_filter( 'request', 'post_schemes_request' );

function post_schemes_request( $qvs ) {
	if ( ! isset( $qvs['post_scheme'] ) )
		return $qvs;
	$slugs = array_flip( get_post_scheme_slugs() );
	if ( isset( $slugs[ $qvs['post_scheme'] ] ) )
		$qvs['post_scheme'] = 'post-scheme-' . $slugs[ $qvs['post_scheme'] ];
	return $qvs;
}

function get_post_scheme_slugs() {

	$textdomain = post_schemes_textdomain();

	$strings = array(
		'default' => 	_x( 'default', 'Post scheme slug', $textdomain ),
		'aside' => 	_x( 'aside', 'Post scheme slug', $textdomain ),
		'audio' => 	_x( 'audio', 'Post scheme slug', $textdomain ),
		'chat' => 		_x( 'chat', 'Post scheme slug', $textdomain ),
		'code' => 	_x( 'code', 'Post scheme slug', $textdomain ),
		'document' => 	_x( 'document', 'Post scheme slug', $textdomain ),
		'download' => 	_x( 'download', 'Post scheme slug', $textdomain ),
		'gallery' => 	_x( 'gallery', 'Post scheme slug', $textdomain ),
		'image' => 	_x( 'image', 'Post scheme slug', $textdomain ),
		'link' => 		_x( 'link', 'Post scheme slug', $textdomain ),
		'list' => 		_x( 'list', 'Post scheme slug', $textdomain ),
		'quote' => 	_x( 'quote', 'Post scheme slug', $textdomain ),
		'review' => 	_x( 'review', 'Post scheme slug', $textdomain ),
		'slideshow' => 	_x( 'slideshow', 'Post scheme slug', $textdomain ),
		'status' => 	_x( 'status', 'Post scheme slug', $textdomain ),
		'video' => 	_x( 'video', 'Post scheme slug', $textdomain ),
	);

	return apply_filters( 'post_schemes_slugs', $strings );
}

/**
 * Gets the strings/label for the post scheme.  If no string is found, the given slug is used.
 *
 * @since 0.1.0
 */
function get_post_scheme_string( $slug ) {
	$strings = get_post_scheme_strings();
	return ( isset( $strings[$slug] ) ) ? $strings[$slug] : $slug;
}

/**
 * Returns an array of the default post scheme text strings/labels.
 *
 * @since 0.1.0
 */
function get_post_scheme_strings() {

	$textdomain = post_schemes_textdomain();

	$strings = array(
		'default' => 	_x( 'Default', 'Post scheme', $textdomain ),
		'aside' => 	_x( 'Aside', 'Post scheme', $textdomain ),
		'audio' => 	_x( 'Audio', 'Post scheme', $textdomain ),
		'chat' => 		_x( 'Chat', 'Post scheme', $textdomain ),
		'code' => 	_x( 'Code', 'Post scheme', $textdomain ),
		'document' => 	_x( 'Document', 'Post scheme', $textdomain ),
		'download' => 	_x( 'Download', 'Post scheme', $textdomain ),
		'gallery' => 	_x( 'Gallery', 'Post scheme', $textdomain ),
		'image' => 	_x( 'Image', 'Post scheme', $textdomain ),
		'link' => 		_x( 'Link', 'Post scheme', $textdomain ),
		'list' => 		_x( 'List', 'Post scheme', $textdomain ),
		'quote' => 	_x( 'Quote', 'Post scheme', $textdomain ),
		'review' => 	_x( 'Review', 'Post scheme', $textdomain ),
		'slideshow' => 	_x( 'Slideshow', 'Post scheme', $textdomain ),
		'status' => 	_x( 'Status', 'Post scheme', $textdomain ),
		'video' => 	_x( 'Video', 'Post scheme', $textdomain ),
	);

	return apply_filters( 'post_schemes_strings', $strings );
}

/**
 * Adds the post formats meta box and hooks the save function to 'save_post'.
 *
 * @since 0.1.0
 */
function post_schemes_admin_setup() {

	/* Return early if 'custom-post-formats' is not supported. */
	if ( !current_theme_supports( 'post-schemes' ) )
		return;

	/* Filter the post states list on the edit posts screen. */
	add_filter( 'display_post_states', 'post_schemes_display_post_states' );

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type ) {

		if ( post_type_supports( $type->name, 'post-schemes' ) )
			add_meta_box( 'post-schemes-meta-box', __( 'Post Scheme' ), 'post_schemes_meta_box', $type->name, 'side', 'default' );
	}

	/* Saves the post format on the post editing page. */
	add_action( 'save_post', 'post_schemes_save_post', 10, 2 );
}

/**
 * Adds the post scheme to the list of post states afther the title on the edit posts screen in the admin.
 *
 * @since 0.1.0
 */
function post_schemes_display_post_states( $states ) {
	global $post;

	if ( !current_theme_supports( 'post-schemes' ) )
		return $states;

	$post_scheme = get_post_scheme( $post );

	if ( !empty( $post_scheme ) )
		$states[] = '<span>[</span>' . get_post_scheme_string( $post_scheme ) . '<span>]</span>';

	return $states;
}

/**
 * Displays the post formats meta box on the post editor screen in the admin.
 *
 * @since 0.1.0
 */
function post_schemes_meta_box( $post, $box ) {

	/* Check if the current post type supports 'post-formats' before displaying. */
	if ( post_type_supports( $post->post_type, 'post-schemes' ) ) {

		/* Get the post formats supported by the theme. */
		$post_schemes = get_theme_support( 'post-schemes' );

		/* Make sure the available post formats are an array. */
		if ( is_array( $post_schemes[0] ) ) {

			/* Get the current post scheme. */
			$post_scheme = get_post_scheme( $post );

			/* If there's no post format, assume it's the default. */
			if ( empty( $post_scheme ) )
				$post_scheme = 'default';

			/* If the post format's not one of theme-supported formats, add it to the formats array. */
			elseif ( !in_array( $post_scheme, $post_schemes[0] ) )
				$post_schemes[0][] = $post_scheme; ?>

			<div class="custom-post-format">

				<p><?php _e( 'Post schemes are theme-defined formatting for specific types of posts.' ); ?></p>

				<input type="hidden" name="post_schemes_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

				<div class="custom-post-format-wrap">
				<ul>
					<li><input type="radio" name="post_scheme" id="post_scheme_default" value="default" <?php checked( $post_scheme, 'default' );?> /> <label for="post_scheme_default"><?php echo esc_html( get_post_scheme_string( 'default' ) ); ?></label></li>

					<?php foreach ( $post_schemes[0] as $scheme ) { ?>
						<li><input type="radio" name="post_scheme" id="post_scheme_<?php echo esc_attr( $scheme ); ?>" value="<?php echo esc_attr( $scheme ); ?>" <?php checked( $post_scheme, $scheme ); ?> /> <label for="post_scheme_<?php echo esc_attr( $scheme ); ?>"><?php echo esc_html( get_post_scheme_string( $scheme ) ); ?></label></li>
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
function post_schemes_save_post( $post_id, $post ) {

	/* Check that the current post type supports 'custom-post-formats'. */
	if ( !post_type_supports( $post->post_type, 'post-schemes' ) )
		return $post_id;

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['post_schemes_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['post_schemes_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the current post format. */
	$old_scheme = get_post_scheme( $post );

	/* Get the user-selected post format. */
	$new_scheme = esc_attr( $_POST['post_scheme'] );

	if ( 'default' == $new_scheme || empty( $new_scheme ) )
		set_post_scheme( $post, false );

	elseif ( $old_scheme !== $new_scheme )
		set_post_scheme( $post, $new_scheme );
}

/**
 * Textdomain for the post schemes extension.  If included in an internationalized themes, the theme 
 * dev can easily filter this to match the theme's textdomain.
 *
 * @since 0.1.0
 */
function post_schemes_textdomain() {
	return apply_filters( 'post_schemes_textdomain', 'post-schemes' );
}

?>