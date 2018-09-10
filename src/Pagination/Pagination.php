<?php
/**
 * Pagination class.
 *
 * This is a fork of the core WordPress `paginate_links()` function to give
 * theme authors full control over the output of their pagination. Unfortunately,
 * core doesn't give theme authors much flexibility for altering the markup and
 * classes. This class is meant to solve this issue.  It also standardizes the
 * pagination used for posts, singular (multi-page) posts, and comments.
 *
 * @package   ABC
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2018, Justin Tadlock
 * @link      https://themehybrid.com/themes/abc
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Pagination;

use Hybrid\Contracts\Pagination\Pagination as PaginationContract;

/**
 * Pagination class.
 *
 * @since  5.0.0
 * @access public
 */
class Pagination implements PaginationContract{

	/**
	 * The type of pagination to output.  `posts`, `comments`, and `singular`
	 * are the default types that are handled.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    string
	 */
	protected $context = 'posts';

	/**
	 * An array of the pagination items.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $items = [];

	/**
	 * The total number of pages.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $total = 0;

	/**
	 * The current page being viewed.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $current = 0;

	/**
	 * The number of items to show on the ends.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $end_size = 0;

	/**
	 * The number of items to show in the middle.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $mid_size = 0;

	/**
	 * Helper property for tracking the URL parts of the current page.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $url_parts = [];

	/**
	 * Helper for keeping track of whether to show dots instead of a number.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $dots = false;

	/**
	 * Create a new pagination object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $context
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $context = 'posts', $args = [] ) {

		$this->context = 'singular' === $context ? 'post' : $context;

		$defaults = [
			// Base arguments imported from `paginate_links()`. It's
			// best not to touch these unless rolling building out
			// something custom.
			'base'               => '',
			'format'             => '',
			'total'              => 0,
			'current'            => 0,
			'add_args'           => [],
			'add_fragment'       => '',

			// Customize the items that are shown.
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 1,
			'prev_next'          => true,

			// Custom text, content, and HTML.
			'prev_text'          => '',
			'next_text'          => '',
			'screen_reader_text' => '',
			'title_text'         => '',
			'before_page_number' => '',
			'after_page_number'  => '',

			// HTML tags.
			'container_tag'      => 'nav',
			'title_tag'          => 'h2',
			'list_tag'           => 'ul',
			'item_tag'           => 'li',

			// Attributes.
			'container_class'    => 'pagination pagination--%s',
			'title_class'        => 'pagination__title screen-reader-text',
			'list_class'         => 'pagination__items',
			'item_class'         => 'pagination__item pagination__item--%s',
			'anchor_class'       => 'pagination__anchor pagination__anchor--%s',
			'aria_current'       => 'page'
		];

		// Build the class method name for grabbing the default arguments
		// based on the current context.
		$method = "{$this->context}Args";

		// Merge defaults and contextual default args.
		$defaults = apply_filters(
			"hybrid/pagination/{$this->context}/defaults",
			array_merge(
				$defaults,
				method_exists( $this, $method ) ? $this->$method() : $this->postArgs()
			)
		);

		// Parse the args with the defaults.
		$this->args = apply_filters(
			"hybrid/pagination/{$this->context}/args",
			wp_parse_args( $args, $defaults )
		);

		// Make sure query args is an array.
		if ( ! is_array( $this->args['add_args'] ) ) {
			$this->args['add_args'] = [];
		}

		// Merge query vars found in the current page's URL into the
		// `add_args` array so that they get appended to the final URL.
		if ( isset( $this->url_parts[1] ) ) {

			// Find the format argument.
			$format       = explode( '?', str_replace( '%_%', $this->args['format'], $this->args['base'] ) );
			$format_query = isset( $format[1] ) ? $format[1] : '';
			wp_parse_str( $format_query, $format_args );

			// Find the query args of the requested URL.
			wp_parse_str( $this->url_parts[1], $url_query_args );

			// Remove the format argument from the array of query
			// arguments, to avoid overwriting custom format.
			foreach ( $format_args as $format_arg => $format_arg_value ) {
				unset( $url_query_args[ $format_arg ] );
			}

			// Merge original query args to `add_args`.
			$this->args['add_args'] = array_merge(
				$this->args['add_args'],
				urlencode_deep( $url_query_args )
			);
		}

		// Make sure that we have absolute integers.
		$this->total    = absint( $this->args['total']    );
		$this->current  = absint( $this->args['current']  );
		$this->end_size = absint( $this->args['end_size'] );
		$this->mid_size = absint( $this->args['mid_size'] );

		// The end size must be at least 1.
		if ( $this->end_size < 1 ) {
			$this->end_size = 1;
		}
	}

	/**
	 * Returns custom arguments for normal, posts pagination.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @global object  $wp_query
	 * @global object  $wp_rewrite
	 * @return array
	 */
	protected function postsArgs() {
		global $wp_query, $wp_rewrite;

		// Setting up default values based on the current URL.
		$pagenum_link    = html_entity_decode( get_pagenum_link() );
		$this->url_parts = explode( '?', $pagenum_link );

		// Get total number of pages.
		$total = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;

		// Get the current page.
		$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

		// Append the format placeholder to the base URL.
		$base = trailingslashit( $this->url_parts[0] ) . '%_%';

		// URL base depends on permalink settings.
		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		return [
			'base'    => $base,
			'format'  => $format,
			'total'   => $total,
			'current' => $current
		];
	}

