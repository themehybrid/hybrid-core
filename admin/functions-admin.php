<?php
/**
 * Theme administration functions used with other components of the framework admin.
 * This file is for setting up any basic features and holding additional admin
 * helper functions.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Register scripts and styles.
add_action( 'admin_enqueue_scripts', 'hybrid_admin_register_scripts', 0 );
add_action( 'admin_enqueue_scripts', 'hybrid_admin_register_styles',  0 );

# Allow posts page to be edited.
add_action( 'edit_form_after_title', 'hybrid_enable_posts_page_editor', 0 );

/**
 * Registers admin scripts.
 *
 * @note   Soft-deprecated. We'll probably reuse function at some point.
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_admin_register_scripts() {}

/**
 * Registers admin styles.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_admin_register_styles() {

	wp_register_style( 'hybrid-admin', hybrid()->uri . 'css/admin' . hybrid_get_min_suffix() . '.css' );
}

/**
 * Fix for users who want to display content on the posts page above the posts list, which is a
 * theme feature common to themes built from the framework.
 *
 * @since  3.0.0
 * @access public
 * @param  object  $post
 * @return void
 */
function hybrid_enable_posts_page_editor( $post ) {

	if ( get_option( 'page_for_posts' ) != $post->ID )
		return;

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( $post->post_type, 'editor' );
}

/**
 * Wrapper function for `wp_verify_nonce()` with a posted value.
 *
 * @since  4.0.0
 * @access public
 * @param  string  $action
 * @param  string  $arg
 * @return bool
 */
function hybrid_verify_nonce_post( $action = '', $arg = '_wpnonce' ) {

	return isset( $_POST[ $arg ] ) ? wp_verify_nonce( sanitize_key( $_POST[ $arg ] ), $action ) : false;
}

/**
 * Wrapper function for `wp_verify_nonce()` with a request value.
 *
 * @since  4.0.0
 * @access public
 * @param  string  $action
 * @param  string  $arg
 * @return bool
 */
function hybrid_verify_nonce_request( $action = '', $arg = '_wpnonce' ) {

	return isset( $_REQUEST[ $arg ] ) ? wp_verify_nonce( sanitize_key( $_REQUEST[ $arg ] ), $action ) : false;
}

/**
 * Displays the layout form field.  Used for various admin screens.
 *
 * @since  4.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function hybrid_form_field_layout( $args = array() ) {

	$defaults = array(
		'layouts'    => hybrid_get_layouts(),
		'selected'   => 'default',
		'field_name' => 'hybrid-layout'
	);

	$args = wp_parse_args( $args, $defaults ); ?>

	<div class="hybrid-form-field-layout">

	<?php foreach ( $args['layouts'] as $layout ) : ?>

		<label class="has-img">
			<input type="radio" value="<?php echo esc_attr( $layout->name ); ?>" name="<?php echo esc_attr( $args['field_name'] ); ?>" <?php checked( $args['selected'], $layout->name ); ?> />

			<span class="screen-reader-text"><?php echo esc_html( $layout->label ); ?></span>

			<img src="<?php echo esc_url( hybrid_sprintf_theme_uri( $layout->image ) ); ?>" alt="<?php echo esc_attr( $layout->label ); ?>" />
		</label>

	<?php endforeach; ?>

	</div>
<?php }

/**
 * Outputs the inline JS for use with the layout form field.
 *
 * @since  4.0.0
 * @access public
 * @return void
 */
function hybrid_layout_field_inline_script() { ?>

	<script type="text/javascript">
	jQuery( document ).ready( function() {

		// Layout container.
		var container = jQuery( '.hybrid-form-field-layout' );

		// Add the `.checked` class to whichever radio is checked.
		jQuery( 'input:checked', container ).addClass( 'checked' );

		// When a radio is clicked.
		jQuery( 'input', container ).click( function() {

			// If the radio has the `.checked` class, remove it and uncheck the radio.
			if ( jQuery( this ).hasClass( 'checked' ) ) {

				jQuery( 'input', container ).removeClass( 'checked' );
				jQuery( this ).prop( 'checked', false );

			// If the radio is not checked, add the `.checked` class and check it.
			} else {

				jQuery( 'input', container ).removeClass( 'checked' );
				jQuery( this ).addClass( 'checked' );
			}
		} );
	} );
	</script>
<?php }
