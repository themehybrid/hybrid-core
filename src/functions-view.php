<?php

namespace Hybrid;

use Hybrid\Common\Collection;
use Hybrid\Template\View;

/**
 * Returns a view object.
 *
 * @since  5.0.0
 * @access public
 * @param  string        $name
 * @param  array|string  $slugs
 * @param  array         $data
 * @return object
 */
function view( $name, $slugs = [], $data = [] ) {

	return new View( $name, $slugs, new Collection( $data ) );
}

/**
 * Outputs a view template.
 *
 * @since  5.0.0
 * @access public
 * @param  string        $name
 * @param  array|string  $slugs
 * @param  array         $data
 * @return void
 */
function render_view( $name, $slugs = [], $data = [] ) {

	view( $name, $slugs, $data )->render();
}

/**
 * Returns a view template as a string.
 *
 * @since  5.0.0
 * @access public
 * @param  string        $name
 * @param  array|string  $slugs
 * @param  array         $data
 * @return string
 */
function fetch_view( $name, $slugs = [], $data = [] ) {

	return view( $name, $slugs, $data )->fetch();
}
