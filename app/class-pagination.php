<?php
/**
 * Pagination class.
 *
 * This is a wrapper for the core WP `paginate_links()` class and is primarily
 * meant to replace `get_the_posts_pagination()`.  Unfortunately, core doesn't
 * give theme authors much flexibility for altering the markup and classes.
 * This class is meant to solve this issue.
 *
 * @package   ABC
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2018, Justin Tadlock
 * @link      https://themehybrid.com/themes/abc
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Pagination class.
 *
 * @since  1.0.0
 * @access public
 */
class Pagination {

	/**
	 * Array of items in the pagination list.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $items = [];

	/**
	 * Array of arguments that will be passed to `paginate_links()`.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $args = [];

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $args
	 * @return void
	 */
	public function __construct( $args = [] ) {

		$this->args = (array) $args + [
			'mid_size'           => 1,
			'screen_reader_text' => '',
			'container_tag'      => 'nav',
			'container_class'    => 'pagination',
			'title_tag'          => 'h2',
			'title_class'        => 'pagination__title',
			'title_text'         => '',
			'list_tag'           => 'ul',
			'list_class'         => 'pagination__items',
			'item_tag'           => 'li',
			'item_class'         => 'pagination__item pagination__item--%s',
			'anchor_class'       => 'pagination__anchor pagination__anchor--%s'
		];

		// Always want an array type so that we can build our own output.
		$this->args['type'] = 'array';
	}

	/**
	 * Sets up the array of items.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function get_items() {

		$links = paginate_links( $this->args );

		if ( ! $links ) {
			return;
		}

		foreach ( $links as $link ) {

			$item = [ 'type' => 'number' ];

			// Capture the element attributes and text.
			preg_match( "/<(?:a|span)(.+?)>(.+?)<\/(?:a|span)>/i", $link, $matches );

			if ( ! empty( $matches ) && isset( $matches[1] ) && isset( $matches[2] ) ) {

				// Get an array of the attributes.
				$attr = wp_kses_hair( trim( $matches[1] ), array( 'http', 'https' ) );

				if ( ! empty( $attr['class'] ) ) {

					$intersection = array_intersect(
						[ 'prev', 'next', 'current', 'dots' ],
						explode( ' ', $attr['class']['value'] )
					);

					if ( $intersection ) {

						$item['type'] = reset( $intersection );
					}
				}

				// If there's an HREF attribute, it means we have a link.
				if ( ! empty( $attr['href'] ) ) {

					$item['href'] = esc_url( $attr['href']['value'] );
				}

				$item['text'] = esc_html( $matches[2] );
			}

			$this->items[] = $item;
		}
	}

	/**
	 * Return the pagination output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	 public function fetch() {

		$this->get_items();

		$title = $list = $template = '';

		if ( $this->items ) {

			// If there's title text, format it.
			if ( $this->args['title_text'] ) {

				$title = sprintf(
					'<%1$s class="%2$s">%3$s</%1$s>',
					tag_escape( $this->args['title_tag'] ),
					esc_attr( $this->args['title_class'] ),
					esc_html( $this->args['title_text'] )
				);
			}

			// Loop through each of the items and format them into a list.
			foreach ( $this->items as $item ) {

				$list .= $this->format_item( $item );
			}

			$list = sprintf(
				'<%1$s class="%2$s">%3$s</%1$s>',
				tag_escape( $this->args['list_tag'] ),
				esc_attr( $this->args['list_class'] ),
				$list
			);

			// Format the HTML output.
			$template = sprintf(
				'<%1$s class="%2$s" role="navigation">%3$s%4$s</%1$s>',
				tag_escape( $this->args['container_tag'] ),
				esc_attr( $this->args['container_class'] ),
				$title,
				$list
			);
		}

		return apply_filters(
			app()->namespace . '/pagination',
			$template,
			$this->args
		);
	}

	/**
	 * Format an item's HTML output.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array   $item
	 * @return string
	 */
	private function format_item( $item ) {

		if ( isset( $item['href'] ) ) {

			$anchor = sprintf(
				'<a href="%s" class="%s">%s</a>',
				esc_url( $item['href'] ),
				esc_attr( sprintf( $this->args['anchor_class'], 'link' ) ),
				esc_html( $item['text'] )
			);
		} else {
			$anchor = sprintf(
				'<span class="%s">%s</span>',
				esc_attr( sprintf( $this->args['anchor_class'], $item['type'] ) ),
				esc_html( $item['text'] )
			);
		}

		return sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $this->args['item_tag'] ),
			esc_attr( sprintf( $this->args['item_class'], $item['type'] ) ),
			$anchor
		);
	}
}
