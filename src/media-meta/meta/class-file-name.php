<?php
/**
 * File name media meta class.
 *
 * Handles the formatting of a media file name for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * File name meta class.
 *
 * @since  5.0.0
 * @access public
 */
class FileName extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'file_name';

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		return basename( get_attached_file( $this->post_id ) );

		return sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_get_attachment_url( $this->post_id ) ),
			basename( get_attached_file( $this->post_id ) )
		);
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$filename = $this->raw();

		if ( $filename ) {

			$filename = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_get_attachment_url( $this->post_id ) ),
				$filename
			);
		}

		return $filename;
	}
}
