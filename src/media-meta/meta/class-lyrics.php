<?php
/**
 * Lyrics media meta class.
 *
 * Handles the formatting of a audio transcript/lyrics for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

/**
 * Lyrics meta class.
 *
 * @since  5.0.0
 * @access public
 */
class Lyrics extends Meta {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'unsychronised_lyric';

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		$lyrics = '';

		// Look for the 'unsynchronised_lyric' tag.
		if ( isset( $this->meta['unsynchronised_lyric'] ) ) {
			$lyrics = $this->meta['unsynchronised_lyric'];

		// Seen this misspelling of the id3 tag.
		} elseif ( isset( $this->meta['unsychronised_lyric'] ) ) {
			$lyrics = $this->meta['unsychronised_lyric'];
		}

		return $lyrics;
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$lyrics = $this->raw();

		if ( $lyrics ) {
			$lyrics = strip_tags( $lyrics );
			$lyrics = wptexturize( $lyrics );
			$lyrics = convert_chars( $lyrics );
			$lyrics = wpautop( $lyrics );
		}

		return $lyrics;
	}
}
