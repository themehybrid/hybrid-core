<?php
/**
 * Extends the WordPress background image customize control class, which allows a theme to register
 * multiple default backgrounds for the user to choose from.  To use this, the theme author 
 * should remove the 'background_image' control and add this control in its place.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Background image customize control class.
 *
 * Note that this is soft-deprecated in 3.0.0.  If we can come up with a fix for adding default 
 * backgrounds to WP 4.1+, we'll revisit this.  For now, it's on track to be removed completely.
 * @link https://github.com/justintadlock/hybrid-core/issues/91
 *
 * @since      2.0.0
 * @deprecated 3.0.0
 */
class Hybrid_Customize_Control_Background_Image extends WP_Customize_Background_Image_Control {

	/**
	 * Array of default backgrounds.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $default_backgrounds = array();

	/**
	 * Set up our control.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function __construct( $manager ) {

		// Let WP handle this.
		parent::__construct( $manager );
	}
}
