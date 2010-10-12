<?php
/**
 * Post Layouts was created to allow theme developers to easily style themes with post-specific layout 
 * structures.  It gives users the ability to control how each post (or any post type) is displayed on the 
 * front end of the site.
 *
 * @copyright 2010
 * @version 0.1.0
 * @author Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostLayouts
 */

/* Filters the body_class hook to add a custom class. */
add_filter( 'body_class', 'post_layouts_body_class' );

/**
 * Gets the layout for the current page.
 *
 * @since 0.1.0
 */
function post_layouts_get_layout() {
	global $wp_query;

	/* Set the layout to 'default'. */
	$layout = 'default';

	/* If viewing a singular post, check if a layout has been specified. */
	if ( is_singular() ) {

		/* Get the current post ID. */
		$post_id = $wp_query->get_queried_object_id();

		/* Allow plugin/theme developers to override the default meta key used. */
		$meta_key = apply_filters( 'post_layouts_meta_key', 'Layout' );

		/* Check the post metadata for the layout. */
		$post_layout = get_post_meta( $post_id, $meta_key, true );

		/* If a layout was found, assign it to the $layout variable. */
		if ( !empty( $post_layout ) )
			$layout = esc_attr( $post_layout );
	}

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( !in_array( $layout, post_layouts_available() ) )
		$layout = 'default';

	/* Return the layout and allow plugin/theme developers to override it. */
	return apply_filters( 'get_post_layout', "layout-{$layout}" );
}

function post_layouts_body_class( $classes ) {

	$classes[] = post_layouts_get_layout();

	return $classes;
}

function post_layouts_available() {

	$layouts = array(
		'1c',
		'2c-l',
		'2c-r',
		'3c-l',
		'3c-r',
		'3c-c'
	);

	return apply_filters( 'post_layouts_available', $layouts );
}

?>