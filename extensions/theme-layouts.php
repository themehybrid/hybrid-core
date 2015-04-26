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

final class Hybrid_Theme_Layouts {

	public $layouts = array();
	public $args = array();

	public function __construct() {

		/* Register metadata with WordPress. */
		add_action( 'init', array( $this, 'register_meta' ) );

		/* Add post type support for theme layouts. */
		add_action( 'init', array( $this, 'post_type_support' ) );

		/* Add layout option in Customize. */
		add_action( 'customize_register', array( $this, 'customize_register' ) );

		/* Filters the theme layout mod. */
		add_filter( 'theme_mod_theme_layout', array( $this, 'filter_layout' ) );

		/* Filters the body_class hook to add a custom class. */
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function register_meta() {
		register_meta( 'post', theme_layouts_get_meta_key(), 'sanitize_html_class' );
		register_meta( 'user', theme_layouts_get_meta_key(), 'sanitize_html_class' );
	}

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

	public function filter_layout( $theme_layout ) {

		/* If viewing a singular post, get the post layout. */
		if ( is_singular() )
			$layout = hybrid_get_post_layout( get_queried_object_id() );

		/* If viewing an author archive, get the user layout. */
		elseif ( is_author() )
			$layout = hybrid_get_user_layout( get_queried_object_id() );

		return !empty( $layout ) ? $layout : $theme_layout;
	}

	public function body_class( $classes ) {

		/* Adds the layout to array of body classes. */
		$classes[] = sanitize_html_class( 'layout-' . hybrid_get_layout() );

		/* Return the $classes array. */
		return $classes;
	}

	public function customize_register( $wp_customize ) {

		/* Get supported theme layouts. */
		$args = hybrid_get_layouts_args();

		if ( true === $args['customize'] ) {

			/* Add the layout section. */
			$wp_customize->add_section(
				'layout',
				array(
					'title'      => esc_html__( 'Layout', 'hybrid-core' ),
					'priority'   => 30,
					'capability' => 'edit_theme_options'
				)
			);

			/* Add the 'layout' setting. */
			$wp_customize->add_setting(
				'theme_layout',
				array(
					'default'           => get_theme_mod( 'theme_layout', hybrid_get_default_layout() ),
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_html_class',
					'transport'         => 'postMessage'
				)
			);

			/* Add the layout control. */
			$wp_customize->add_control(
				new Hybrid_Customize_Control_Theme_Layout(
					$wp_customize,
					'theme-layout-control',
					array(
						'label'    => esc_html__( 'Global Layout', 'hybrid-core' ),
						'section'  => 'layout',
						'settings' => 'theme_layout',
					)
				)
			);

			/* If viewing the customize preview screen, add a script to show a live preview. */
			if ( $wp_customize->is_preview() && !is_admin() )
				add_action( 'wp_footer', 'theme_layouts_customize_preview_script', 21 );
		}
	}

	public function customize_preview_script() { ?>
		<script type="text/javascript">
		wp.customize(
			'theme_layout',
			function( value ) {
				value.bind( 
					function( to ) {
						var classes = jQuery( 'body' ).attr( 'class' ).replace( /\slayout-[a-zA-Z0-9_-]*/g, '' );
						jQuery( 'body' ).attr( 'class', classes ).addClass( 'layout-' + to );
					} 
				);
			}
		);
		</script>
	<?php }
}


/**
 * Theme Layout customize control class.
 *
 * @since  2.1.0
 * @access public
 */
class Hybrid_Customize_Control_Theme_Layout extends WP_Customize_Control {

	/**
	 * Set up our control.
	 *
	 * @since  2.1.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		$choices = hybrid_get_layout_choices();

		if ( 'default' === hybrid_get_default_layout() )
			unset( $choices['default'] );

		/* Override specific arguments. */
		$args['type']    = 'radio';
		$args['choices'] = $choices;

		/* Let WP handle this. */
		parent::__construct( $manager, $id, $args );
	}
}

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

function hybrid_get_layouts() {
	$layouts = get_theme_support( 'theme-layouts' );
	return isset( $layouts[0] ) ? array_keys( $layouts[0] ) : array_keys( hybrid_get_layout_labels() );
}

function hybrid_get_layouts_args() {

	$defaults = array( 
		'customize' => true, 
		'post_meta' => true, 
		'default'   => 'default' 
	);

	$layouts = get_theme_support( 'theme-layouts' );

	$args = isset( $layouts[1] ) ? $layouts[1] : array();

	$args = apply_filters( 'hybrid_get_theme_layouts_args', wp_parse_args( $args, $defaults ) );

	return $args;
}

function hybrid_get_default_layout() {
	$args = hybrid_get_layouts_args();
	return $args['default'];
}

function hybrid_get_layout() {
	$args = hybrid_get_layouts_args();
	return get_theme_mod( 'theme_layout', hybrid_get_default_layout() );
}

