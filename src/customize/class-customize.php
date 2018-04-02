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

                add_action( 'customize_register', [ $this, 'registerControls' ], 0 );
        }

        /**
         * Registers our JS-based custom control types with WordPress.
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
        }
}
