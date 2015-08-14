<?php
/**
 * Customizer setting class for handling an array or comma-separated list of values.  This takes the
 * given `sanitize_callback` and runs it over each element in the array via the `array_map()` function.
 *
 * @package    Hybrid
 * @subpackage Customize
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Handles an array of values by running the `sanitize_callback` through `array_map()`.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Customize_Setting_Array_Map extends WP_Customize_Setting {

	/**
	 * The sanitize callback function to run over each element of the array.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Sanitize the array values.  This method overwrites the parent `sanitize()` method and
	 * runs `array_map()` over the multiple values.  Expected input is an array of values or
	 * a comma-separated list of values.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  array|string  $values
	 * @return array
	 */
	public function sanitize( $values ) {

		$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;

		return ! empty( $multi_values ) ? array_map( array( $this, 'map' ), $multi_values ) : array();
	}

	/**
	 * Callback function for `array_map()`.  Uses the defined `sanitize_callback` to filter
	 * each element of the array.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  mixed  $value
	 * @return mixed
	 */
	public function map( $value ) {

		return apply_filters( "customize_sanitize_{$this->id}", $value, $this );
	}
}
