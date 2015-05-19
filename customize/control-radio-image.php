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
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
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
	 * @since 3.0.0
	 * @var   string
	 */
	public $type = 'radio-image';

	/**
	 * Loads the jQuery UI Button script and framework scripts/styles.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-button'          );
		wp_enqueue_script( 'hybrid-customize-controls' );
		wp_enqueue_style(  'hybrid-customize-controls' );
	}

	/**
	 * Displays the control content.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function render_content() {

		// If no choices are provided, bail.
		if ( empty( $this->choices ) )
			return; ?>

		<?php if ( !empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>

		<?php if ( !empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
		<?php endif; ?>

		<div class="buttonset">

			<?php foreach ( $this->choices as $value => $args ) : ?>

				<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( "_customize-radio-{$this->id}" ); ?>" id="<?php echo esc_attr( "{$this->id}-{$value}" ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $value ); ?> /> 

				<label for="<?php echo esc_attr( "{$this->id}-{$value}" ); ?>">
					<span class="screen-reader-text"><?php echo esc_html( $args['label'] ); ?></span>
					<img src="<?php echo esc_url( sprintf( $args['url'], get_template_directory_uri(), get_stylesheet_directory_uri() ) ); ?>" alt="<?php echo esc_attr( $args['label'] ); ?>" />
				</label>

			<?php endforeach; ?>

		</div><!-- .buttonset -->
	<?php }
}
