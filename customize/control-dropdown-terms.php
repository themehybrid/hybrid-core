<?php
/**
 * A dropdown taxonomy terms `<select>` customizer control class.  This control is built on top of
 * the core `wp_dropdown_categories()` function (works for any taxonomy).  By passing in a custom
 * `$args` parameter, which is passed to `wp_dropdown_categories()`, you can alter the output of the
 * dropdown select.
 *
 * @package    Hybrid
 * @subpackage Customize
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2008 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Dropdown terms customize control class.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Customize_Control_Dropdown_Terms extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'dropdown-terms';

	/**
	 * Custom arguments to pass into `wp_dropdown_categories()`.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $args = array();

	/**
	 * Displays the control content.
	 *
	 * @since 3.0.0
	 */
	public function render_content() {

		// Allow devs to pass in custom arguments.
		$args = wp_parse_args(
			$this->args,
			array( 'hierarchical' => true, 'show_option_none'  => ' ', 'option_none_value' => '0' )
		);

		// Overwrite specific arguments.
		$args['name']     = '_customize-dropdown-terms-' . $this->id;
		$args['selected'] = $this->value();
		$args['echo']     = false; ?>

		<label>

			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<?php echo str_replace( '<select', '<select ' . $this->get_link(), wp_dropdown_categories( $args ) ); ?>

		</label>
	<?php }
}
