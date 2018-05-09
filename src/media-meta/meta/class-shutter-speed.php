<?php
/**
 * Shutter speed media meta class.
 *
 * Handles the formatting of a camera's shutter speed for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Shutter speed meta class.
 *
 * @since  5.0.0
 * @access public
 */
class ShutterSpeed extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'shutter_speed';

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$shutter = $this->raw();

		// If a shutter speed is given, format the float into a fraction.
		if ( $shutter ) {

			$shutter = $speed = floatval( strip_tags( $shutter ) );

			if ( ( 1 / $speed ) > 1 ) {
				$shutter = sprintf( '<sup>%s</sup>&#8260;', number_format_i18n( 1 ) );

				if ( number_format( ( 1 / $speed ), 1 ) ==  number_format( ( 1 / $speed ), 0 ) ) {

					$shutter .= sprintf(
						'<sub>%s</sub>',
						number_format_i18n( ( 1 / $speed ), 0, '.', '' )
					);

				} else {

					$shutter .= sprintf(
						'<sub>%s</sub>',
						number_format_i18n( ( 1 / $speed ), 1, '.', '' )
					);
				}
			}

			// Translators: %s is the shutter speed of a camera.
			$shutter = sprintf( __( '%s sec', 'hybrid-core' ), $shutter );
		}

		return $shutter;
	}
}