function hybrid_get_post_layout( $post_id ) {
	return get_post_meta( $post_id, theme_layouts_get_meta_key(), true );
}

function hybrid_set_post_layout( $post_id, $layout ) {
	return update_post_meta( $post_id, theme_layouts_get_meta_key(), $layout );
}

function hybrid_delete_post_layout( $post_id ) {
	return delete_post_meta( $post_id, theme_layouts_get_meta_key() );
}

function hybrid_has_post_layout( $layout, $post_id = '' ) {
	if ( !$post_id ) $post_id = get_the_ID();
	return $layout == hybrid_get_post_layout( $post_id ) ? true : false;
}

function hybrid_get_user_layout( $user_id ) {
	return get_user_meta( $user_id, theme_layouts_get_meta_key(), true );
}

function hybrid_set_user_layout( $user_id, $layout ) {
	return update_user_meta( $user_id, theme_layouts_get_meta_key(), $layout );
}

function hybrid_delete_user_layout( $user_id ) {
	return delete_user_meta( $user_id, theme_layouts_get_meta_key() );
}

function hybrid_has_user_layout( $layout, $user_id = '' ) {
	if ( !$user_id ) $user_id = get_query_var( 'author' );
	return $layout == hybrid_get_user_layout( $user_id ) ? true : false;
}

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
	return apply_filters( 'hybrid_get_layout_labels', $strings );
}

function hybrid_get_layout_label( $layout ) {

	/* Get an array of post layout strings. */
	$strings = hybrid_get_layout_labels();

	/* Return the layout's string if it exists. Else, return the layout slug. */
	return isset( $strings[ $layout ] ) ? $strings[ $layout ] : $layout;
}

function hybrid_layout_label( $layout ) {
	echo hybrid_get_layout_label( $layout );
}

final class Hybrid_Theme_Layouts_Admin {

	public function __construct() {

		/* Set up the custom post layouts. */
		add_action( 'admin_init', array( $this, 'setup' ) );
	}

