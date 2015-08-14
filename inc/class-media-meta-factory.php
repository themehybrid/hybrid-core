<?php
/**
 * Media metadata factory class. This is a singleton factory class for creating and storing
 * `Hybrid_Media_Meta` objects.
 *
 * Theme authors need not access this class directly.  Instead, utilize the template tags in the
 * `/inc/template-media.php` file.
 *
 * @package    Hybrid
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Singleton factory class that registers and instantiates `Hybrid_Media_Meta` classes. Use the
 * `hybrid_media_factory()` function to get the instance.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
class Hybrid_Media_Meta_Factory {

	/**
	 * Array of media meta objects created via `Hybrid_Media_Meta`.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    array
	 */
	protected $media = array();

	/**
	 * Creates a new `Hybrid_Media_Meta` object and stores it in the `$media` array by
	 * post ID.
	 *
	 * @see    Hybrid_Media_Meta::__construct()
	 * @since  3.0.0
	 * @access protected
	 * @param  int       $post_id
	 */
	protected function create_media_meta( $post_id ) {

		$this->media[ $post_id ] = new Hybrid_Media_Meta( $post_id );
	}

	/**
	 * Gets a specific `Hybrid_Media_Meta` object by post (attachment) ID.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  int     $post_id
	 * @return object
	 */
	public function get_media_meta( $post_id ) {

		// If the media meta object doesn't exist, create it.
		if ( ! isset( $this->media[ $post_id ] ) )
			$this->create_media_meta( $post_id );

		return $this->media[ $post_id ];
	}

	/**
	 * Returns the instance of the `Hybrid_Media_Meta_Factory`.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new Hybrid_Media_Meta_Factory;

		return $instance;
	}
}
