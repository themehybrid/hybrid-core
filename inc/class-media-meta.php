<?php
/**
 * Class for getting and formatting attachment metadata.  The class currently handles attachment metadata for 
 * the image, audio, and video mime types.  It may handle other types in the future, depending on the direction 
 * of WordPress core.  The purpose of this class is wrap up the return values of the core WP function 
 * `wp_get_attachment_metadata()` into a more usuable format for theme authors so that they can easily display 
 * data related to media in their themes.
 *
 * @package    Hybrid
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * @since  2.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function hybrid_media_meta( $meta_key, $args = array() ) {
	echo hybrid_get_media_meta( $meta_key, $args );
}

/**
 * @since  3.0.0
 */
function hybrid_get_media_meta( $meta_key, $args = array() ) {

	$args = wp_parse_args( $args, array( 'text' => '%s', 'before' => '', 'after' => '', 'wrap' => '<span %s>%s</span>' ) );

	$meta = Hybrid_Media_Factory::get_instance()->get_meta( get_the_ID() )->$meta_key;

	return $meta ? $args['before'] . sprintf( $args['wrap'], 'class="data"', sprintf( $args['text'], $meta ) ) . $args['after'] : '';
}

/**
 * Creates and houses media meta objects.  Don't access this class directly.  Utilize the 
 * `hybrid_media_meta()` or `hybrid_get_media_meta()` functions.
 *
 * @since  3.0.0
 */
class Hybrid_Media_Factory {

	/**
	 * @since  3.0.0
	 */
	public $media_objects = array();

	/**
	 * @since  3.0.0
	 */
	public function get_meta( $post_id ) {

		if ( !isset( $this->media_objects[ $post_id ] ) )
			$this->media_objects[ $post_id ] = new Hybrid_Media_Meta( $post_id );

		return $this->media_objects[ $post_id ];
	}

	/**
	 * @since  3.0.0
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new Hybrid_Media_Factory;

		return $instance;
	}
}

/**
 * Class for getting and formatting attachment metadata.
 *
 * @since  2.0.0
 * @access public
 */
class Hybrid_Media_Meta {

	/**
	 * Arguments passed in.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $post_id  = 0;

	/**
	 * Metadata from the wp_get_attachment_metadata() function.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $meta  = array();

	/**
	 * @since  3.0.0
	 */
	public $dimensions = '';

	/**
	 * @since  3.0.0
	 */
	public $created_timestamp = '';

	/**
	 * @since  3.0.0
	 */
	public $date = '';

	/**
	 * @since  3.0.0
	 */
	public $camera = '';

	/**
	 * @since  3.0.0
	 */
	public $aperture = '';

	/**
	 * @since  3.0.0
	 */
	public $focal_length = '';

	/**
	 * @since  3.0.0
	 */
	public $iso = '';

	/**
	 * @since  3.0.0
	 */
	public $shutter_speed = '';

	/**
	 * @since  3.0.0
	 */
	public $length_formatted = '';

	/**
	 * @since  3.0.0
	 */
	public $artist = '';

	/**
	 * @since  3.0.0
	 */
	public $composer = '';

	/**
	 * @since  3.0.0
	 */
	public $album = '';

	/**
	 * @since  3.0.0
	 */
	public $track_number = '';

	/**
	 * @since  3.0.0
	 */
	public $year = '';

	/**
	 * @since  3.0.0
	 */
	public $genre = '';

	/**
	 * @since  3.0.0
	 */
	public $file_name = '';

	/**
	 * @since  3.0.0
	 */
	public $file_size = '';

	/**
	 * @since  3.0.0
	 */
	public $file_type = '';

	/**
	 * @since  3.0.0
	 */
	public $mime_type = '';

	/**
	 * Sets up and runs the functionality for getting the attachment meta.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $post_id ) {

		$this->post_id  = $post_id;
		$this->meta     = wp_get_attachment_metadata( $this->post_id );

		/* If the attachment is an image. */
		if ( wp_attachment_is_image( $this->post_id ) )
			$this->image_meta();

		/* If the attachment is audio. */
		elseif ( hybrid_attachment_is_audio( $this->post_id ) )
			$this->audio_meta();

		/* If the attachment is video. */
		elseif ( hybrid_attachment_is_video( $this->post_id ) )
			$this->video_meta();
	}

	/**
	 * Adds and formats image metadata for the items array.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function image_meta() {

		$this->dimensions();
		$this->created_timestamp();
		$this->camera();
		$this->aperture();
		$this->focal_length();
		$this->iso();
		$this->shutter_speed();
	}

	/**
	 * Adds and formats audio metadata for the items array.
	 *
	 * Note that we're purposely leaving out the "transcript/lyrics" metadata in this instance.  This 
	 * is because it doesn't fit in well with how other metadata works on display.  There's a separate 
	 * function for that called `hybrid_get_audio_transcript()`.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function audio_meta() {

		$this->length_formatted();
		$this->artist();
		$this->composer();
		$this->album();
		$this->track_number();
		$this->year();
		$this->genre();
		$this->file_name();
		$this->file_size();
		$this->file_type();
		$this->mime_type();
	}

	/**
	 * Adds and formats video meta data for the items array.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function video_meta() {

		$this->length_formatted();
		$this->dimensions();
		$this->file_name();
		$this->file_size();
		$this->file_type();
		$this->mime_type();
	}

	/**
	 * @since  3.0.0
	 */
	public function dimensions() {

		/* If there's a width and height. */
		if ( !empty( $this->meta['width'] ) && !empty( $this->meta['height'] ) ) {

			/* Translators: Media dimensions - 1 is width and 2 is height. */
			$dimensions = sprintf(
				esc_html__( '%1$s &#215; %2$s', 'hybrid-core' ),
				number_format_i18n( absint( $this->meta['width'] ) ), 
				number_format_i18n( absint( $this->meta['height'] ) )
			);

			$this->dimensions = sprintf( '<a href="%s">%s</a>', esc_url( wp_get_attachment_url() ), $dimensions );
		}
	}

