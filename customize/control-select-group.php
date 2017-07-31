<?php
/**
 * The group select customize control extends the WP_Customize_Control class.  This class allows
 * developers to create a `<select>` form field with the `<optgroup>` elements mixed in.  They
 * can also utilize regular `<option>` choices.
 *
 * @package    Hybrid
 * @subpackage Customize
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Multiple select customize control class.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Customize_Control_Select_Group extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'select-group';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hybrid-customize-controls' );
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

		$choices = $group = array();

		foreach ( $this->choices as $choice => $maybe_group ) {

			if ( is_array( $maybe_group ) )
				$group[ $choice ] = $maybe_group;
			else
				$choices[ $choice ] = $maybe_group;
		}

		$this->json['choices'] = $choices;
		$this->json['group']   = $group;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Don't render the content via PHP.  This control is handled with a JS template.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return bool
	 */
	protected function render_content() {}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( ! data.choices && ! data.group ) {
			return;
		} #>

		<label>

			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>

			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>

			<select {{{ data.link }}}>

				<# _.each( data.choices, function( label, choice ) { #>

					<option value="{{ choice }}" <# if ( choice === data.value ) { #> selected="selected" <# } #>>{{ label }}</option>

				<# } ) #>

				<# _.each( data.group, function( group ) { #>

					<optgroup label="{{ group.label }}">

						<# _.each( group.choices, function( label, choice ) { #>

							<option value="{{ choice }}" <# if ( choice === data.value ) { #> selected="selected" <# } #>>{{ label }}</option>

						<# } ) #>

					</optgroup>
				<# } ) #>
			</select>
		</label>
	<?php }
}
