<?php
/**
 * SVG class.
 *
 * This is an SVG system for displaying SVGs in themes.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Svg;

use Hybrid\Contracts\Fetchable;
use Hybrid\Contracts\Renderable;
use function Hybrid\attributes;

class Svg implements Fetchable, Renderable {

	/**
	 * The name of the SVG object.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = '';

	/**
	 * The SVG file that we're getting. Use a relative path to the theme
	 * folder where the file is.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $file = '';

	/**
	 * Used to add or replace an existing `<title>` element in the SVG.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $title = '';

	/**
	 * Used to add or replace an existing `<desc>` element in the SVG.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $desc = '';

	/**
	 * Path info about the file.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $pathinfo = [];

	/**
	 * Sets up the object properties.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $file
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $file, $args = [] ) {

		// If any of the arguments match a class property, set that
		// property to the argument value.
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		// Define the file property.
		$this->file = $file;

		// Get the file path info.
		$this->pathinfo = pathinfo( $this->file );

		// If the file has no extension, add a `.svg`.
		if ( ! isset( $this->pathinfo['extension'] ) ) {
			$this->file = "{$this->file}.svg";
		}

		// Get a name for use in hooks and such.
		$this->name = isset( $this->pathinfo['filename'] )
		              ? $this->pathinfo['filename']
			      : basename( $this->file );
	}

	/**
	 * Returns the SVG output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function fetch() {

		$path = trim( apply_filters( 'hybrid/svg/path', '' ), '/' );

		$file = $path ? "{$path}/{$this->file}" : $this->file;

		$svg = file_get_contents( get_theme_file_path( $file ) );

		if ( ! $svg ) {
			return '';
		}

		// Get the attributes and inner HTML.
		preg_match( '/<svg(.*?)>(.*?)<\/svg>/is', $svg, $matches );

		if ( ! empty( $matches ) && isset( $matches[1] ) && isset( $matches[2] ) ) {

			$inner_html = $matches[2];

			// Create an array of existing attributes.
			$atts = wp_kses_hair( $matches[1], [ 'http', 'https' ] );

			// Sets up our attributes array.
			$attr = array_combine(
				array_column( $atts, 'name' ),
				array_column( $atts, 'value' )
			);

			// This doesn't actually help us in any way because we're
			// not building the `<title>` and `<desc>` elements.
			if ( $this->title ) {
				$unique_id = esc_attr( uniqid() );

				$attr['aria-labelledby'] = sprintf(
					$this->desc ? 'svg-title-%1$s svg-desc-%1$s' : 'svg-title-%s', $unique_id
				);

				$patterns = [
					'/<title.*?<\/title>/is',
					'/<desc.*?<\/desc>/is'
				];

				$inner_html = preg_replace( $patterns, '', $inner_html );

				$title_desc = sprintf(
					'<title id="svg-title-%s">%s</title>',
					$unique_id,
					esc_html( $this->title )
				);

				if ( $this->desc ) {
					$title_desc .= sprintf(
						'<desc id="svg-desc-%s">%s</desc>',
						$unique_id,
						esc_html( $this->desc )
					);
				}

				$inner_html = $title_desc . $inner_html;

			} else {
				$attr['aria-hidden'] = 'true';
				$attr['focusable']   = 'false';
			}

			$attr['role'] = 'img';

			// Get an attributes object.
			$attr = attributes( 'svg', $this->name, $attr );

			$svg = sprintf( '<svg %s>%s</svg>', $attr->fetch(), $inner_html );
		}

		return $svg;
	}

	/**
	 * Renders the SVG output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function render() {

		echo $this->fetch();
	}
}
