<?php
/**
 * Meta class for media.
 *
 * This is the base class for handling the retrieval of an individual media
 * metadata value and formatting the data for output.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta\Meta;

use Hybrid\Contracts\MediaMeta as MediaMetaContract;

/**
 * Base meta class.
 *
 * @since  5.0.0
 * @access public
 */
class Meta implements MediaMetaContract {

	/**
	 * The metadata name/key.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = '';

	/**
	 * The attachment post ID.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $post_id = 0;

	/**
	 * Array of metadata retrieved via `wp_get_attachment_metadata()`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $meta = [];

	/**
	 * Output sanitization/escaping callback function used to sanitize the
	 * data for output to the screen.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $sanitize_callback = 'esc_html';

	/**
	 * Assigns the parameters as class properties and sets up the object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  int    $post_id
	 * @param  array  $args
	 * @return void
	 */
	public function __construct( $post_id, $args = [] ) {

		if ( ! $this->name && isset( $args['name'] ) ) {
			$this->name = $args['name'];
		}

		if ( isset( $args['meta'] ) ) {
			$this->meta = $args['meta'];
		}

		$this->post_id = $post_id;

		if ( $this->sanitize_callback ) {
			add_filter( "hybrid/media/meta/{$this->name}/sanitize", $this->sanitize_callback );
		}
	}

	/**
	 * Returns the raw, unsanitized value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function raw() {

		$value = '';

		// If the property exists in the meta array.
		if ( isset( $this->meta[ $this->name ] ) ) {

			$value = $this->meta[ $this->name ];

		// If the property exists in the image meta array.
		} elseif ( isset( $this->meta['image_meta'] ) && isset( $this->meta['image_meta'][ $this->name ] ) ) {

			$value = $this->meta['image_meta'][ $this->name ];

		// If the property exists in the video's audio meta array.
		} elseif ( isset( $this->meta['audio'] ) && isset( $this->meta['audio'][ $this->name ] ) ) {

			$value = $this->meta['audio'][ $this->name ];
		}

		return apply_filters( "hybrid/media/meta/{$this->name}", $value );
	}

	/**
	 * Returns the sanitized and formatted meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return mixed
	 */
	public function fetch() {

		$value = $this->raw();

		return $value ? $this->sanitize( $value ) : '';
	}

	/**
	 * Sanitizes a value by applying any sanitization callbacks.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  mixed     $value
	 * @return mixed
	 */
	protected function sanitize( $value ) {

		return apply_filters( "hybrid/media/meta/{$this->name}/sanitize", $value, $this );
	}
}
