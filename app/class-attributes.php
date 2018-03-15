<?php
/**
 * Attributes class.
 *
 * This is an HTML attributes class system. The purpose is to provide
 * devs a system for adding filterable attributes.  This is sort of
 * like `body_class()`, `post_class()`, and `comment_class()` on
 * steroids. However, it can handle attributes for any elements.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2017, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Attributes class.
 *
 * @since  1.0.0
 * @access public
 */
class Attributes {

	/**
	 * The name/ID of the element (e.g., `sidebar`).
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $name = '';

	/**
	 * A specific context for the element (e.g., `primary`).
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	protected $context = '';

	/**
	 * Stored array of attributes.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $attr = [];

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
		$this->attr    = $attr;
	}

	/**
	 * Outputs an escaped string of attributes for use in HTML.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function render() {

		echo $this->fetch();
	}

	/**
	 * Returns an escaped string of attributes for use in HTML.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function fetch() {

		$this->filter();

		$html = '';

		foreach ( $this->attr as $name => $value ) {

			$html .= false !== $value ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );
		}

		return trim( $html );
	}

	/**
	 * Filtes the array of attributes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function filter() {

		$defaults = [
			'class' => $this->context ? "{$this->name} {$this->name}-{$this->context}" : $this->name
		];

		$filtered = apply_filters( app()->namespace . '/attr', $defaults, $this->name, $this->context );
		$filtered = apply_filters( app()->namespace . "/attr_{$this->name}", $filtered, $this->context );

		$this->attr = wp_parse_args( $this->attr, $filtered );

		foreach ( $this->attr as $name => $value ) {

			$hook = app()->namespace . "/attr_{$this->name}_{$name}";

			$hook = app()->namespace . "/attr_{$this->name}_class";

			// Provide a filter hook for the class attribute directly. The classes are
			// split up into an array for easier filtering. Note that theme authors
			// should still utilize the core WP body, post, and comment class filter
			// hooks. This should only be used for custom attributes.
			if ( 'class' === $name && has_filter( $hook ) ) {

				$this->attr[ $name ] = join( ' ', apply_filters( $hook, explode( ' ', $value ) ) );
			}
		}
	}
}
