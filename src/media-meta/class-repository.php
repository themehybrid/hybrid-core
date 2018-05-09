<?php
/**
 * Media metadata repository class.
 *
 * This class is for getting and storing media file metadata.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta;

use Hybrid\Contracts\MediaMeta;
use Hybrid\Tools\Collection;

/**
 * Media meta repository class.
 *
 * @since  5.0.0
 * @access public
 */
class Repository {

	/**
	 * The attachment post ID.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    int
	 */
	protected $post_id = 0;

	/**
	 * Array of metadata retrieved via `wp_get_attachment_metadata()`.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    array
	 */
	protected $meta = [];

	/**
	 * Collection of metadata.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @var    object
	 */
	protected $collection;

	/**
	 * Sets up the repository for the given post.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  int     $post_id
	 * @return void
	 */
	public function __construct( $post_id ) {

		$this->post_id = $post_id;

		$this->meta = wp_get_attachment_metadata( $this->post_id );

		$this->collection = new Collection();
	}

	/**
	 * Stores a meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @param  object  $value
	 * @return void
	 */
	public function set( $name, MediaMeta $value = null ) {

		$this->collection[ $name ] = $value;
	}

	/**
	 * Returns a meta value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @return object
	 */
	public function get( $name ) {

		if ( ! $this->has( $name ) ) {

			$this->set( $name, Factory::make( $name, $this->post_id, $this->meta ) );
		}

		return $this->collection[ $name ];
	}

	/**
	 * Checks if a meta value is stored.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function has( $name ) {

		return $this->collection->has( $name );
	}
}
