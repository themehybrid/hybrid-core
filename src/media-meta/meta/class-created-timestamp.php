<?php
/**
 * Created timestamp media meta class.
 *
 * Handles the formatting of an image's created timestamp for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Created timestamp meta class.
 *
 * @since  5.0.0
 * @access public
 */
class CreatedTimestamp extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'created_timestamp';

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$timestamp = $this->raw();

		if ( $timestamp ) {

			$timestamp = date_i18n(
				get_option( 'date_format' ),
				strip_tags( $timestamp )
			);
		}

		return $timestamp;
	}
}
