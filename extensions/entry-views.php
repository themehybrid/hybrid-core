<?php
/**
 * Entry Views - A WordPress script for counting post views.
 *
 * Entry views is a script for calculating the number of views a post gets.  It is meant to be basic and 
 * not a full-featured solution.  The idea is to allow theme/plugin authors to quickly load this file and 
 * build functions on top of it to suit their project needs.  This is an AJAX-based solution, so only visitors 
 * to your site with JavaScript enabled in their browser will update the view count.  It is possible to do this
 * without AJAX but not recommend (see notes below).
 *
 * By default, no post types are supported.  You have to register support for 'entry-views' for the post types
 * you wish to use this extension with.
 *
 * Not using AJAX: You can call up entry_views_update() at any time and pass it a post ID to update the 
 * count, but this has problems.  Any links with rel="next" or rel="prefetch" will cause some browsers to prefetch
 * the data for that particular page.  This can cause the view count to be skewed.  To try and avoid this 
 * issue, you need to disable/remove adjacent_posts_rel_link_wp_head().  However, this is not bullet-proof 
 * as it cannot control links it doesn't know about.
 * @link http://core.trac.wordpress.org/ticket/14568
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package EntryViews
 * @version 0.2.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2011, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add post type support for 'entry-views'. */
add_action( 'init', 'entry_views_post_type_support' );

/* Add the [entry-views] shortcode. */
add_shortcode( 'entry-views', 'entry_views_get' );

/* Registers the entry views extension scripts if we're on the correct page. */
add_action( 'template_redirect', 'entry_views_load' );

/* Add the entry views AJAX actions to the appropriate hooks. */
add_action( 'wp_ajax_entry_views', 'entry_views_update_ajax' );
add_action( 'wp_ajax_nopriv_entry_views', 'entry_views_update_ajax' );

/**
 * Adds support for 'entry-views' to the 'post', 'page', and 'attachment' post types (default WordPress 
 * post types).  For all other post types, the theme should explicitly register support for this feature.
 *
 * @since 0.2.0
 */
function entry_views_post_type_support() {

	/* Add support for entry-views to the 'post' post type. */
	add_post_type_support( 'post', array( 'entry-views' ) );

	/* Add support for entry-views to the 'page' post type. */
	add_post_type_support( 'page', array( 'entry-views' ) );

	/* Add support for entry-views to the 'attachment' post type. */
	add_post_type_support( 'attachment', array( 'entry-views' ) );
}

/**
 * Checks if we're on a singular post view and if the current post type supports the 'entry-views'
 * extension.  If so, set the $post_id variable and load the needed JavaScript.
 *
 * @since 0.1.0
 */
function entry_views_load() {
	global $entry_views;

	/* Check if we're on a singular post view. */
	if ( is_singular() ) {

		/* Get the post object. */
		$post = get_queried_object();

		/* Check if the post type supports the 'entry-views' feature. */
		if ( post_type_supports( $post->post_type, 'entry-views' ) ) {

			/* Set the post ID for later use because we wouldn't want a custom query to change this. */
			$entry_views->post_id = get_queried_object_id();

			/* Enqueue the jQuery library. */
			wp_enqueue_script( 'jquery' );

			/* Load the entry views JavaScript in the footer. */
			add_action( 'wp_footer', 'entry_views_load_scripts' );
		}
	}
}

/**
 * Updates the number of views when on a singular view of a post.  This function uses post meta to store
 * the number of views per post.  By default, the meta key is 'Views', but you can filter this with the 
 * 'entry_views_meta_key' hook.
 *
 * @since 0.1.0
 */
function entry_views_update( $post_id = '' ) {

	/* If we're on a singular view of a post, calculate the number of views. */
	if ( !empty( $post_id ) ) {

		/* Allow devs to override the meta key used. By default, this is 'Views'. */
		$meta_key = apply_filters( 'entry_views_meta_key', 'Views' );

		/* Get the number of views the post currently has. */
		$old_views = get_post_meta( $post_id, $meta_key, true );

		/* Add +1 to the number of current views. */
		$new_views = absint( $old_views ) + 1;

		/* Update the view count with the new view count. */
		update_post_meta( $post_id, $meta_key, $new_views, $old_views );
	}
}

/**
 * Gets the number of views a specific post has.  It also doubles as a shortcode, which is called with the 
 * [entry-views] format.
 *
 * @since 0.1.0
 * @param array $attr Attributes for use in the shortcode.
 */
function entry_views_get( $attr = '' ) {

	/* Merge the defaults and the given attributes. */
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'post_id' => get_the_ID() ), $attr );

	/* Allow devs to override the meta key used. */
	$meta_key = apply_filters( 'entry_views_meta_key', 'Views' );

	/* Get the number of views the post has. */
	$views = intval( get_post_meta( $attr['post_id'], $meta_key, true ) );

	/* Returns the formatted number of views. */
	return $attr['before'] . number_format_i18n( $views ) . $attr['after'];
}

/**
 * Callback function hooked to 'wp_ajax_entry_views' and 'wp_ajax_nopriv_entry_views'.  It checks the
 * AJAX nonce and passes the given $post_id to the entry views update function.
 *
 * @since 0.1.0
 */
function entry_views_update_ajax() {

	/* Check the AJAX nonce to make sure this is a valid request. */
	check_ajax_referer( 'entry_views_ajax' );

	/* If the post ID is set, set it to the $post_id variable and make sure it's an integer. */
	if ( isset( $_POST['post_id'] ) )
		$post_id = absint( $_POST['post_id'] );

	/* If $post_id isn't empty, pass it to the entry_views_update() function to update the view count. */
	if ( !empty( $post_id ) )
		entry_views_update( $post_id );
}

/**
 * Displays a small script that sends an AJAX request for the page.  It passes the $post_id to the AJAX 
 * callback function for updating the meta.
 *
 * @since 0.1.0
 */
function entry_views_load_scripts() {
	global $entry_views;

	/* Create a nonce for the AJAX request. */
	$nonce = wp_create_nonce( 'entry_views_ajax' );

	/* Display the JavaScript needed. */
	echo '<script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready( function() { jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", { action : "entry_views", _ajax_nonce : "' . $nonce . '", post_id : ' . $entry_views->post_id . ' } ); } ); /* ]]> */</script>' . "\n";
}

?>