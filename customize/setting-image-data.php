<?php
/**
 * Custom class for saving image data in an array. Only supports the 'theme_mod' type. This is for use
 * with an image setting in which the image URL is saved.  This customizer setting class uses the theme
 * mod name and appends `_data` to the end.  So, if the theme mod is `example_image`, an additional
 * theme mod will be created called `example_image_data`.  The original will have the URL, but the new
 * mod will be an array of data for the image.
 *
 * @package    Hybrid
 * @subpackage Customize
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Saves image data in addition to the URL.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Customize_Setting_Image_Data extends WP_Customize_Setting {

	/**
	 * Overwrites the `update()` method so we can save some extra data.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $value
	 * @return string
	 */
	protected function update( $value ) {

		if ( $value ) {

			$post_id = attachment_url_to_postid( $value );

			if ( $post_id ) {

				$image = wp_get_attachment_image_src( $post_id );

				if ( $image ) {

					// Set up a custom array of data to save.
					$data = array(
						'url'    => esc_url_raw( $image[0] ),
						'width'  => absint( $image[1] ),
						'height' => absint( $image[2] ),
						'id'     => absint( $post_id )
					);

					set_theme_mod( "{$this->id_data[ 'base' ]}_data", $data );
				}
			}
		}

		// No media? Remove the data mod.
		if ( empty( $value ) || empty( $post_id ) || empty( $image ) )
			remove_theme_mod( "{$this->id_data[ 'base' ]}_data" );

		// Let's send this back up and let the parent class do its thing.
		return parent::update( $value );
	}
}