	/**
	 * Returns custom arguments for singular post pagination.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @global int     $page
	 * @global int     $numpages
	 * @global bool    $more
	 * @global object  $wp_rewrite
	 * @return array
	 */
	protected function postArgs() {
		global $page, $numpages, $more, $wp_rewrite;

		// Split the current URL between the base and query string.
		$this->url_parts = explode( '?', html_entity_decode( get_permalink() ) );

		// Build the base without the query string.
		$base = trailingslashit( $this->url_parts[0] ) . '%_%';

		// Build URL format.
		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $base, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( '%#%' ) : '?page=%#%';

		return [
			'base'    => $base,
			'format'  => $format,
			'current' => ! $more && 1 === $page ? 0 : $page,
			'total'   => $numpages
		];
	}
	/**
	 * Returns custom arguments for comments pagination.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @global object  $wp_rewrite
	 * @return array
	 */
	protected function commentsArgs() {
		global $wp_rewrite;

		$base = add_query_arg( 'cpage', '%#%' );

		// Split the current URL between the base and query string.
		$this->url_parts = explode( '?', html_entity_decode( get_pagenum_link() ) );

		if ( $wp_rewrite->using_permalinks() ) {
			$base = user_trailingslashit( trailingslashit( get_permalink() ) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged' );
		}

		return [
			'base'         => $base,
			'format'       => '',
			'total'        => get_comment_pages_count(),
			'current'      => get_query_var( 'cpage' ) ?: 1,
			'add_fragment' => '#comments'
		];
	}

	/**
	 * Outputs the pagination output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function display() {

		echo $this->render();
	}

	/**
	 * Returns the pagination output.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return string
	 */
	public function render() {

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

			// Loop through each of the items and format each into
			// an HTML string.
			foreach ( $this->items as $item ) {

				$list .= $this->formatItem( $item );
			}

			// Format the list.
			$list = sprintf(
				'<%1$s class="%2$s">%3$s</%1$s>',
				tag_escape( $this->args['list_tag'] ),
				esc_attr( $this->args['list_class'] ),
				$list
			);

			// Format the nav wrapper.
			$template = sprintf(
				'<%1$s class="%2$s" role="navigation">%3$s%4$s</%1$s>',
				tag_escape( $this->args['container_tag'] ),
				esc_attr( sprintf( $this->args['container_class'], $this->context ) ),
				$title,
				$list
			);
		}

		return apply_filters( "hybrid/pagination/{$this->context}", $template, $this->args );
	}

	/**
	 * Builds the pagination `$items` array.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return PaginationContract
	 */
	public function make() {

		if ( 2 <= $this->total ) {

			$this->prevItem();

			for ( $n = 1; $n <= $this->total; $n++ ) {
				$this->pageItem( $n );
			}

			$this->nextItem();
		}

		return $this;
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
		$attr     = [];
		$esc_attr = '';

		// Add the anchor/span class attribute.
		$attr['class'] = sprintf(
			$this->args['anchor_class'],
			$is_link ? 'link' : $item['type']
		);

		// If this is a link, add the URL.
		if ( $is_link ) {
			$attr['href'] = $item['url'];
		}

		// If this is the current item, add the `aria-current` attribute.
		if ( 'current' === $item['type'] ) {
			$attr['aria-current'] = $this->args['aria_current'];
		}

		// Loop through the attributes and format them into a string.
		foreach ( $attr as $name => $value ) {

			$esc_attr .= sprintf(
				' %s="%s"',
				esc_html( $name ),
				'href' === $name ? esc_url( $value ) : esc_attr( $value )
			);
		}

		// Builds and formats the list item.
		return sprintf(
			'<%1$s class="%2$s"><%3$s %4$s>%5$s</%3$s></%1$s>',
			tag_escape( $this->args['item_tag'] ),
			esc_attr( sprintf( $this->args['item_class'], $item['type'] ) ),
			$is_link ? 'a' : 'span',
			trim( $esc_attr ),
			$item['content']
		);
	}

	/**
	 * Builds the previous item.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function prevItem() {

		if ( $this->args['prev_next'] && $this->current && 1 < $this->current ) {

			$this->items[] = [
				'type'    => 'prev',
				'url'     => $this->buildUrl( 2 == $this->current ? '' : $this->args['format'], $this->current - 1 ),
				'content' => $this->args['prev_text']
			];
		}
	}

	/**
	 * Builds the next item.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function nextItem() {

		if ( $this->args['prev_next'] && $this->current && $this->current < $this->total ) {

			$this->items[] = [
				'type'    => 'next',
				'url'     => $this->buildUrl( $this->args['format'], $this->current + 1 ),
				'content' => $this->args['next_text']
			];
		}
	}

	/**
	 * Builds the numeric page link, current item, and dots item.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function pageItem( $n ) {

		// If the current item we're building is for the current page
		// being viewed.
		if ( $n === $this->current ) {

			$this->items[] = [
				'type'    => 'current',
				'content' => $this->args['before_page_number'] . number_format_i18n( $n ) . $this->args['after_page_number']
			];

			$this->dots = true;

		// If showing a linked number or dots.
		} else {

			if ( $this->args['show_all'] || ( $n <= $this->end_size || ( $this->current && $n >= $this->current - $this->mid_size && $n <= $this->current + $this->mid_size ) || $n > $this->total - $this->end_size ) ) {

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

	/**
	 * Builds and formats a page link URL.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $format
	 * @param  int       $number
	 * @return string
	 */
	protected function buildUrl( $format, $number ) {

		$link = str_replace( '%_%', $format, $this->args['base'] );
		$link = str_replace( '%#%', $number, $link );

		// Adds any existing or new query vars to the URL.
		if ( $this->args['add_args'] ) {
			$link = add_query_arg( $this->args['add_args'], $link );
		}

		// Appends a fragment to the end of the URL.
		$link .= $this->args['add_fragment'];

		// Applies the core WP `paginate_links` filter hook.
		return apply_filters( 'paginate_links', $link );
	}
}
