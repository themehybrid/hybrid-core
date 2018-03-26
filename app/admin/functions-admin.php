<?php
/**
 * Admin functions.
 *
 * Theme administration functions used with other components of the framework
 * admin. This file is for setting up any basic features and holding additional
 * admin helper functions.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Admin;

# Register scripts and styles.
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\register_scripts',  0 );

# Allow posts page to be edited.
add_action( 'edit_form_after_title', __NAMESPACE__ . '\enable_posts_page_editor', 0 );

/**
 * Registers admin scripts and/or styles.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function register_scripts() {

	wp_register_style(
		'hybrid-admin',
		\Hybrid\uri( 'resources/styles/admin' . \Hybrid\get_min_suffix() . '.css' )
	);
}

/**
 * Fix for users who want to display content on the posts page above the posts
 * list, which is a theme feature common to themes built from the framework.
 *
 * @since  5.0.0
 * @access public
 * @param  object  $post
 * @return void
 */
function enable_posts_page_editor( $post ) {

	if ( get_option( 'page_for_posts' ) != $post->ID ) {
		return;
	}

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( $post->post_type, 'editor' );
}

/**
 * Wrapper function for `wp_verify_nonce()` with a posted value.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $action
 * @param  string  $arg
 * @return bool
 */
function verify_nonce_post( $action = '', $arg = '_wpnonce' ) {

	return isset( $_POST[ $arg ] )
	       ? wp_verify_nonce( sanitize_key( $_POST[ $arg ] ), $action )
	       : false;
}

/**
 * Wrapper function for `wp_verify_nonce()` with a request value.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $action
 * @param  string  $arg
 * @return bool
 */
function verify_nonce_request( $action = '', $arg = '_wpnonce' ) {

	return isset( $_REQUEST[ $arg ] )
	       ? wp_verify_nonce( sanitize_key( $_REQUEST[ $arg ] ), $action )
	       : false;
}

/**
 * Displays the layout form field.  Used for various admin screens.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function hybrid_form_field_layout( $args = [] ) {

	$args = wp_parse_args( $args, [
		'layouts'    => \Hybrid\get_layouts(),
		'selected'   => 'default',
		'field_name' => 'hybrid-layout'
	] ); ?>

	<div class="hybrid-form-field-layout">

	<?php foreach ( $args['layouts'] as $layout ) : ?>

		<label class="has-img">
			<input type="radio" value="<?php echo esc_attr( $layout->name ); ?>" name="<?php echo esc_attr( $args['field_name'] ); ?>" <?php checked( $args['selected'], $layout->name ); ?> />

			<span class="screen-reader-text"><?php echo esc_html( $layout->label ); ?></span>

			<img src="<?php echo esc_url( \Hybrid\sprintf_theme_uri( $layout->image ) ); ?>" alt="<?php echo esc_attr( $layout->label ); ?>" />
		</label>

	<?php endforeach; ?>

	</div>
<?php }

/**
 * Outputs the inline JS for use with the layout form field.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
function layout_field_inline_script() { ?>

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
