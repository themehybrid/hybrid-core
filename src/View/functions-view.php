<?php
/**
 * View template tags.
 *
 * Template functions related to views.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2019, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\View;

use Hybrid\Contracts\View\View;
use Hybrid\Proxies\App;
use Hybrid\Tools\Collection;

/**
 * Returns a view object.
 *
 * @since  5.0.0
 * @access public
 * @param  string            $name
 * @param  array|string      $slugs
 * @param  array|Collection  $data
 * @return View
 */
function view( $name, $slugs = [], $data = [] ) {

	if ( ! $data instanceof Collection ) {
		$data = new Collection( $data );
	}

	return App::resolve( View::class, compact( 'name', 'slugs', 'data' ) );
}

/**
 * Outputs a view template.
 *
 * @since  5.0.0
 * @access public
 * @param  string            $name
 * @param  array|string      $slugs
 * @param  array|Collection  $data
 * @return void
 */
function display( $name, $slugs = [], $data = [] ) {

	view( $name, $slugs, $data )->display();
}

/**
 * Returns a view template as a string.
 *
 * @since  5.0.0
 * @access public
 * @param  string            $name
 * @param  array|string      $slugs
 * @param  array|Collection  $data
 * @return string
 */
function render( $name, $slugs = [], $data = [] ) {

	return view( $name, $slugs, $data )->render();
}
