<?php
/**
 * Media meta factory class.
 *
 * This is a simple factory class for creating new media meta objects.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta;

/**
 * Media meta factory.
 *
 * @since  5.0.0
 * @access public
 */
class Factory {

	/**
	 * Creates and returns a new media meta object.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @param  int     $post_id
	 * @param  array   $meta
	 * @return object
	 */
	public static function make( $name, $post_id, $meta ) {

		// Gets the meta subnamespace with the trailing backslash.
		$namespace = __NAMESPACE__ . '\\Meta\\';

		// `filesize` is correct, but `file_size` matches up with the
		// other `file_*` meta names, so let's support both.
		if ( 'filesize' === $name ) {
			$name = 'file_size';
		}

		// Create the meta class name.
		$meta_class = str_replace( '_', '', $namespace . ucwords( $name, '_' ) );

		// If the class exists, use it. Otherwise, fallback to the base.
		$class = class_exists( $meta_class ) ? $meta_class : $namespace . 'Meta';

		return new $class( $post_id, [
			'name' => $name,
			'meta' => $meta
		] );
	}
}
