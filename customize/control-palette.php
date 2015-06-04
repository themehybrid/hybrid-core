<?php
/**
 * Customize control class to handle color palettes.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Theme Layout customize control class.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Customize_Control_Palette extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since 3.0.0
	 * @var   string
	 */
	public $type = 'palette';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  3.0.0
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
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		$colors = array();

		foreach ( $this->choices as $choice => $value )
			$colors[ $choice ] = array_map( 'maybe_hash_hex_color', $value['colors'] );

		$this->json['colors']  = $colors;
		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# var name = '_customize-' + data.type + '-' + data.id; #>

		<# for ( palette in data.choices ) { #>
			<label>
				<input type="radio" value="{{ palette }}" name="{{ name }}" {{{ data.link }}} <# if ( palette === data.value ) { #> checked="checked" <# } #> /> 

				<span class="palette-label">{{ data.choices[ palette ]['label'] }}</span>

				<div class="palette-block">

					<# for ( color in data.colors[ palette ] ) { #>

						<span class="palette-color" style="background-color: {{ data.colors[ palette ][ color ] }}">&nbsp;</span>
					<# } #>

				</div>
			</label> 
		<# } #>
	<?php }
}
