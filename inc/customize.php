<?php
/**
 * Functions for registering and setting theme settings that tie into the WordPress theme customizer.  
 * This file loads additional classes and adds settings to the customizer for the built-in Hybrid Core 
 * settings.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load custom control classes. */
add_action( 'customize_register', 'hybrid_load_customize_controls', 0 );

/* Register customizer panels, sections, settings, and/or controls. */
add_action( 'customize_register', 'hybrid_customize_register' );

/* Register customize controls scripts/styles. */
add_action( 'customize_controls_enqueue_scripts', 'hybrid_register_customize_controls_scripts', 5 );
add_action( 'customize_controls_enqueue_scripts', 'hybrid_register_customize_controls_styles',  5 );

/* Register/Enqueue customize preview scripts/styles. */
add_action( 'customize_preview_init', 'hybrid_register_customize_preview_scripts', 5 );
add_action( 'customize_preview_init', 'hybrid_enqueue_customize_preview_scripts'     );

/**
 * Loads framework-specific customize control classes.  Customize control classes extend the WordPress 
 * WP_Customize_Control class to create unique classes that can be used within the framework.
 *
 * @since  1.4.0
 * @access public
 * @return void
 */
function hybrid_load_customize_controls() {

	/* Loads the select multiple customize control class. */
	require_once( HYBRID_CUSTOMIZE . 'control-select-multiple.php' );

	/* Loads the radio image customize control class. */
	require_once( HYBRID_CUSTOMIZE . 'control-radio-image.php' );

	/* Loads the color palette customize control class. */
	require_once( HYBRID_CUSTOMIZE . 'control-palette.php' );

	/* Loads the background image customize control class. */
	require_once( HYBRID_CUSTOMIZE . 'control-background-image.php' );

	/* Loads the background image customize control class. */
	require_if_theme_supports( 'theme-layouts', HYBRID_CUSTOMIZE . 'control-theme-layout.php' );
}

/**
 * Register customizer panels, sections, controls, and/or settings.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_customize_register( $wp_customize ) {

	/* Bail if no theme layout support. */
	if ( !current_theme_supports( 'theme-layouts' ) )
		return;

	/* Get layout args. */
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
	}
}

/**
 * Register customizer controls scripts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_register_customize_controls_scripts() {
	wp_register_script( 'hybrid-customize-controls', esc_url( HYBRID_JS . 'customize-controls' . hybrid_get_min_suffix() . '.js' ), array( 'jquery' ), '20150507', true );
}

/**
 * Register customizer controls styles.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_register_customize_controls_styles() {
	wp_register_style( 'hybrid-customize-controls', esc_url( HYBRID_CSS . 'customize-controls' . hybrid_get_min_suffix() . '.css' ) );
}

/**
 * Register customizer preview scripts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_register_customize_preview_scripts() {
	wp_register_script( 'hybrid-customize-preview', esc_url( HYBRID_JS . 'customize-preview' . hybrid_get_min_suffix() . '.js' ), array( 'jquery' ), '20150507', true );
}

/**
 * Register customizer preview scripts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function hybrid_enqueue_customize_preview_scripts() {

	if ( current_theme_supports( 'theme-layouts' ) )
		wp_enqueue_script( 'hybrid-customize-preview' );
}
