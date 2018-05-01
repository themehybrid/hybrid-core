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

namespace Hybrid\Media;

use Hybrid\Contracts\Fetchable;
use Hybrid\Contracts\Renderable;
use Hybrid\Attributes\Attributes;

class Svg implements Fetchable, Renderable {

	// Can/should we use these with inline svgs?
	protected $title = '';
	protected $desc = '';

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
		$this->name = $this->pathinfo['filename'];
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

			// Create an array of existing attributes.
			$atts = wp_kses_hair( $matches[1], [ 'http', 'https'] );

			// Sets up our attributes array.
			$attr = array_combine(
				array_column( $atts, 'name' ),
				array_column( $atts, 'value' )
			);

			// This doesn't actually help us in any way because we're
			// not building the `<title>` and `<desc>` elements.
			if ( $this->title ) {
				$attr['aria-labelledby'] = sprintf(
					$this->desc ? 'title-%1$s desc-%1$s' : 'title-%s', uniqid()
				);
			} else {
				$attr['aria-hidden'] = 'true';
				$attr['focusable']   = 'false';
			}

			$attr['role'] = 'img';

			// Get an attributes object.
			$attr = new Attributes( 'svg', $this->name, $attr );

			$svg = sprintf( '<svg %s>%s</svg>', $attr->fetch(), $matches[2] );
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
