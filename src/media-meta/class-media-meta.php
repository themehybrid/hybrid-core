<?php
/**
 * Media metadata class.
 *
 * This class is for getting and formatting attachment media file metadata. This
 * is for metadata about the actual file and not necessarily any post metadata.
 * Currently, only image, audio, and video files are handled.
 *
 * @package   Hybrid
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid\MediaMeta;

use Hybrid\Tools\Collection;

class MediaMeta {

	protected $post_id = 0;
	protected $meta = [];
	protected $collection;

	public function __construct( $post_id ) {

		$this->post_id = $post_id;

		$this->meta = wp_get_attachment_metadata( $this->post_id );

		$this->collection = new Collection();
	}

	public function get( $name ) {

		if ( ! $this->has( $name ) ) {

			$this->collection[ $name ] = Factory::make( $name, $this->post_id, $this->meta );
		}

		return $this->collection[ $name ];
	}

	public function has( $name ) {

		return $this->collection->has( $name );
	}
}
