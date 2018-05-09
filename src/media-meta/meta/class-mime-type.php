<?php
/**
 * Mime type media meta class.
 *
 * Handles the formatting of a media file's mime type for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Mime type meta class.
 *
 * @since  5.0.0
 * @access public
 */
class MimeType extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'mime_type';

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		$mime_type = get_post_mime_type( $this->post_id );

		if ( empty( $mime_type ) && ! empty( $this->meta['mime_type'] ) ) {

			$mime_type = $this->meta['mime_type'];
		}

		return $mime_type;
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$mime_type = $this->raw();

		return $mime_type ? esc_html( $mime_type ) : '';
	}
}
