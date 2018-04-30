<?php
/**
 * Multiple checkbox customize control.
 *
 * The multiple checkbox customize control allows theme authors to add theme
 * options that have multiple choices.
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
 * Multiple checkbox customize control class.
 *
 * @since  5.0.0
 * @access public
 */
class CheckboxMultiple extends Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'hybrid-checkbox-multiple';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hybrid-customize-controls' );
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

		$this->json['value']   = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value();
		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
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
	 * @access protected
	 * @return void
	 */
	protected function content_template() { ?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>
			<# _.each( data.choices, function( label, choice ) { #>
				<li>
					<label>
						<input type="checkbox" value="{{ choice }}" <# if ( -1 !== data.value.indexOf( choice ) ) { #> checked="checked" <# } #> />
						{{ label }}
					</label>
				</li>
			<# } ) #>
		</ul>
	<?php }
}
