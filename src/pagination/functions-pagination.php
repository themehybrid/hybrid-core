<?php

namespace Hybrid\Pagination;

/**
 * Renders the pagination output.
 *
 * @since  5.0.0
 * @access public
 * @param  string $context
 * @param  array  $args
 * @return object
 */
function render( $context = 'posts', array $args = [] ) {

	( new Pagination( $context, $args ) )->make()->render();
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
function fetch( $context = 'posts', array $args = [] ) {

	return ( new Pagination( $context, $args ) )->make()->fetch();
}
