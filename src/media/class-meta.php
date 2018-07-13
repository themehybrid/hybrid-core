<?php
/**
 * Media meta retriever.
 *
 * Simplifies the process of getting media metadata, which core WP has no
 * standardized methods for handling. This class allows you to get the metadata
 * for a single attachment post. Then, use the `get( $key )` method to get the
 * specific meta value needed, escaped and formatted for output.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Media;

use Hybrid\Contracts\MediaMeta;

/**
 * Media meta class.
 *
 * @since  5.0.0
 * @access public
 */
class Meta implements MediaMeta {

	/**
	 * The attachment post object.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    \WP_Post
	 */
	protected $post = null;

	/**
	 * Copy of attachment meta retrieved via `wp_get_attachment_metadata()`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $meta = [];

	/**
	 * Sets up the new media meta object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  \WP_Post|int  A post object or ID.
	 * @return void
	 */
	public function __construct( $post ) {

		$this->post = get_post( $post );
		$this->meta = wp_get_attachment_metadata( $this->post->ID );
	}

	/**
	 * Returns the escaped and formatted media meta.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $key
	 * @return string
	 */
	public function get( $key ) {

		// `filesize` is correct, but `file_size` matches up with the
		// other `file_*` meta names, so let's support both.
		if ( 'filesize' === $key ) {
			$key = 'file_size';
		}

		// Create the method name based on the given key.
		$method = str_replace( '_', '', lcfirst( ucwords( $key, '_' ) ) );

		// If we have a method for handling the particular meta, use it.
		// Otherwise, fall back to the raw data and escape.
		$value = method_exists( $this, $method ) ? $this->$method() : esc_html( $this->raw( $key ) );

		return apply_filters( "hybrid/media/meta/{$key}", $value );
	}

	/**
	 * Returns raw data from the media meta.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string  $key
	 * @return mixed
	 */
	protected function raw( $key ) {

		$value = '';

		// If the property exists in the meta array.
		if ( isset( $this->meta[ $key ] ) ) {

			$value = $this->meta[ $key ];

		// If the property exists in the image meta array.
		} elseif ( isset( $this->meta['image_meta'] ) && isset( $this->meta['image_meta'][ $key ] ) ) {

			$value = $this->meta['image_meta'][ $key ];

		// If the property exists in the video's audio meta array.
		} elseif ( isset( $this->meta['audio'] ) && isset( $this->meta['audio'][ $key ] ) ) {

			$value = $this->meta['audio'][ $key ];
		}

		return $value;
	}

	/**
	 * Returns the camera aperture for an image.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function aperture() {

		$aperture = $this->raw( 'aperture' );

		if ( $aperture ) {

			$aperture = sprintf(
				'<sup>f</sup>&#8260;<sub>%s</sub>',
				absint( $aperture )
			);
		}

		return $aperture;
	}

	/**
	 * Returns the created timestamp for an image.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function createdTimestamp() {

		$timestamp = $this->raw( 'created_timestamp' );

		if ( $timestamp ) {

			$timestamp = date_i18n(
				get_option( 'date_format' ),
				strip_tags( $timestamp )
			);
		}

		return $timestamp;
	}

	/**
	 * Returns the media dimensions (width/height).
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function dimensions() {

		$dimensions = '';
		$width      = $this->raw( 'width'  );
		$height     = $this->raw( 'height' );

		if ( ! empty( $width ) && ! empty( $height ) ) {

			$dimensions = sprintf(
				// Translators: Media dimensions - 1 is width and 2 is height.
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $width  ) ),
				number_format_i18n( absint( $height ) )
			);
		}

		return $dimensions;
	}

	/**
	 * Returns the media file name, linked to the original media file.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function fileName() {

		$filename = basename( get_attached_file( $this->post->ID ) );

		if ( $filename ) {

			$filename = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_get_attachment_url( $this->post->ID ) ),
				$filename
			);
		}

		return $filename;
	}

	/**
	 * Returns the media file size.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function fileSize() {

		$filesize = isset( $this->meta['filesize'] ) ? $this->meta['filesize'] : '';

		if ( ! $filesize ) {
			$file = get_attached_file( $this->post->ID );

			if ( file_exists( $file ) ) {
				$filesize = filesize( $file );
			}
		}

		if ( $filesize ) {

			$filesize = size_format( strip_tags( $filesize ), 2 );
		}

		return $filesize;
	}

	/**
	 * Returns the media file type.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function fileType() {

		$type = '';

		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->post->ID ), $matches ) ) {

			$type = $matches[1];
		}

		return $type ? esc_html( strtoupper( $type ) ) : '';
	}

	/**
	 * Returns the camera focal length for an image.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function focalLength() {

		$focal_length = $this->raw( 'focal_length' );

		if ( $focal_length ) {

			// Translators: %s is the focal length of a camera.
			$focal_length = sprintf(
				__( '%s mm', 'hybrid-core' ),
				absint( $focal_length )
			);
		}

		return $focal_length;
	}

	/**
	 * Returns the lyrics for an audio file.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function lyrics() {

		$lyrics = '';

		// Look for the 'unsynchronised_lyric' tag.
		if ( isset( $this->meta['unsynchronised_lyric'] ) ) {
			$lyrics = $this->meta['unsynchronised_lyric'];

		// Seen this misspelling of the id3 tag.
		} elseif ( isset( $this->meta['unsychronised_lyric'] ) ) {
			$lyrics = $this->meta['unsychronised_lyric'];
		}

		if ( $lyrics ) {
			$lyrics = strip_tags( $lyrics );
			$lyrics = wptexturize( $lyrics );
			$lyrics = convert_chars( $lyrics );
			$lyrics = wpautop( $lyrics );
		}

		return $lyrics;
	}

	/**
	 * Returns the media file mime type.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function mimeType() {

		$mime_type = get_post_mime_type( $this->post );

		if ( ! $mime_type ) {
			$mime_type = $this->raw( 'mime_type' );
		}

		return $mime_type ? esc_html( $mime_type ) : '';
	}

	/**
	 * Returns the camera shutter speed for an image.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function shutterSpeed() {

		$shutter = $this->raw( 'shutter_speed' );

		// If a shutter speed is given, format the float into a fraction.
		if ( $shutter ) {

			$shutter = $speed = floatval( strip_tags( $shutter ) );

			if ( ( 1 / $speed ) > 1 ) {
				$shutter = sprintf( '<sup>%s</sup>&#8260;', number_format_i18n( 1 ) );

				if ( number_format( ( 1 / $speed ), 1 ) ==  number_format( ( 1 / $speed ), 0 ) ) {

					$shutter .= sprintf(
						'<sub>%s</sub>',
						number_format_i18n( ( 1 / $speed ), 0, '.', '' )
					);

				} else {

					$shutter .= sprintf(
						'<sub>%s</sub>',
						number_format_i18n( ( 1 / $speed ), 1, '.', '' )
					);
				}
			}

			// Translators: %s is the shutter speed of a camera.
			$shutter = sprintf( __( '%s sec', 'hybrid-core' ), $shutter );
		}

		return $shutter;
	}
}
