<?php
/**
 * File type media meta class.
 *
 * Handles the formatting of a media file type for output.
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
class FileType extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'file_type';

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		$file_type = '';

		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->post_id ), $matches ) ) {

			$file_type = $matches[1];
		}

		return $file_type;
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$type = $this->raw();

		return $type ? esc_html( strtoupper( $type ) ) : '';
	}
}
