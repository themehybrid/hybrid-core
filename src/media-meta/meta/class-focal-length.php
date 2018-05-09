<?php
/**
 * Focal length media meta class.
 *
 * Handles the formatting of a camera's focal length for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Focal length meta class.
 *
 * @since  5.0.0
 * @access public
 */
class FocalLength extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'focal_length';

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$focal_length = $this->raw();

		if ( $focal_length ) {

			// Translators: %s is the focal length of a camera.
			$focal_length = sprintf(
				__( '%s mm', 'hybrid-core' ),
				absint( $focal_length )
			);
		}

		return $focal_length;
	}
}
