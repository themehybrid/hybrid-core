<?php
/**
 * The radio image customize control extends the WP_Customize_Control class.  This class allows
 * developers to create a list of image radio inputs.
 *
 * Note, the `$choices` array is slightly different than normal and should be in the form of
 * `array(
 *	$value => array( 'url' => $image_url, 'label' => $text_label ),
 *	$value => array( 'url' => $image_url, 'label' => $text_label ),
 * )`
 *
 * @package    Hybrid
 * @subpackage Customize
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Radio image customize control.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Customize_Control_Radio_Image extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'radio-image';

	/**
	 * Loads the framework scripts/styles.
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

		// We need to make sure we have the correct image URL.
		foreach ( $this->choices as $value => $args )
			$this->choices[ $value ]['url'] = esc_url( hybrid_sprintf_theme_uri( $args['url'] ) );

		$this->json['choices'] = $this->choices;
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
