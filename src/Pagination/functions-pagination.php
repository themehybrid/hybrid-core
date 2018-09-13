<?php
/**
 * Pagination functions.
 *
 * Helper functions and template tags related to pagination.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\Pagination;

/**
 * Outputs the pagination output.
 *
 * @since  5.0.0
 * @access public
 * @param  string $context
 * @param  array  $args
 * @return object
 */
function display( $context = 'posts', array $args = [] ) {

	( new Pagination( $context, $args ) )->make()->display();
}

/**
 * Returns the pagination output.
 *
 * @since  5.0.0
 * @access public
 * @param  string $context
 * @param  array  $args
 * @return object
 */
function render( $context = 'posts', array $args = [] ) {

	return ( new Pagination( $context, $args ) )->make()->render();
}
