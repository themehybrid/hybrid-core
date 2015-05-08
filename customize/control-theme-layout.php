<?php
/**
 * Customize control class to handle theme layouts.  By default, it simply outputs a custom set of 
 * radio inputs.  Theme authors can extend this class and do something even cooler.
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
class Hybrid_Customize_Control_Theme_Layout extends WP_Customize_Control {

	/**
	 * Set up our control.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		$choices = hybrid_get_layout_choices();

		if ( isset( $choices['default'] ) )
			unset( $choices['default'] );

		/* Override specific arguments. */
		$args['type']    = 'radio';
		$args['choices'] = $choices;

		/* Let WP handle this. */
		parent::__construct( $manager, $id, $args );
	}
}
