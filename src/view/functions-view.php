<?php
/**
 * View template tags.
 *
 * Template functions related to views.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\View;

use Hybrid\Proxies\App;

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

	return App::resolve( 'view', compact( 'name', 'slugs', 'data' ) );
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
function render( $name, $slugs = [], $data = [] ) {

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
function fetch( $name, $slugs = [], $data = [] ) {

	return view( $name, $slugs, $data )->fetch();
}
