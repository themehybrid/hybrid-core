<?php
/**
 * Class autoloader.
 *
 * This file holds the class autoloader for the framework so that we can load
 * classes on an as-needed basis.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

# Register our autoloader with PHP.
spl_autoload_register( function( $class ) {

	autoload( $class );
} );
