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

		<?php foreach ( $this->choices as $value => $palette ) : ?>

			<label>
				<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( "_customize-{$this->type}-{$this->id}" ); ?>" id="<?php echo esc_attr( "{$this->id}-{$value}" ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $value ); ?> />

				<span class="palette-label"><?php echo esc_html( $palette['label'] ); ?></span>

				<div class="palette-block">

					<?php foreach ( $palette['colors'] as $color ) : ?>
						<span class="palette-color" style="background-color: <?php echo esc_attr( maybe_hash_hex_color( $color ) ); ?>">&nbsp;</span>
					<?php endforeach; ?>

				</div><!-- .palette-block -->
			</label>

		<?php endforeach;
	}
}
