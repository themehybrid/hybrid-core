<?php
/**
 * Post Formats - Theme-defined styles/formats for posts.
 *
 * The post formats feature grants theme developers the ability to give users an easy-to-select format 
 * for their blog posts.  This makes neat things such as styling asides, links, quotes, and other formats much 
 * easier.  Developers can now add specific "formats" for these types of things without forcing users to 
 * create special categories.
 *
 * This feature was added to WordPress 3.1.  However, the core developers are intentionally limiting this 
 * feature to an "approved" list of formats in favor of standardization and portability.  Unfortunately, this
 * severely limits the flexibility for a feature that themes have been using for years.  This script was 
 * created to fix this issue by allowing developers and users to define custom formats if needed.
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

/* Switches 'post-formats' to 'custom-post-formats'. */
add_action( 'init', 'custom_post_formats_theme_support' );

/* Set up the custom post formats. */
add_action( 'admin_menu', 'custom_post_formats_admin_setup' );

/* Add the post format to the post states list. */
add_filter( 'display_post_states', 'custom_post_formats_display_post_states' );

/* Add some minor formatting CSS to the post editor. */
add_action( 'admin_head-post.php', 'custom_post_formats_admin_css' );

/**
 * This function switches theme-supported 'post-formats' to 'custom-post-formats'.  What this does
 * is take the supported 'post-formats' array and creates an array of 'custom-post-formats'.  It then
 * removes theme support for 'post-formats'.  This allows theme developers to use the built-in WordPress
 * way of doing things without having to learn a new system.
 *
 * @since 0.1.0
 */
function custom_post_formats_theme_support() {

	/* Check if the current theme supports 'post-formats'. */
	if ( current_theme_supports( 'post-formats' ) ) {

		/* Get the post formats the theme registered support for. */
		$post_formats = get_theme_support( 'post-formats' );

		/* Check if the post formats are in an array. */
		if ( is_array( $post_formats[0] ) ) {

			/* Add theme support for 'custom-post-formats' using the registered formats. */
			add_theme_support( 'custom-post-formats', $post_formats[0] );

			/* Remove theme support for 'post-formats'. */
			remove_theme_support( 'post-formats' );
		}
	}
}

/**
 * Adds the post formats meta box and hooks the save function to 'save_post'.
 *
 * @since 0.1.0
 */
function custom_post_formats_admin_setup() {

	/* Return early if 'custom-post-formats' is not supported. */
	if ( !current_theme_supports( 'custom-post-formats' ) )
		return;

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type ) {

		if ( post_type_supports( $type->name, 'post-formats' ) )
			add_meta_box( 'post-formats-meta-box', __( 'Post Format' ), 'custom_post_formats_meta_box', $type->name, 'side', 'default' );
	}

	/* Saves the post format on the post editing page. */
	add_action( 'save_post', 'custom_post_formats_save_post', 10, 2 );
}

/**
 * Displays the post formats meta box on the post editor screen in the admin.
 *
 * @since 0.1.0
 */
function custom_post_formats_meta_box( $post, $box ) {

	/* Check if the current post type supports 'post-formats' before displaying. */
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {

		/* Get the post formats supported by the theme. */
		$post_formats = get_theme_support( 'custom-post-formats' );

		/* Make sure the available post formats are an array. */
		if ( is_array( $post_formats[0] ) ) {

			/* Get the current post format for the post. */
			$post_format = get_post_format( $post->ID );

			/* If there's no post format, assume it's the default. */
			if ( empty( $post_format ) )
				$post_format = '0';

			/* If the post format's not one of theme-supported formats, add it to the formats array. */
			elseif ( !in_array( $post_format, $post_formats[0] ) )
				$post_formats[0][] = $post_format; ?>

			<div class="post-format">

				<p><?php _e( 'Post formats are theme-defined formatting for specific types of posts.' ); ?></p>

				<input type="hidden" name="custom_post_formats_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />

				<div class="post-format-wrap">
				<ul>
					<li><input type="radio" name="post_format" id="post_format_default" value="0" <?php checked( $post_format, '0' );?>> <label for="post_format_default"><?php echo esc_html( get_custom_post_format_string( '0' ) ); ?></label></li>

					<?php foreach ( $post_formats[0] as $format ) { ?>
						<li><input type="radio" name="post_format" id="post_format_<?php echo esc_attr( $format ); ?>" value="<?php echo esc_attr( $format ); ?>" <?php checked( $post_format, $format ); ?>> <label for="post_format_<?php echo esc_attr( $format ); ?>"><?php echo esc_html( get_custom_post_format_string( $format ) ); ?></label></li>
					<?php } ?>
				</ul>
				</div>

			</div><?php
		}
	}
}

