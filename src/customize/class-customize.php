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

use WP_Customize_Manager;
use Hybrid\Contracts\Bootable;
use Hybrid\Customize\Controls\CheckboxMultiple;
use Hybrid\Customize\Controls\Palette;
use Hybrid\Customize\Controls\RadioImage;
use Hybrid\Customize\Controls\SelectGroup;
use Hybrid\Customize\Controls\SelectMultiple;

use function Hybrid\get_min_suffix;
use function Hybrid\uri;
use function Hybrid\version;

/**
 * Customize class.
 *
 * @since  5.0.0
 * @access public
 */
class Customize implements Bootable {

	/**
	 * Adds our customizer-related actions to the appropriate hooks.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', [ $this, 'registerControls' ], 0 );

		// Enqueue scripts and styles.
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'controlsEnqueue'] );
	}

	/**
	 * Registers our JS-based custom control types with WordPress.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function registerControls( WP_Customize_Manager $manager ) {

		$controls = [
			CheckboxMultiple::class,
			Palette::class,
			RadioImage::class,
			SelectGroup::class,
			SelectMultiple::class
		];

		array_map( function( $control ) use ( $manager ) {

			$manager->register_control_type( $control );

		}, $controls );
	}

	/**
	 * Register or enqueue scripts/styles for the controls that are output
	 * in the controls frame.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function controlsEnqueue() {

		$suffix = get_min_suffix();

		wp_register_script(
			'hybrid-customize-controls',
			uri( "resources/scripts/customize-controls{$suffix}.js" ),
			[ 'customize-controls', 'jquery' ],
			version(),
			true
		);

		wp_register_style(
			'hybrid-customize-controls',
			uri( "resources/styles/customize-controls{$suffix}.css" ),
			[],
			version()
		);
	}
}
