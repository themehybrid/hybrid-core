<?php
/**
 * Loads customizer-related files (see `/inc/customize`) and sets up customizer functionality.
 *
 * @package    HybridCore
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Load custom control classes.
add_action( 'customize_register', __NAMESPACE__ . '\load_customize_classes', 0 );

# Register customizer panels, sections, settings, and/or controls.
add_action( 'customize_register', __NAMESPACE__ . '\customize_register', 5 );

# Register customize controls scripts/styles.
add_action( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\customize_controls_register_scripts', 0 );
add_action( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\customize_controls_register_styles',  0 );

# Register/Enqueue customize preview scripts/styles.
add_action( 'customize_preview_init', __NAMESPACE__ . '\customize_preview_register_scripts', 0 );
add_action( 'customize_preview_init', __NAMESPACE__ . '\customize_preview_enqueue_scripts'     );

/**
 * Loads framework-specific customize classes.  These are classes that extend the core `WP_Customize_*`
 * classes to provide theme authors access to functionality that core doesn't handle out of the box.
 *
 * @since  3.0.0
 * @access public
 * @param  object  $wp_customize
 * @return void
 */
function load_customize_classes( $wp_customize ) {

	// Register JS control types.
	$wp_customize->register_control_type( 'Hybrid\Customize\Controls\CheckboxMultiple' );
	$wp_customize->register_control_type( 'Hybrid\Customize\Controls\Palette'          );
	$wp_customize->register_control_type( 'Hybrid\Customize\Controls\RadioImage'       );
	$wp_customize->register_control_type( 'Hybrid\Customize\Controls\SelectGroup'      );
	$wp_customize->register_control_type( 'Hybrid\Customize\Controls\SelectMultiple'   );
}

/**
 * Register customizer panels, sections, controls, and/or settings.
 *
 * @since  3.0.0
 * @access public
 * @param  object  $wp_customize
 * @return void
 */
function customize_register( $wp_customize ) {

	// Always add the layout section so that theme devs can utilize it.
	$wp_customize->add_section(
		'layout',
		array(
			'title'    => esc_html__( 'Layout', 'hybrid-core' ),
			'priority' => 30,
		)
	);

	// Check if the theme supports the theme layouts customize feature.
	if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {

		// Add the layout setting.
		$wp_customize->add_setting(
			'theme_layout',
			array(
				'default'           => get_default_layout(),
				'sanitize_callback' => 'sanitize_key',
				'transport'         => 'postMessage'
			)
		);

		// Add the layout control.
		$wp_customize->add_control(
			new Customize\Controls\Layout(
				$wp_customize,
				'theme_layout',
				array( 'label' => esc_html__( 'Global Layout', 'hybrid-core' ) )
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
function customize_controls_register_scripts() {

	wp_register_script( 'hybrid-customize-controls', app()->uri . 'resources/scripts/customize-controls' . hybrid_get_min_suffix() . '.js', array( 'customize-controls' ), null, true );
}

/**
 * Register customizer controls styles.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function customize_controls_register_styles() {

	wp_register_style( 'hybrid-customize-controls', app()->uri . 'resources/styles/customize-controls' . hybrid_get_min_suffix() . '.css' );
}

/**
 * Register customizer preview scripts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function customize_preview_register_scripts() {

	wp_register_script( 'hybrid-customize-preview', app()->uri . 'resources/scripts/customize-preview' . hybrid_get_min_suffix() . '.js', array( 'jquery' ), null, true );
}

/**
 * Register customizer preview scripts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function customize_preview_enqueue_scripts() {

	if ( current_theme_supports( 'theme-layouts', 'customize' ) )
		wp_enqueue_script( 'hybrid-customize-preview' );
}
