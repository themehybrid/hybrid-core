<?php
/**
 * Customize class.
 *
 * Registers customizer panels, sections, settings, controls, scripts, and styles.
 * Basically, this class just sets up a lot of stuff for theme authors to use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Customize class.
 *
 * @since  5.0.0
 * @access public
 */
class Customize {

        /**
         * Adds our customizer-related actions to the appropriate hooks.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function __construct() {

                // Add panels, sections, settings, and controls.
                add_action( 'customize_register', [ $this, 'register_sections' ], 0 );
                add_action( 'customize_register', [ $this, 'register_settings' ], 0 );
                add_action( 'customize_register', [ $this, 'register_controls' ], 0 );

                // Register customize controls scripts/styles.
                add_action( 'customize_controls_enqueue_scripts', [ $this, 'controls_register_scripts' ], 0 );

                // Enqueue customize preview scripts/styles.
                add_action( 'customize_preview_init', [ $this, 'preview_enqueue' ], 5 );
        }

        /**
         * Registers the `layout` section. This is used by the theme layotus
         * feature by default but can actually be used for custom theme
         * layout features.
         *
         * @since  5.0.0
         * @access public
         * @param  object  $wp_customize
         * @return void
         */
        public function register_sections( $wp_customize ) {

                $wp_customize->add_section( 'layout', [
                        'title'    => esc_html__( 'Layout', 'hybrid-core' ),
                        'priority' => 30
                ] );
        }

        /**
         * Registers the global theme layout setting if the theme supports the
         * theme layouts feature.
         *
         * @since  5.0.0
         * @access public
         * @param  object  $wp_customize
         * @return void
         */
        public function register_settings( $wp_customize ) {

                if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {

                        $wp_customize->add_setting( 'theme_layout', [
                                'default'           => get_default_layout(),
                                'sanitize_callback' => 'sanitize_key',
                                'transport'         => 'postMessage'
                        ] );
                }
        }

        /**
         * Registers our JS-based custom control types with WordPress.  Also,
         * adds the global layout control if the theme supports theme layouts.
         *
         * @since  5.0.0
         * @access public
         * @param  object  $wp_customize
         * @return void
         */
        public function register_controls( $wp_customize ) {

                $wp_customize->register_control_type( __NAMESPACE__ . '\Customize\Controls\CheckboxMultiple' );
                $wp_customize->register_control_type( __NAMESPACE__ . '\Customize\Controls\Palette'          );
                $wp_customize->register_control_type( __NAMESPACE__ . '\Customize\Controls\RadioImage'       );
                $wp_customize->register_control_type( __NAMESPACE__ . '\Customize\Controls\SelectGroup'      );
                $wp_customize->register_control_type( __NAMESPACE__ . '\Customize\Controls\SelectMultiple'   );

                if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {

                        $layout = new Customize\Controls\Layout( $wp_customize, 'theme_layout', [
                                'label' => esc_html__( 'Global Layout', 'hybrid-core' )
                        ] );

                        $wp_customize->add_control( $layout );
                }
        }

        /**
         * Register customizer controls scripts/styles.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function controls_register_scripts() {

        	wp_register_script(
                        app()->namespace . '-customize-controls',
                        app()->uri . 'resources/scripts/customize-controls' . get_min_suffix() . '.js',
                        [ 'customize-controls' ],
                        null,
                        true
                );

                wp_register_style(
                        app()->namespace . '-customize-controls',
                        app()->uri . 'resources/styles/customize-controls' . get_min_suffix() . '.css'
                );
        }

        /**
         * Enqueue customizer preview scripts/styles.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function preview_enqueue() {

        	if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {

                        wp_enqueue_script(
                                app()->namespace . '-customize-preview',
                                app()->uri . 'resources/scripts/customize-preview' . get_min_suffix() . '.js',
                                [ 'jquery' ],
                                null,
                                true
                        );
                }
        }
}
