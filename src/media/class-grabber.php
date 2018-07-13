<?php
/**
 * Media Grabber - A script for grabbing media related to a post.
 *
 * Hybrid Media Grabber is a script for pulling media either from the post
 * content or attached to the post.  It's an attempt to consolidate the various
 * methods that users have used over the years to embed media into their posts.
 * This script was written so that theme developers could grab that media and
 * use it in interesting ways within their themes.  For example, a theme could
 * get a video and display it on archive pages alongside the post excerpt or
 * pull it out of the content to display it above the post on single post views.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Media;

use Hybrid\Contracts\MediaGrabber;

/**
 * Grabs media related to the post.
 *
 * @since  5.0.0
 * @access public
 * @return void
 */
class Grabber implements MediaGrabber {

	/**
	 * The HTML version of the media to return.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $media = '';

	/**
	 * The original media taken from the post content.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $original_media = '';

	/**
	 * The type of media to get.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'video';

	/**
	 * Array of allowed types of media that the script can search for.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $allowed_types = [ 'audio', 'gallery', 'video' ];

	/**
	 * The content to search for embedded media within.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $content = '';

	/**
	 * Post ID for the media.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $post_id = 0;

	/**
	 * HTML to add before the output.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $before = '';

	/**
	 * HTML to append to the output.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $after = '';

	/**
	 * Whether to split the media from the post content.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $split = false;

	/**
	 * A width to constrain the media to. Uses the theme's `$content_width`
	 * by default.  In most scenarios, this shouldn't be changed.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $width = 0;

	/**
	 * Whether to search content for known media shortcodes.  Pass an array
	 * of shortcode tags for a custom lookup.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    bool|array
	 */
	protected $shortcodes = true;

	/**
	 * Whether to use WP's built-in autoembed feature to pull media from the
	 * post content.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $autoembeds = true;

	/**
	 * Whether to search for other media embedded into the post content.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $embedded = true;

	/**
	 * Whether to look for media attached to the post as a last resort.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $attached = true;

	/**
	 * An array of known video shortcodes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $video_shortcodes = [
		'playlist',
		'embed',
		'video',
		'blip.tv',
		'dailymotion',
		'flickr',
		'ted',
		'vimeo',
		'vine',
		'youtube',
		'wpvideo',
	];

	/**
	 * An array of known audio shortcodes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $audio_shortcodes = [
		'playlist',
		'embed',
		'audio',
		'bandcamp',
		'soundcloud'
	];

	/**
	 * An array of known gallery shortcodes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $gallery_shortcodes = [
		'gallery'
	];

	/**
	 * Constructor method.  Sets up the media grabber.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  array   $args
	 * @global object  $wp_embed
	 * @global int     $content_width
	 * @return void
	 */
	public function __construct( $args = [] ) {
		global $wp_embed, $content_width;

		array_map( function( $key ) use ( $args ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}

		}, array_keys( get_object_vars( $this ) ) );

		// If not post ID is set, assume we're in The Loop and use the
		// current post ID.
		if ( ! $this->post_id ) {
			$this->post_id = get_the_ID();
		}

		// If no width is set, use the theme's content width.
		if ( ! $this->width ) {
			$this->width = $content_width;
		}

		// Reset to look for videos if the type is not allowed.
		if ( ! in_array( $this->type, $this->allowed_types ) ) {
			$this->type = 'video';
		}