/**
 * Gets the 'pretty' name (i.e. label, string) for the post format.  If no format string is set, return the slug
 * as the string.
 *
 * @since 0.1.0
 */
function get_custom_post_format_string( $slug ) {
	$strings = get_custom_post_format_strings();
	return ( isset( $strings[$slug] ) ) ? $strings[$slug] : $slug;
}

/**
 * Uses the built-in WordPress formats as the default but allows developers to filter the strings to add new
 * formats.  The core devs could've added this single hook to core and it would've made this entire post 
 * formats script unnecessary.
 *
 * @since 0.1.0
 */
function get_custom_post_format_strings() {
	return apply_filters( 'post_format_strings', get_post_format_strings() );
}

/**
 * Saves the post format for the post when the post is updated.
 *
 * @since 0.1.0
 */
function custom_post_formats_save_post( $post_id, $post ) {

	/* Check that the current post type supports 'custom-post-formats'. */
	if ( !post_type_supports( $post->post_type, 'post-formats' ) )
		return $post_id;

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['custom_post_formats_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['custom_post_formats_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the current post format. */
	$old_format = get_post_format( $post );

	/* Get the user-selected post format. */
	$new_format = esc_attr( $_POST['post_format'] );

	/* If the old format doesn't match the new format, update post format. */
	if ( $old_format !== $new_format )
		set_post_format( $post, $new_format );
}

/**
 * Adds the post format to the post states list on the edit posts screen in the admin.  This function also 
 * removes the post format text added by WordPress since it doesn't support custom format strings.
 *
 * @since 0.1.0
 */
function custom_post_formats_display_post_states( $states ) {
	global $post;

	/* Return if 'custom-post-formats' is not supported. */
	if ( !current_theme_supports( 'custom-post-formats' ) )
		return $states;

	/* Get the current post's format. */
	$format = get_post_format( $post );

	/* If the post has a format, continue. */
	if ( !empty( $format ) ) {

		// http://core.trac.wordpress.org/ticket/15421
		//$states['post_format'] = '<span>[</span>' . get_custom_post_format_string( $format ) . '<span>]</span>';

		/* Create a search string to match WordPress' post format string. */
		$search = '<span>[</span>' . get_post_format_string( $format ) . '<span>]</span>';

		/* Search the post display states for the post format array key. */
		$format_key = array_search( $search, $states );

		/* If the post format was found, remove it from the display states array. */
		if ( isset( $format_key ) )
			unset( $states[$format_key] );

		/* Add a custom post format string. */
		$states[] = '<span>[</span>' . get_custom_post_format_string( $format ) . '<span>]</span>';
	}

	return $states;
}

/**
 * Some minor CSS so that themes with many formats have a scrollbar to avoid an overly large meta box.
 *
 * @since 0.1.0
 */
function custom_post_formats_admin_css() {

	/* Return if 'custom-post-formats' is not supported. */
	if ( !current_theme_supports( 'custom-post-formats' ) )
		return; ?>

	<style type="text/css">.post-format-wrap { padding: 0 5px; max-height: 170px; overflow-y: auto; }</style>
<?php }

?>