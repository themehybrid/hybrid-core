<?php
/**
 * Attributes class.
 *
 * This is an HTML attributes class system. The purpose is to provide devs a
 * system for adding filterable attributes.  This is sort of like `body_class()`,
 * `post_class()`, and `comment_class()` on steroids. However, it can handle
 * attributes for any elements.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Attr;

use Hybrid\Contracts\Attr\Attributes;

/**
 * Attributes class.
 *
 * @since  5.0.0
 * @access public
 */
class Attr implements Attributes {

	/**
	 * The name/ID of the element (e.g., `sidebar`).
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = '';

	/**
	 * A specific context for the element (e.g., `primary`).
	 *
	 * @since  5.0.0
	 * @access public
	 * @var    string
	 */
	protected $context = '';

	/**
	 * The input attributes first passed in.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $input = [];

	/**
	 * Stored array of attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $attr = [];

	/**
	 * Stored array of data.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $data = [];

	/**
	 * Outputs an HTML element's attributes.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $slug
	 * @param  string  $context
	 * @param  array   $attr
	 * @return void
	 */
	public function __construct( $name, $context = '', array $attr = [] ) {

		$this->name    = $name;
		$this->context = $context;
		$this->input   = $attr;
	}

	/**
	 * When attempting to use the object as a string, return the attributes
	 * output as a string.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {

		return $this->render();
	}

	/**
	 * Outputs an escaped string of attributes for use in HTML.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function display() {

		echo $this->render();
	}

	/**
	 * Returns an escaped string of attributes for use in HTML.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function render() {

		$html = '';

		foreach ( $this->all() as $name => $value ) {

			$esc_value = '';

			// If the value is a link `href`, use `esc_url()`.
			if ( $value !== false && 'href' === $name ) {
				$esc_value = esc_url( $value );

			} elseif ( $value !== false ) {
				$esc_value = esc_attr( $value );
			}

			$html .= false !== $value ? sprintf( ' %s="%s"', esc_html( $name ), $esc_value ) : esc_html( " {$name}" );
		}

		return trim( $html );
	}

	/**
	 * Adds custom data to the attribute object.
	 *
	 * @since  5.2.0
	 * @access public
	 * @param  string|array  $name
	 * @param  mixed         $value
	 * @return $this
	 */
	public function with( $name, $value = null ) {

		if ( is_array( $name ) ) {
			$this->data = array_merge( $this->data, $name );
		} else {
			$this->data[ $name ] = $value;
		}

		return $this;
	}

	/**
	 * Returns a single, unescaped attribute's value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @return string
	 */
	public function get( $name ) {

		$attr = $this->all();

		return isset( $attr[ $name ] ) ? $attr[ $name ] : '';
	}

	/**
	 * Filters and returns the array of attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	public function all() {

		// If we already have attributes, let's return them and bail.
		if ( $this->attr ) {
			return $this->attr;
		}

		$defaults = [];

		// If the a class was input, we want to go ahead and set that as
		// the default class.  That way, filters can know early on that
		// a class has already been declared. Any filters on the defaults
		// should, ideally, respect any classes that already exist.
		if ( isset( $this->input['class'] ) ) {
			$defaults['class'] = $this->input['class'];

			// This is kind of a hacky way to keep the class input
			// from overwriting everything later.
			unset( $this->input['class'] );

		// If no class was input, let's build a custom default.
		} else {
			$defaults['class'] = $this->context ? "{$this->name} {$this->name}--{$this->context}" : $this->name;
		}

		// Compatibility with core WP attributes.
		if ( method_exists( $this, $this->name ) ) {
			$method   = $this->name;
			$defaults = $this->$method( $defaults );
		}

		// Filter the default attributes.
		$defaults = apply_filters( "hybrid/attr/{$this->name}/defaults", $defaults, $this->context, $this );

		// Merge the attributes with the defaults.
		$this->attr = wp_parse_args( $this->input, $defaults );

		// Apply filters to the parsed attributes.
		$this->attr = apply_filters( 'hybrid/attr', $this->attr, $this->name, $this->context );
		$this->attr = apply_filters( "hybrid/attr/{$this->name}", $this->attr, $this->context );

		// Provide a filter hook for the class attribute directly. The
		// classes are split up into an array for easier filtering. Note
		// that theme authors should still utilize the core WP body,
		// post, and comment class filter hooks. This should only be
		// used for custom attributes.
		$hook = "hybrid/attr/{$this->name}/class";

		if ( isset( $this->attr['class'] ) && has_filter( $hook ) ) {

			$classes = apply_filters( $hook, explode( ' ', $this->attr['class'] ), $this->context );

			$this->attr['class'] = join( ' ', array_unique( $classes ) );
		}

		return $this->attr;
	}

	/**
	 * `<html>` element attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array   $attr
	 * @return array
	 */
	protected function html( $attr ) {

		$attr = [];

		$parts = wp_kses_hair( get_language_attributes(), [ 'http', 'https' ] );

		if ( $parts ) {

			foreach ( $parts as $part ) {

				$attr[ $part['name'] ] = $part['value'];
			}
		}

		return $attr;
	}

	/**
	 * `<body>` element attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array   $attr
	 * @return array
	 */
	protected function body( $attr ) {

		$class = isset( $attr['class'] ) && 'body' !== $attr['class'] ? $attr['class'] : '';

		$attr['class'] = join( ' ', get_body_class( $class ) );
		$attr['dir']   = is_rtl() ? 'rtl' : 'ltr';

		return $attr;
	}

	/**
	 * Post `<article>` element attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array   $attr
	 * @return array
	 */
	protected function post( $attr ) {

		$post  = isset( $this->data['post'] ) ? get_post( $this->data['post'] ) : get_post();
		$class = isset( $attr['class'] ) ? $attr['class'] : '';

		$attr['id']    = ! empty( $post ) ? sprintf( 'post-%d', $post->ID ) : 'post-0';
		$attr['class'] = join( ' ', get_post_class( $class, $post ) );

		return $attr;
	}

	/**
	 * Alias for `post()`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array   $attr
	 * @return array
	 */
	protected function entry( $attr ) {

		return $this->post( $attr );
	}

	/**
	 * Comment wrapper attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  array   $attr
	 * @return array
	 */
	protected function comment( $attr ) {

		$class = isset( $attr['class'] ) ? $attr['class'] : '';

		$attr['id']    = 'comment-' . get_comment_ID();
		$attr['class'] = join( ' ', get_comment_class( $class ) );

		return $attr;
	}
}
