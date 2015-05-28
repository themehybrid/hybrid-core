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
class Hybrid_Customize_Control_Layout extends Hybrid_Customize_Control_Radio_Image {

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

		// Array of allowed layouts. Pass via `$args['layouts']`.
		$allowed = !empty( $args['layouts'] ) ? $args['layouts'] : array_keys( hybrid_get_layouts() );

		// Loop through each of the layouts and add it to the choices array with proper key/value pairs.
		foreach ( hybrid_get_layouts() as $layout ) {

			if ( in_array( $layout->name, $allowed ) && ! ( 'theme_layout' === $id && false === $layout->is_global_layout ) && $layout->image ) {

				$args['choices'][ $layout->name ] = array(
					'label' => $layout->label,
					'url'   => sprintf( $layout->image, get_template_directory_uri(), get_stylesheet_directory_uri() )
				);
			}
		}

		// Let the parent class handle the rest.
		parent::__construct( $manager, $id, $args );
	}
}
