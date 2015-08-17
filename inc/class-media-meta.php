<?php
/**
 * Media metadata class. This class is for getting and formatting attachment media file metadata. This
 * is for metadata about the actual file and not necessarily any post metadata.  Currently, only
 * image, audio, and video files are handled.
 *
 * Theme authors need not access this class directly.  Instead, utilize the template tags in the
 * `/inc/template-media.php` file.
 *
 * @package    Hybrid
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Gets attachment media file metadata.  Each piece of meta will be escaped and formatted when
 * returned so that theme authors can properly utilize it within their themes.
 *
 * Theme authors shouldn't access this class directly.  Instead, utilize the `hybrid_media_meta()`
 * and `hybrid_get_media_meta()` functions.
 *
 * @since  3.0.0
 * @access public
 */
class Hybrid_Media_Meta {

	/**
	 * Arguments passed in.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    array
	 */
	protected $post_id  = 0;

	/**
	 * Metadata from the wp_get_attachment_metadata() function.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    array
	 */
	protected $meta  = array();

	/**
	 * Type of media for the current attachment.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    string  image|audio|video
	 */
	protected $type = '';

	/**
	 * Allowed media types.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	protected $allowed_types = array( 'image', 'audio', 'video' );

	/* ====== Magic Methods ====== */

	/**
	 * Sets up and runs the functionality for getting the attachment meta.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $post_id ) {

		$this->post_id  = $post_id;
		$this->meta     = wp_get_attachment_metadata( $this->post_id );
		$this->type     = hybrid_get_attachment_type( $this->post_id );

		// If we have a type that's in the whitelist, run filters.
		if ( $this->type && in_array( $this->type, $this->allowed_types ) ) {

			// Run common media filters for any media type.
			$this->media_filters();

			// Run type-specific filters.
			call_user_func( array( $this, "{$this->type}_filters" ) );
		}
	}

	/**
	 * Magic method for getting media object properties.  Let's keep from failing if a theme
	 * author attempts to access a property that doesn't exist.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return mixed
	 */
	public function __get( $property ) {

		return isset( $this->property ) ? $this->property : $this->get( $property );
	}

	/* ====== Protected Methods ====== */

	/**
	 * Function for escaping properties when there is not a specific method for handling them
	 * within the class.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @param  string|int  $value
	 * @param  string      $property
	 * @return string|int
	 */
	protected function escape( $value, $property ) {

		if ( has_filter( "hybrid_media_meta_escape_{$property}" ) )
			return apply_filters( "hybrid_media_meta_escape_{$property}", $value, $this->type );

		return is_numeric( $value ) ? intval( $value ) : esc_html( $value );
	}

	/**
	 * Adds filters for common media meta.
	 *
	 * Properties: file_name, filesize, file_type, mime_type
	 *
	 * @since  3.0.0
	 * @access protected
	 * @return void
	 */
	protected function media_filters() {

		add_filter( 'hybrid_media_meta_escape_file_name', array( $this, 'file_name' ), 5 );
		add_filter( 'hybrid_media_meta_escape_filesize',  array( $this, 'file_size' ), 5 );
		add_filter( 'hybrid_media_meta_escape_file_size', array( $this, 'file_size' ), 5 ); // alias for filesize
		add_filter( 'hybrid_media_meta_escape_file_type', array( $this, 'file_type' ), 5 );
		add_filter( 'hybrid_media_meta_escape_mime_type', array( $this, 'mime_type' ), 5 );
	}

	/**
	 * Adds filters for image meta.
	 *
	 * Properties: aperture, camera, caption, copyright, credit, created_timestamp, dimensions,
	 *             focal_length, iso, shutter_speed
	 *
	 * @since  3.0.0
	 * @access protected
	 * @return void
	 */
	protected function image_filters() {

		add_filter( 'hybrid_media_meta_escape_dimensions',        array( $this, 'dimensions'        ), 5 );
		add_filter( 'hybrid_media_meta_escape_created_timestamp', array( $this, 'created_timestamp' ), 5 );
		add_filter( 'hybrid_media_meta_escape_aperture',          array( $this, 'aperture'          ), 5 );
		add_filter( 'hybrid_media_meta_escape_shutter_speed',     array( $this, 'shutter_speed'     ), 5 );
		add_filter( 'hybrid_media_meta_escape_focal_length',      'absint',                            5 );
		add_filter( 'hybrid_media_meta_escape_iso',               'absint',                            5 );
	}

	/**
	 * Adds filters for audio meta.
	 *
	 * Properties: album, artist, composer, genre, length_formatted, lyrics, track_number, year
	 *
	 * @since  3.0.0
	 * @access protected
	 * @return void
	 */
	protected function audio_filters() {

		add_filter( 'hybrid_media_meta_escape_track_number', 'absint', 5 );
		add_filter( 'hybrid_media_meta_escape_year',         'absint', 5 );

		// Filters for the audio transcript.
		add_filter( 'hybrid_media_meta_escape_lyrics', array( $this, 'lyrics' ), 5 );
		add_filter( 'hybrid_media_meta_escape_lyrics', 'wptexturize',            10 );
		add_filter( 'hybrid_media_meta_escape_lyrics', 'convert_chars',          15 );
		add_filter( 'hybrid_media_meta_escape_lyrics', 'wpautop',                20 );
	}

