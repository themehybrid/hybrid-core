<?php
/**
 * Framwork bootstrap process.
 *
 * This file bootstraps parts of the framework that can't be autoloaded. We
 * define any global constants here and load any additional functions files.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2021, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Define the directory path to the framework. This shouldn't need changing
 * unless doing something really out there or just for clarity.
 *
 * @deprecated 6.0
 * @deprecated Use $themeslug->instance( 'path', '/path/to/hybrid-core );
 */
if ( ! defined( 'HYBRID_DIR' ) ) {

	define( 'HYBRID_DIR', __DIR__ );
}

# Check if the framework has been bootstrapped. If not, load the bootstrap files
# and get the framework set up.
if ( ! defined( 'HYBRID_BOOTSTRAPPED' ) ) {

	require_once( 'bootstrap-functions.php' );

	define( 'HYBRID_BOOTSTRAPPED', true );
}