		// Get the raw post content.
		$this->content = get_post_field( 'post_content', $this->post_id, 'raw' );
	}

	/**
	 * Renders the found media.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function render() {

		echo $this->fetch();
	}

	/**
	 * Basic method for returning the media found.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function fetch() {

		return apply_filters( 'hybrid/media/grabber/media', $this->locate(), $this );
	}

	/**
	 * Tries several methods to find media related to the post.  Returns the
	 * found media.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function locate() {

		// Don't return a link if embeds don't work. Need media or nothing at all.
		add_filter( 'embed_maybe_make_link', '__return_false' );

		// Get the media if the post type is an attachment.
		if ( 'attachment' === get_post_type( $this->post_id ) ) {
			$this->media = $this->locateSelfMedia();
		}

		// Find media in the post content based on WordPress' media-related shortcodes.
		if ( ! $this->media && $this->shortcodes ) {
			$this->media = $this->locateShortcodeMedia();
		}

		// If no media is found and autoembeds are enabled, check for autoembeds.
		if ( ! $this->media && get_option( 'embed_autourls' ) && $this->autoembeds ) {
			$this->media = $this->locateAutoembedMedia();
		}

		// If no media is found, check for media HTML within the post content.
		if ( ! $this->media && $this->embedded ) {
			$this->media = $this->locatedEmbeddedMedia();
		}

		// If no media is found, check for media attached to the post.
		if ( ! $this->media && $this->attached ) {
			$this->media = $this->locateAttachedMedia();
		}

		// If media is found, let's run a few things.
		if ( $this->media ) {

			// Split the media from the content.
			if ( true === $this->split && ! empty( $this->original_media ) ) {
				add_filter( 'the_content', [ $this, 'split' ], 5 );
			}

			// Filter the media dimensions and add the before/after HTML.
			$this->media = $this->before . $this->filterDimensions( $this->media ) . $this->after;
		}

		// Remove our filter from earlier.
		remove_filter( 'embed_maybe_make_link', '__return_false' );

		return $this->media;
	}

	/**
	 * If the post type itself is an attachment, call the shortcode wrapper
	 * function for handling the media.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function locateSelfMedia() {

		$url  = esc_url( wp_get_attachment_url( $this->post_id ) );
		$mime = get_post_mime_type( $this->post_id );

		list( $type, $subtype ) = false !== strpos( $mime, '/' ) ? explode( '/', $mime ) : [ $mime, '' ];

		return in_array( $type, [ 'audio', 'video' ] )
		       ? call_user_func( "wp_{$type}_shortcode", [ 'src' => $url ] )
		       : '';
	}

	/**
	 * Searches for shortcodes in the post content and sets the generated
	 * shortcode output if one is found.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function locateShortcodeMedia() {

		// Finds matches for shortcodes in the content.
		preg_match_all( '/' . get_shortcode_regex() . '/s', $this->content, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return '';
		}

		// Create a list of allowed shortcodes.
		$shortcodes = is_array( $this->shortcodes ) ? $this->shortcodes : [];

		// We need to add any known shortcodes for the given type.
		$property = "{$this->type}_shortcodes";

		if ( property_exists( $this, $property ) ) {

			$shortcodes = array_merge(
				$shortcodes,
				apply_filters(
					"hybrid/media/grabber/shortcodes/{$this->type}",
					$this->{$property}
				)
			);
		}

		// Loops through all of the shortcode matches. If we find one of
		// our shortcodes, run it.
		foreach ( $matches as $shortcode ) {

			if ( in_array( $shortcode[2], $shortcodes ) ) {

				$this->original_media = array_shift( $shortcode );

				return $this->doShortcode( $shortcode[2], $this->original_media );
			}
		}

		return '';
	}

	/**
	 * Uses WordPress' autoembed feature to automatically to handle media
	 * that's just input as a URL.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function locateAutoembedMedia() {

		// Searches for URLs on a line by themselves in the post content.
		preg_match_all( '|^\s*(https?://[^\s"]+)\s*$|im', $this->content, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) || ! is_array( $matches ) ) {
			return '';
		}

		foreach ( $matches as $value ) {

			// Let WP work its magic with the 'autoembed' method.
			$embed = $GLOBALS['wp_embed']->autoembed( $value[0] );

			if ( $embed ) {

				// If we're given a shortcode, roll with it.
				if ( preg_match( "/\[{$this->type}\s/", $embed ) ) {

					$this->original_media = $value[0];

					return $this->doShortcode( $this->type, $embed );
				}
			}
		}

		return '';
	}

	/**
	 * Grabs media embbeded into the content within <iframe>, <object>,
	 * <embed>, and other HTML methods for embedding media.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function locatedEmbeddedMedia() {

		$embedded_media = get_media_embedded_in_content( $this->content );

		if ( $embedded_media ) {
			return $this->original_media = array_shift( $embedded_media );
		}

		return '';
	}

	/**
	 * Gets media attached to the post.  Then, uses the WordPress [audio] or
	 * [video] shortcode to handle the HTML output of the media.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected function locateAttachedMedia() {

		// Gets media attached to the post by mime type.
		$attached_media = get_attached_media( $this->type, $this->post_id );

		// If media is found.
		if ( $attached_media ) {

			// Get the first attachment/post object found for the post.
			$post = array_shift( $attached_media );

			// Gets the URI for the attachment (the media file).
			$url = esc_url( wp_get_attachment_url( $post->ID ) );

			// Run the media as a shortcode using WordPress' built-in
			// [audio] and [video] shortcodes.
			if ( in_array( $this->type, [ 'audio', 'video' ] ) ) {

				return call_user_func( "wp_{$this->type}_shortcode", [ 'src' => $url ] );
			}
		}

		return '';
	}

	/**
	 * Helper function for running a shortcode.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $tag
	 * @param  string    $shortcode
	 * @return string
	 */
	protected function doShortcode( $tag, $shortcode ) {

		if ( 'embed' === $tag ) {

			return $GLOBALS['wp_embed']->run_shortcode( $shortcode );

		} elseif ( 'video' === $tag ) {

			// Need to filter dimensions here to overwrite WP's
			// `<div>` surrounding the [video] shortcode.
			return do_shortcode( $this->filterDimensions( $shortcode ) );
		}

		return do_shortcode( $shortcode );
	}

	/**
	 * Removes the found media from the content.  The purpose of this is so
	 * that themes can retrieve the media from the content and display it
	 * elsewhere on the page based on its design.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $content
	 * @return string
	 */
	public function split( $content ) {

		if ( get_the_ID() === (int) $this->post_id ) {

			$content = str_replace( $this->original_media, '', $content );
			$content = wp_kses_post( $content );
		}

		return $content;
	}

	/**
	 * Method for filtering the media's 'width' and 'height' attributes so
	 * that the theme can handle the dimensions how it sees fit.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string  $html
	 * @return string
	 */
	protected function filterDimensions( $html ) {

		$media_atts = [];
		$_html      = strip_tags( $html, '<object><embed><iframe><video>' );

		// Find the attributes of the media.
		$atts = wp_kses_hair( $_html, [ 'http', 'https' ] );

		// Loop through the media attributes and add them in key/value pairs.
		foreach ( $atts as $att ) {
			$media_atts[ $att['name'] ] = $att['value'];
		}

		// If no dimensions are found, just return the HTML.
		if ( empty( $media_atts ) || ! isset( $media_atts['width'] ) || ! isset( $media_atts['height'] ) ) {
			return $html;
		}

		// Set the max width.
		$max_width = $this->width;

		// Set the max height based on the max width and original width/height ratio.
		$max_height = round( $max_width / ( $media_atts['width'] / $media_atts['height'] ) );

		// Fix for Spotify embeds.
		if ( ! empty( $media_atts['src'] ) && preg_match( '#https?://(embed)\.spotify\.com/.*#i', $media_atts['src'], $matches ) ) {

			list( $max_width, $max_height ) = $this->spotifyDimensions( $media_atts );
		}

		// Calculate new media dimensions.
		$dimensions = wp_expand_dimensions(
			$media_atts['width'],
			$media_atts['height'],
			$max_width,
			$max_height
		);

		// Allow devs to filter the final width and height of the media.
		list( $width, $height ) = apply_filters(
			'hybrid/media/grabber/dimensions',
			$dimensions,                       // width/height array
			$media_atts,                       // media HTML attributes
			$this                              // media grabber object
		);

		// Set up the patterns for the 'width' and 'height' attributes.
		$patterns = [
			'/(width=[\'"]).+?([\'"])/i',
			'/(height=[\'"]).+?([\'"])/i',
			'/(<div.+?style=[\'"].*?width:.+?).+?(px;.+?[\'"].*?>)/i',
			'/(<div.+?style=[\'"].*?height:.+?).+?(px;.+?[\'"].*?>)/i'
		];

		// Set up the replacements for the 'width' and 'height' attributes.
		$replacements = [
			'${1}' . $width . '${2}',
			'${1}' . $height . '${2}',
			'${1}' . $width . '${2}',
			'${1}' . $height . '${2}',
		];

		// Filter the dimensions and return the media HTML.
		return preg_replace( $patterns, $replacements, $html );
	}

	/**
	 * Fix for Spotify embeds because they're the only embeddable service
	 * that doesn't work that well with custom-sized embeds.  So, we need to
	 * adjust this the best we can.  Right now, the only embed size that
	 * works for full-width embeds is the "compact" player (height of 80).
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array   $media_atts
	 * @return array
	 */
	protected function spotifyDimensions( $media_atts ) {

		$max_width  = $media_atts['width'];
		$max_height = $media_atts['height'];

		if ( 80 == $media_atts['height'] ) {
			$max_width  = $this->width;
		}

		return [ $max_width, $max_height ];
	}
}
