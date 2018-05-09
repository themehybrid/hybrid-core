<?php
/**
 * Dimensions media meta class.
 *
 * Handles the formatting of media dimensions (width x height) for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Dimensions meta class.
 *
 * @since  5.0.0
 * @access public
 */
class Dimensions extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'dimensions';

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		$dimensions = [];

		if ( ! empty( $this->meta['width'] ) && ! empty( $this->meta['height'] ) ) {

			$dimensions['width']  = $this->meta['width'];
			$dimensions['height'] = $this->meta['height'];
		}

		return $dimensions;
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$dimensions = $this->raw();

		if ( $dimensions ) {

			return sprintf(
				// Translators: Media dimensions - 1 is width and 2 is height.
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $dimensions['width']  ) ),
				number_format_i18n( absint( $dimensions['height'] ) )
			);
		}

		return '';
	}
}
