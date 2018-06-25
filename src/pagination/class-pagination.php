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

namespace Hybrid\Pagination;

use Hybrid\Contracts\Pagination as PaginationContract;

/**
 * Pagination class.
 *
 * @since  5.0.0
 * @access public
 */
class Pagination implements PaginationContract{

	protected $items = [];
	protected $total = 0;
	protected $current = 0;
	protected $end_size = 0;
	protected $mid_size = 0;
	protected $dots = false;

	public function __construct( $args = [] ) {

		$this->args = $args;
	}

	public function render() {

		echo $this->fetch();
	}

	public function fetch() {

		$this->build();

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

				$list .= $this->formatItem( $item );
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

		return apply_filters( 'hybrid/pagination', $template, $this->args );
	}

	/**
	 * Format an item's HTML output.
	 *
	 * @since  5.0.0
	 * @access private
	 * @param  array   $item
	 * @return string
	 */
	private function formatItem( $item ) {

		$is_link  = isset( $item['url'] );
		$esc_attr = '';

		$attr = [
			'class' => sprintf( $this->args['anchor_class'], $is_link ? 'link' : $item['type'] )
		];

		if ( $is_link ) {
			$attr['href'] = $item['url'];
		}

		if ( 'current' === $item['type'] ) {
			$attr['aria-current'] = $this->args['aria_current'];
		}

		// We need to re-add attributes to the item.
		foreach ( $attr as $name => $value ) {

			$esc_attr .= sprintf(
				' %s="%s"',
				esc_html( $name ),
				'href' === $name ? esc_url( $value ) : esc_attr( $value )
			);
		}

		return sprintf(
			'<%1$s class="%2$s"><%3$s %4$s>%5$s</%3$s></%1$s>',
			tag_escape( $this->args['item_tag'] ),
			esc_attr( sprintf( $this->args['item_class'], $item['type'] ) ),
			$is_link ? 'a' : 'span',
			trim( $esc_attr ),
			$item['content']
		);
	}

	protected function build() {
		global $wp_query, $wp_rewrite;

		// Setting up default values based on the current URL.
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$url_parts    = explode( '?', $pagenum_link );

		// Get max pages and current page out of the current query, if available.
		$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

		// Append the format placeholder to the base URL.
		$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

		// URL base depends on permalink settings.
		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$defaults = array(
			'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
			'format'             => $format, // ?page=%#% : %#% is replaced by the page number
			'total'              => $total,
			'current'            => $current,
			'aria_current'       => 'page',
			'show_all'           => false,
			'prev_next'          => true,
			'prev_text'          => '',
			'next_text'          => '',
			'end_size'           => 1,
			'mid_size'           => 1,
			'add_args'           => [],
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',

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
		);

		$this->args = wp_parse_args( $this->args, $defaults );

		if ( ! is_array( $this->args['add_args'] ) ) {
			$this->args['add_args'] = [];
		}

		// Merge additional query vars found in the original URL into 'add_args' array.
		if ( isset( $url_parts[1] ) ) {

			// Find the format argument.
			$format       = explode( '?', str_replace( '%_%', $this->args['format'], $this->args['base'] ) );
			$format_query = isset( $format[1] ) ? $format[1] : '';
			wp_parse_str( $format_query, $format_args );

			// Find the query args of the requested URL.
			wp_parse_str( $url_parts[1], $url_query_args );

			// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
			foreach ( $format_args as $format_arg => $format_arg_value ) {
				unset( $url_query_args[ $format_arg ] );
			}

			$this->args['add_args'] = array_merge( $this->args['add_args'], urlencode_deep( $url_query_args ) );
		}

		// Who knows what else people pass in $args
		$this->total = (int) $this->args['total'];

		if ( $this->total < 2 ) {
			return;
		}

		$this->current = (int) $this->args['current'];

		$this->end_size = (int) $this->args['end_size']; // Out of bounds?  Make it the default.

		if ( $this->end_size < 1 ) {
			$this->end_size = 1;
		}

		$this->mid_size = (int) $this->args['mid_size'];

		if ( $this->mid_size < 0 ) {
			$this->mid_size = 2;
		}

		$this->prevItem();

		for ( $n = 1; $n <= $this->total; $n++ ) {
			$this->pageItem( $n );
		}

		$this->nextItem();
	}

	protected function prevItem() {

		if ( $this->args['prev_next'] && $this->current && 1 < $this->current ) {

			$this->items[] = [
				'type'    => 'prev',
				'url'     => $this->buildUrl( 2 == $this->current ? '' : $this->args['format'], $this->current - 1 ),
				'content' => $this->args['prev_text']
			];
		}
	}

	protected function nextItem() {

		if ( $this->args['prev_next'] && $this->current && $this->current < $this->total ) {

			$this->items[] = [
				'type'    => 'next',
				'url'     => $this->buildUrl( $this->args['format'], $this->current + 1 ),
				'content' => $this->args['next_text']
			];
		}
	}

	protected function pageItem( $n ) {

		if ( $n === $this->current ) {

			$this->items[] = [
				'type'    => 'current',
				'content' => $this->args['before_page_number'] . number_format_i18n( $n ) . $this->args['after_page_number']
			];

			$this->dots = true;
		} else {

			if ( $this->args['show_all'] || ( $n <= $this->end_size || ( $this->current && $n >= $this->current - $this->mid_size && $n <= $this->current + $this->mid_size ) || $n > $this->total - $this->end_size ) ) {

				$link = str_replace( '%_%', 1 == $n ? '' : $this->args['format'], $this->args['base'] );
				$link = str_replace( '%#%', $n, $link );

				if ( $this->args['add_args'] ) {
					$link = add_query_arg( $this->args['add_args'], $link );
				}

				$link .= $this->args['add_fragment'];

				$this->items[] = [
					'type'    => 'number',
					'url'     => $this->buildUrl( 1 == $n ? '' : $this->args['format'], $n ),
					'content' => $this->args['before_page_number'] . number_format_i18n( $n ) . $this->args['after_page_number']
				];

				$this->dots = true;

			} elseif ( $this->dots && ! $this->args['show_all'] ) {

				$this->items[] = [
					'type'    => 'dots',
					'content' => __( '&hellip;', 'hybrid-core' )
				];

				$this->dots = false;
			}
		}
	}

	protected function buildUrl( $format, $number ) {

		$link = str_replace( '%_%', $format, $this->args['base'] );

		$link = str_replace( '%#%', $number, $link );

		if ( $this->args['add_args'] ) {
			$link = add_query_arg( $this->args['add_args'], $link );
		}

		$link .= $this->args['add_fragment'];

		return apply_filters( 'paginate_links', $link );
	}
}
