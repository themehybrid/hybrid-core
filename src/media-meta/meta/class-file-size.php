<?php
/**
 * File size media meta class.
 *
 * Handles the formatting of a media file size for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * File size meta class.
 *
 * @since  5.0.0
 * @access public
 */
class FileSize extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'filesize';

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		$filesize = isset( $this->meta['filesize'] ) ? $this->meta['filesize'] : '';

		if ( ! $filesize ) {
			$file = get_attached_file( $this->post_id );

			if ( file_exists( $file ) ) {
				$filesize = filesize( $file );
			}
		}

		return $filesize;
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$filesize = $this->raw();

		if ( $filesize ) {

			$filesize = size_format( strip_tags( $filesize ), 2 );
		}

		return $filesize;
	}
}
