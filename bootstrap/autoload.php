<?php
/**
 * Class autoloader.
 *
 * This file holds the autoloader.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Autoloader for the theme.  Looks in the `/app` folder for classes.
 * File names are prefixed with `class-` and are a lowercased version
 * of the class name.  Classes are broken up by uppercase letter.
 *
 * `ABC\MyClass`       = `/app/class-my-class.php`
 * `ABC\Admin\MyClass` = `/app/admin/class-my-class.php`
 *
 * @since  1.0.0
 * @access public
 * @param  string  $class
 * @return void
 */
spl_autoload_register( function( $class ) {

	$namespace = __NAMESPACE__ . '\\';

	// Bail if the class is not in our namespace.
	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	$file       = '';
	$new_pieces = [];

	// Remove the namespace.
	$class = str_replace( $namespace, '', $class );

	// Explode the full class name into an array of items by sub-namespace
	// and class name.
	$pieces = explode( '\\', $class );

	foreach ( $pieces as $piece ) {

		// Split pieces by uppercase letter.  Assume sub-namespaces and
		// classes are in "PascalCase".
		$pascal = preg_split( '/(?=[A-Z])/', $piece,  -1, PREG_SPLIT_NO_EMPTY );

		// Lowercase and hyphenate the word pieces within a string.
		$new_pieces[] = strtolower( join( '-', $pascal ) );
	}

	// Pop the last item off the array and re-add it with the `class-` prefix
	// and the `.php` file extension.  This is our class file.
	$new_pieces[] = sprintf( 'class-%s.php', array_pop( $new_pieces ) );

	// Join all the pieces together by a forward slash. These are directories.
	$file = join( DIRECTORY_SEPARATOR, $new_pieces );

	// Get the file from the `/app` folder.
	$file = require_once( HYBRID_DIR . "app/{$file}" );

	// Include the file only if it exists.
	if ( file_exists( $file ) ) {

		include( $file );
	}
} );
