<?php
/**
 * Functions for registering and setting theme settings that tie into the WordPress theme customizer.  
 * This file loads additional classes and adds settings to the customizer for the built-in Hybrid Core 
 * settings.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load custom control classes. */
add_action( 'customize_register', 'hybrid_load_customize_controls', 1 );

/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'hybrid_customize_register' );

/**
 * Loads framework-specific customize control classes.  Customize control classes extend the WordPress 
 * WP_Customize_Control class to create unique classes that can be used within the framework.
 *
 * @since 1.4.0
 * @access private
 */
function hybrid_load_customize_controls() {

	/* Loads the textarea customize control class. */
	require_once( trailingslashit( HYBRID_CLASSES ) . 'customize-control-textarea.php' );
}

/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since 1.4.0
 * @access private
 * @param object $wp_customize
 */
function hybrid_customize_register( $wp_customize ) {

	/* Get supported theme settings. */
	$supports = get_theme_support( 'hybrid-core-theme-settings' );

	/* Add the footer section, setting, and control if theme supports the 'footer' setting. */
	if ( is_array( $supports[0] ) && in_array( 'footer', $supports[0] ) ) {

		$wp_customize->add_section(
			'hybrid-core-footer',
			array(
				'title' => __( 'Footer', 'hybrid-core' ),
				'priority' => 200,
				'capability' => 'edit_theme_options',
			//	'theme_supports' => 'hybrid-core-theme-settings'
			)
		);

		$default_settings = hybrid_get_default_theme_settings();

		$wp_customize->add_setting(
			hybrid_get_prefix() . '_theme_settings[footer_insert]',
			array(
				'default' => $default_settings['footer_insert'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				//'sanitize_js_callback' => 'do_shortcode',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Hybrid_Customize_Control_Textarea(
				$wp_customize,
				'hybrid-core-footer',
				array(
					'label' => __( 'Footer', 'hybrid-core' ),
					'section' => 'hybrid-core-footer',
					'settings' => hybrid_get_prefix() . '_theme_settings[footer_insert]',
				)
			)
		);

		if ( $wp_customize->is_preview() && !is_admin() )
			add_action( 'wp_footer', 'hybrid_customize_preview', 21 );
	}
}

/**
 * Handles changing settings for the live preview of the theme.
 *
 * @since 1.4.0
 * @access private
 */
function hybrid_customize_preview() {
	?>
	<script type="text/javascript">
	wp.customize(
		'<?php echo hybrid_get_prefix(); ?>_theme_settings[footer_insert]',
		function( value ) {
			value.bind(
				function( to ) {
					jQuery( '#footer .wrap' ).html( to );
				}
			);
		}
	);
	</script>
	<?php
}

?>