	/**
	 * Adds filters for video meta.
	 *
	 * Properties: dimensions, length-formatted
	 *
	 * @since  3.0.0
	 * @access protected
	 * @return void
	 */
	protected function video_filters() {

		add_filter( 'hybrid_media_meta_escape_dimensions', array( $this, 'dimensions' ), 5 );
	}

	/* ====== Public Methods ====== */

	/**
	 * Method for grabbing meta formatted metadata by key.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return mixed
	 */
	public function get( $property ) {

		$value = null;

		// If the property exists in the meta array.
		if ( isset( $this->meta[ $property ] ) )
			$value = $this->meta[ $property ];

		// If the property exists in the image meta array.
		elseif ( 'image' === $this->type && isset( $this->meta['image_meta'][ $property ] ) )
			$value = $this->meta['image_meta'][ $property ];

		// If the property exists in the video's audio meta array.
		elseif ( 'video' === $this->type && isset( $this->meta['audio'][ $property ] ) )
			$value = $this->meta['audio'][ $property ];

		// Escape and return.
		return $this->escape( $value, $property );
	}

	/**
	 * Image/Video meta. Media width + height dimensions.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $dimensions
	 * @return string
	 */
	public function dimensions( $dimensions ) {

		// If there's a width and height.
		if ( ! empty( $this->meta['width'] ) && ! empty( $this->meta['height'] ) ) {

			$dimensions = sprintf(
				// Translators: Media dimensions - 1 is width and 2 is height.
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $this->meta['width'] ) ),
				number_format_i18n( absint( $this->meta['height'] ) )
			);
		}

		return $dimensions;
	}

	/**
	 * Image meta.  Date the image was created.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $timestamp
	 * @return string
	 */
	public function created_timestamp( $timestamp ) {

		if ( ! empty( $this->meta['image_meta']['created_timestamp'] ) ) {

			$timestamp = date_i18n(
				get_option( 'date_format' ),
				strip_tags( $this->meta['image_meta']['created_timestamp'] )
			);
		}

		return $timestamp;
	}

	/**
	 * Image meta.  Camera aperture in the form of `f/{$aperture}`.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $aperture
	 * @return string
	 */
	public function aperture( $aperture ) {

		if ( !empty( $this->meta['image_meta']['aperture'] ) )
			$aperture = sprintf( '<sup>f</sup>&#8260;<sub>%s</sub>', absint( $this->meta['image_meta']['aperture'] ) );

		return $aperture;
	}

	/**
	 * Image meta. Camera shutter speed in seconds (i18n number format).
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $shutter
	 * @return string
	 */
	public function shutter_speed( $shutter ) {

		// If a shutter speed is given, format the float into a fraction.
		if ( ! empty( $this->meta['image_meta']['shutter_speed'] ) ) {

			$shutter = $speed = floatval( strip_tags( $this->meta['image_meta']['shutter_speed'] ) );

			if ( ( 1 / $speed ) > 1 ) {
				$shutter = sprintf( '<sup>%s</sup>&#8260;', number_format_i18n( 1 ) );

				if ( number_format( ( 1 / $speed ), 1 ) ==  number_format( ( 1 / $speed ), 0 ) )
					$shutter .= sprintf( '<sub>%s</sub>', number_format_i18n( ( 1 / $speed ), 0, '.', '' ) );

				else
					$shutter .= sprintf( '<sub>%s</sub>', number_format_i18n( ( 1 / $speed ), 1, '.', '' ) );
			}
		}

		return $shutter;
	}

	/**
	 * Audio meta. Lyrics/transcript for an audio file.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function lyrics( $lyrics ) {

		// Look for the 'unsynchronised_lyric' tag.
		if ( isset( $this->meta['unsynchronised_lyric'] ) )
			$lyrics = $this->meta['unsynchronised_lyric'];

		// Seen this misspelling of the id3 tag.
		elseif ( isset( $this->meta['unsychronised_lyric'] ) )
			$lyrics = $this->meta['unsychronised_lyric'];

		return strip_tags( $lyrics );
	}

	/**
	 * Name of the file linked to the permalink for the file.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return string
	 */
	public function file_name() {

		return sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_get_attachment_url( $this->post_id ) ),
			basename( get_attached_file( $this->post_id ) )
		);
	}

	/**
	 * Audio/Video meta. Size of the file.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  int    $file_size
	 * @return int
	 */
	public function file_size( $file_size ) {

		return ! empty( $this->meta['filesize'] ) ? size_format( strip_tags( $this->meta['filesize'] ), 2 ) : $file_size;
	}

	/**
	 * Type of file.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $file_type
	 * @return string
	 */
	public function file_type( $file_type ) {

		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->post_id ), $matches ) )
			$file_type = esc_html( strtoupper( $matches[1] ) );

		return $file_type;
	}

	/**
	 * Mime type for the file.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $mime_type
	 * @return string
	 */
	public function mime_type( $mime_type ) {

		$mime_type = get_post_mime_type( $this->post_id );

		if ( empty( $mime_type ) && ! empty( $this->meta['mime_type'] ) )
			$mime_type = $this->meta['mime_type'];

		return esc_html( $mime_type );
	}
}
