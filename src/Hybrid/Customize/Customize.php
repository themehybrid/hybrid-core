<?php
/**
 * Customize class.
 *
 * Registers panels, sections, settings, controls, and anything else needed
 * for the customizer. Basically, this class just sets up a lot of stuff
 * for theme authors to use.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Customize;

use Hybrid\Customize\Controls\CheckboxMultiple;
use Hybrid\Customize\Controls\Palette;
use Hybrid\Customize\Controls\RadioImage;
use Hybrid\Customize\Controls\SelectGroup;
use Hybrid\Customize\Controls\SelectMultiple;
use function Hybrid\get_default_layout;

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
                add_action( 'customize_register', [ $this, 'registerSections' ], 0 );
                add_action( 'customize_register', [ $this, 'registerSettings' ], 0 );
                add_action( 'customize_register', [ $this, 'registerControls' ], 0 );

                // Enqueue customize preview scripts/styles.
                add_action( 'customize_preview_init', [ $this, 'previewEnqueue' ], 5 );
        }

        /**
         * Registers the `layout` section. This is used by the theme layouts
         * feature by default but can actually be used for custom theme
         * layout features.
         *
         * @since  5.0.0
         * @access public
         * @param  object  $wp_customize
         * @return void
         */
        public function registerSections( $wp_customize ) {

                $wp_customize->add_section( 'hybrid-layout', [
                        'title'    => esc_html__( 'Layout', 'hybrid-core' ),
                        'priority' => 30
                ] );
        }

        /**
         * Registers the global theme layout setting if the theme supports
         * the theme layouts feature.
         *
         * @since  5.0.0
         * @access public
         * @param  object  $wp_customize
         * @return void
         */
        public function registerSettings( $wp_customize ) {

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
        public function registerControls( $wp_customize ) {

                $wp_customize->register_control_type( CheckboxMultiple::class );
                $wp_customize->register_control_type( Palette::class          );
                $wp_customize->register_control_type( RadioImage::class       );
                $wp_customize->register_control_type( SelectGroup::class      );
                $wp_customize->register_control_type( SelectMultiple::class   );

                if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {

                        $layout = new Controls\Layout( $wp_customize, 'theme_layout', [
                                'label' => esc_html__( 'Global Layout', 'hybrid-core' )
                        ] );

                        $wp_customize->add_control( $layout );
                }
        }

        /**
         * Enqueue customizer preview scripts/styles.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function previewEnqueue() {

        	if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {
                        wp_enqueue_script( 'hybrid-customize-preview' );
                }
        }
}
