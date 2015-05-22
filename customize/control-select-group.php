<?php
/**
 * The group select customize control extends the WP_Customize_Control class.  This class allows 
 * developers to create a `<select>` form field with the `<optgroup>` elements mixed in.  They 
 * can also utilize regular `<option>` choices.
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
	 * Displays the control content.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
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

			<select <?php $this->link(); ?>>

				<?php foreach ( $this->choices as $value => $maybe_group ) : ?>

					<?php if ( is_array( $maybe_group ) ) : // If we have an `<optgroup>`. ?>

						<optgroup label="<?php echo esc_attr( $maybe_group['label'] ); ?>">

							<?php foreach ( $maybe_group['choices'] as $choice_value => $choice_label ) : ?>
								<option value="<?php echo esc_attr( $choice_value ); ?>" <?php selected( $choice_value, $this->value() ); ?>><?php echo esc_html( $choice_label ); ?></option>
							<?php endforeach; ?>

						</optgroup>

					<?php else : // Assume regular `<option>`. ?>

						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $this->value() ); ?>><?php echo esc_html( $maybe_group ); ?></option>

					<?php endif; // End check for `<optgroup>`. ?>

				<?php endforeach; // End loop through choices. ?>
			</select>
		</label>
	<?php }
}
