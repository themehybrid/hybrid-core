<?php
/**
 * Creates a meta box for the theme settings page, which holds a textarea for custom footer text within 
 * the theme.  To use this feature, the theme must support the 'footer' argument for the 
 * 'hybrid-core-theme-settings' feature.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Create the footer meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'hybrid_meta_box_theme_add_footer' );

/* Sanitize the footer settings before adding them to the database. */
add_filter( 'sanitize_option_' . hybrid_get_prefix() . '_theme_settings', 'hybrid_meta_box_theme_save_footer' );

/**
 * Adds the core theme footer meta box to the theme settings page in the admin.
 *
 * @since 1.2.0
 * @return void
 */
function hybrid_meta_box_theme_add_footer() {

	add_meta_box( 'hybrid-core-footer', __( 'Footer settings', 'hybrid-core' ), 'hybrid_meta_box_theme_display_footer', hybrid_get_settings_page_name(), 'normal', 'high' );
}

/**
 * Creates a meta box that allows users to customize their footer.
 *
 * @since 1.2.0
 * @uses wp_editor() Creates an instance of the WordPress text/content editor.
 * @return void
 */
function hybrid_meta_box_theme_display_footer() {

	/* Add a textarea using the wp_editor() function to make it easier on users to add custom content. */
	wp_editor(
		esc_textarea( hybrid_get_setting( 'footer_insert' ) ),	// Editor content.
		hybrid_settings_field_id( 'footer_insert' ),		// Editor ID.
		array(
			'tinymce' => 		false, // Don't use TinyMCE in a meta box.
			'textarea_name' => 	hybrid_settings_field_name( 'footer_insert' )
		)
	); ?>

	<p>
		<span class="description"><?php _e( 'You can add custom <acronym title="Hypertext Markup Language">HTML</acronym> and/or shortcodes, which will be automatically inserted into your theme.', 'hybrid-core' ); ?></span>
	</p>

<?php }

/**
 * Saves the footer meta box settings by filtering the "sanitize_option_{$prefix}_theme_settings" hook.
 *
 * @since 1.2.0
 * @param array $settings Array of theme settings passed by the Settings API for validation.
 * @return array $settings
 */
function hybrid_meta_box_theme_save_footer( $settings ) {

	/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
	if ( isset( $settings['footer_insert'] ) && !current_user_can( 'unfiltered_html' ) )
		$settings['footer_insert'] = stripslashes( wp_filter_post_kses( addslashes( $settings['footer_insert'] ) ) );

	/* Return the theme settings. */
	return $settings;
}

?>