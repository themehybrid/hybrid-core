<?php
/**
 * Aperture media meta class.
 *
 * Handles the formatting of a camera's aperture for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Aperture meta class.
 *
 * @since  5.0.0
 * @access public
 */
class Aperture extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'aperture';

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$aperture = $this->raw();

		if ( $aperture ) {

			$aperture = sprintf(
				'<sup>f</sup>&#8260;<sub>%s</sub>',
				absint( $aperture )
			);
		}

		return $aperture;
	}
}
