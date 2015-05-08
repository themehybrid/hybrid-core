<?php
/**
 * The multiple select customize control extends the WP_Customize_Control class.  This class allows 
 * developers to create a `<select>` form field with the `multiple` attribute within the WordPress 
 * theme customizer.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Multiple select customize control class.
 *
 * @since 3.0.0
 */
class Hybrid_Customize_Control_Select_Multiple extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since 3.0.0
	 */
	public $type = 'select';

	/**
	 * Displays the control content.
	 *
	 * @since 3.0.0
	 */
	public function render_content() {

		if ( empty( $this->choices ) )
			return; ?>

		<label>
			<?php if ( !empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( !empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select multiple="multiple" <?php $this->link(); ?>>
				<?php foreach ( $this->choices as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( in_array( $value, (array) $this->value() ) ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	<?php }
}
