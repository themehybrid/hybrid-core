<?php
/**
 * Radio image customize control.
 *
 * The radio image customize control allows developers to create a list of image
 * radio inputs.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Customize\Controls;

use WP_Customize_Control as Control;

/**
 * Radio image customize control.
 *
 * @since  5.0.0
 * @access public
 */
class RadioImage extends Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'hybrid-radio-image';

	/**
	 * Loads the framework scripts/styles.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hybrid-customize-controls' );
		wp_enqueue_style(  'hybrid-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// We need to make sure we have the correct image URL.
		array_walk( $this->choices, function( &$args, $key ) {

			$args['url'] = esc_url( \Hybrid\sprintf_theme_uri( $args['url'] ) );
		} );

		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Don't render the content via PHP.  This control is handled with a JS template.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return bool
	 */
	protected function render_content() {}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# _.each( data.choices, function( args, choice ) { #>
			<label>
				<input type="radio" value="{{ choice }}" name="_customize-{{ data.type }}-{{ data.id }}" {{{ data.link }}} <# if ( choice === data.value ) { #> checked="checked" <# } #> />

				<span class="screen-reader-text">{{ args.label }}</span>

				<img src="{{ args.url }}" alt="{{ args.label }}" />
			</label>
		<# } ) #>
	<?php }
}