	public function setup() {

		/* Get the extension arguments. */
		$args = hybrid_get_layouts_args();

		/* Return if the theme doesn't support the post meta box. */
		if ( false === $args['post_meta'] )
			return;

		/* Load the post meta boxes on the new post and edit post screens. */
		add_action( 'load-post.php',     array( $this, 'load_meta_boxes' ) );
		add_action( 'load-post-new.php', array( $this, 'load_meta_boxes' ) );

		/* If the attachment post type supports 'theme-layouts', add form fields for it. */
		if ( post_type_supports( 'attachment', 'theme-layouts' ) ) {

			/* Adds a theme layout <select> element to the attachment edit form. */
			add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );

			/* Saves the theme layout for attachments. */
			add_filter( 'attachment_fields_to_save', array( $this, 'attachment_fields_to_save' ), 10, 2 );
		}
	}

	public function load_meta_boxes() {

		/* Add the layout meta box on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );

		/* Saves the post format on the post editing page. */
		add_action( 'save_post',       array( $this, 'save_post' ), 10, 2 );
		add_action( 'add_attachment',  array( $this, 'save_post' )        );
		add_action( 'edit_attachment', array( $this, 'save_post' )        );
	}

	public function add_meta_boxes( $post_type ) {

		if ( post_type_supports( $post_type, 'theme-layouts' ) && current_user_can( 'edit_theme_options' ) )
			add_meta_box( 'hybrid-post-layout', __( 'Layout', 'hybrid-core' ), array( $this, 'meta_box' ), $post_type, 'side', 'default' );
	}

	public function meta_box( $post, $box ) {

		/* Get the current post's layout. */
		$post_layout = hybrid_get_post_layout( $post->ID ); ?>

		<div class="post-layout">

			<?php wp_nonce_field( basename( __FILE__ ), 'theme-layouts-nonce' ); ?>

			<div class="post-layout-wrap">
				<ul>
				<?php foreach ( hybrid_get_layout_choices() as $value => $label ) : ?>
					<li>
						<label>
							<input type="radio" name="post-layout" value="<?php echo esc_attr( $value ); ?>" <?php checked( $post_layout, $value ); ?> /> 
							<?php echo esc_html( $label ); ?>
						</label>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div><?php

	}

	public function save_post( $post_id, $post = '' ) {

		/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
		if ( !is_object( $post ) )
			$post = get_post();

		/* Verify the nonce for the post formats meta box. */
		if ( !isset( $_POST['theme-layouts-nonce'] ) || !wp_verify_nonce( $_POST['theme-layouts-nonce'], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the meta key. */
		$meta_key = theme_layouts_get_meta_key();

		/* Get the previous post layout. */
		$meta_value = get_post_layout( $post_id );

		/* Get the submitted post layout. */
		$new_meta_value = sanitize_key( $_POST['post-layout'] );

		/* If there is no new meta value but an old value exists, delete it. */
		if ( '' == $new_meta_value && $meta_value )
			hybrid_delete_post_layout( $post_id );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( $meta_value !== $new_meta_value )
			hybrid_set_post_layout( $post_id, $new_meta_value );
	}

	public function attachment_fields_to_edit( $fields, $post ) {

		if ( !current_user_can( 'edit_theme_options' ) )
			return $fields;

		/* Get the current post's layout. */
		$post_layout = get_post_layout( $post->ID );

		/* Loop through each theme-supported layout, adding it to the select element. */
		foreach ( foreach ( hybrid_get_layout_choices() as $value => $label ) )
			$select .= '<option value="' . esc_attr( $value ) . '" ' . selected( $post_layout, $value, false ) . '>' . esc_html( $label ) . '</option>';

		/* Set the HTML for the post layout select drop-down. */
		$select = sprintf( '<select name="attachments[%1$s][theme-layouts-post-layout]" id="attachments[%1$s][theme-layouts-post-layout]"></select>', $post->ID, $select );

		/* Add the attachment layout field to the $fields array. */
		$fields['theme-layouts-post-layout'] = array(
			'label'         => __( 'Layout', 'hybrid-core' ),
			'input'         => 'html',
			'html'          => $select,
			'show_in_edit'  => false,
			'show_in_modal' => true
		);

		/* Return the $fields array back to WordPress. */
		return $fields;
	}

	public function attachment_fields_to_save( $post, $fields ) {

		/* If the theme layouts field was submitted. */
		if ( isset( $fields['theme-layouts-post-layout'] ) ) {

			/* Get the meta key. */
			$meta_key = theme_layouts_get_meta_key();

			/* Get the previous post layout. */
			$meta_value = hybrid_get_post_layout( $post['ID'] );

			/* Get the submitted post layout. */
			$new_meta_value = $fields['theme-layouts-post-layout'];

			/* If there is no new meta value but an old value exists, delete it. */
			if ( '' == $new_meta_value && $meta_value )
				hybrid_delete_post_layout( $post['ID'] );

			/* If a new meta value was added and there was no previous value, add it. */
			elseif ( $meta_value !== $new_meta_value )
				hybrid_set_post_layout( $post['ID'], $new_meta_value );
		}

		/* Return the attachment post array. */
		return $post;
	}

/*********************************/

/**
 * Wrapper function for returning the metadata key used for objects that can use layouts.
 *
 * @since  0.3.0
 * @access public
 * @return string The meta key used for theme layouts.
 */
function theme_layouts_get_meta_key() {
	return apply_filters( 'theme_layouts_meta_key', 'Layout' );
}

/* ====== Deprecated ====== */

/**
 * @since      0.1.0
 * @deprecated 0.2.0 Use theme_layouts_get_layout().
 */
function post_layouts_get_layout() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function theme_layouts_register_meta() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function theme_layouts_sanitize_meta() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function theme_layouts_add_post_type_support() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function theme_layouts_remove_post_type_support() {}

/**
 * @since      0.5.0
 * @deprecated 1.0.0
 */
function theme_layouts_get_layouts() {}

/**
 * @since      0.5.0
 * @deprecated 1.0.0
 */
function theme_layouts_get_args() {}

/**
 * @since      0.5.0
 * @deprecated 1.0.0
 */
function theme_layouts_filter_layout() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_get_layout() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function get_post_layout() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function set_post_layout() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function delete_post_layout() {}

/**
 * @since      0.3.0
 * @deprecated 1.0.0
 */
function has_post_layout() {}

/**
 * @since      0.3.0
 * @deprecated 1.0.0
 */
function get_user_layout() {}

/**
 * @since      0.3.0
 * @deprecated 1.0.0
 */
function set_user_layout() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function delete_user_layout() {}

/**
 * @since      0.3.0
 * @deprecated 1.0.0
 */
function has_user_layout() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_body_class() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_strings() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_get_string() {}


/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_admin_setup() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function theme_layouts_load_meta_boxes() {}

/**
 * @since      0.4.0
 * @deprecated 1.0.0
 */
function theme_layouts_add_meta_boxes() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_post_meta_box() {}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function theme_layouts_save_post() {}

/**
 * @since      0.3.0
 * @deprecated 1.0.0
 */
function theme_layouts_attachment_fields_to_edit() {}

/**
 * @since      0.3.0
 * @deprecated 1.0.0
 */
function theme_layouts_attachment_fields_to_save() {}

/**
 * @since      0.1.0
 * @deprecated 1.0.0
 */
function theme_layouts_customize_register() {}

/**
 * @since      0.1.0
 * @deprecated 1.0.0
 */
function theme_layouts_customize_preview_script() {}