	/**
	 * @since  3.0.0
	 */
	public function created_timestamp() {

		if ( !empty( $this->meta['image_meta']['created_timestamp'] ) ) {

			$this->date = $this->created_timestamp = date_i18n(
				get_option( 'date_format' ),
				strip_tags( $this->meta['image_meta']['created_timestamp'] )
			);
		}
	}

	/**
	 * @since  3.0.0
	 */
	public function camera() {

		if ( !empty( $this->meta['image_meta']['camera'] ) )
			$this->camera = esc_html( $this->meta['image_meta']['camera'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function aperture() {

		if ( !empty( $this->meta['image_meta']['aperture'] ) )
			$this->aperture = sprintf( '<sup>f</sup>&#8260;<sub>%s</sub>', absint( $this->meta['image_meta']['aperture'] ) );
	}

	/**
	 * @since  3.0.0
	 */
	public function focal_length() {

		if ( !empty( $this->meta['image_meta']['focal_length'] ) ) {

			/* Translators: %s is the camera focal length in millimeters. */
			$this->focal_length = sprintf( esc_html__( '%s mm', 'hybrid-core' ), absint( $this->meta['image_meta']['focal_length'] ) );
		}
	}

	/**
	 * @since  3.0.0
	 */
	public function iso() {

		if ( !empty( $this->meta['image_meta']['iso'] ) )
			$this->iso = absint( $this->meta['image_meta']['iso'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function shutter_speed() {

		/* If a shutter speed is given, format the float into a fraction and add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['shutter_speed'] ) ) {

			$this->meta['image_meta']['shutter_speed'] = floatval( $this->meta['image_meta']['shutter_speed'] );

			if ( ( 1 / $this->meta['image_meta']['shutter_speed'] ) > 1 ) {
				$shutter_speed = '<sup>' . number_format_i18n( 1 ) . '</sup>&#8260;';

				if ( number_format( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 1 ) ==  number_format( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 0 ) )
					$shutter_speed .= sprintf( '<sub>%s</sub>', number_format_i18n( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 0, '.', '' ) );
				else
					$shutter_speed .= sprintf( '<sub>%s</sub>', number_format_i18n( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 1, '.', '' ) );
			} else {
				$shutter_speed = $this->meta['image_meta']['shutter_speed'];
			}

			/* Translators: %s is the camera shutter speed. "sec" is an abbreviation for "seconds". */
			$this->shutter_speed = sprintf( esc_html__( '%s sec', 'hybrid-core' ), $shutter_speed );
		}
	}

	/**
	 * @since  3.0.0
	 */
	public function length_formatted() {

		if ( !empty( $this->meta['length_formatted'] ) )
			$this->length_formatted = esc_html( $this->meta['length_formatted'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function artist() {

		if ( !empty( $this->meta['artist'] ) )
			$this->artist = esc_html( $this->meta['artist'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function composer() {

		if ( !empty( $this->meta['composer'] ) )
			$this->composer = esc_html( $this->meta['composer'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function album() {

		if ( !empty( $this->meta['album'] ) )
			$this->album = esc_html( $this->meta['album'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function track_number() {

		if ( !empty( $this->meta['track_number'] ) )
			$this->track_number = absint( $this->meta['track_number'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function year() {

		if ( !empty( $this->meta['year'] ) )
			$this->year = absint( $this->meta['year'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function genre() {

		if ( !empty( $this->meta['genre'] ) )
			$this->genre = esc_html( $this->meta['genre'] );
	}

	/**
	 * @since  3.0.0
	 */
	public function file_name() {

		$this->file_name = sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_get_attachment_url( $this->post_id ) ),
			basename( get_attached_file( $this->post_id ) )
		);
	}

	/**
	 * @since  3.0.0
	 */
	public function file_size() {

		if ( !empty( $this->meta['filesize'] ) )
			$this->filesize = size_format( strip_tags( $this->meta['filesize'] ), 2 );
	}

	/**
	 * @since  3.0.0
	 */
	public function file_type() {

		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->post_id ), $matches ) )
			$this->file_type = esc_html( strtoupper( $matches[1] ) );
	}

	/**
	 * @since  3.0.0
	 */
	public function mime_type() {

		if ( !empty( $this->meta['mime_type'] ) )
			$this->mime_type = esc_html( $this->meta['mime_type'] );
	}
}
