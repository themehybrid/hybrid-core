<?php

namespace Hybrid\MediaMeta\Meta;

use Hybrid\Contracts\Fetchable;

class Meta implements Fetchable {

	protected $name = '';

	protected $post_id = 0;

	protected $meta = [];

	protected $sanitize_callback = 'esc_html';

	public function __construct( $post_id, $args = [] ) {

		if ( ! $this->name && isset( $args['name'] ) ) {
			$this->name = $args['name'];
		}

		$this->meta = $args['meta'];

	//	$this->name    = $name;
		$this->post_id = $post_id;
	//	$this->meta    = $meta;

		if ( $this->sanitize_callback ) {
			add_filter( "hybrid/media/meta/{$this->name}/sanitize", $this->sanitize_callback );
		}
	}

	public function raw() {

		$value = '';

		// If the property exists in the meta array.
		if ( isset( $this->meta[ $this->name ] ) ) {

			$value = $this->meta[ $this->name ];

		// If the property exists in the image meta array.
		} elseif ( isset( $this->meta['image_meta'] ) && isset( $this->meta['image_meta'][ $this->name ] ) ) {

			$value = $this->meta['image_meta'][ $this->name ];

		// If the property exists in the video's audio meta array.
		} elseif ( isset( $this->meta['audio'] ) && isset( $this->meta['audio'][ $this->name ] ) ) {

			$value = $this->meta['audio'][ $this->name ];
		}

		return apply_filters( "hybrid/media/meta/{$this->name}", $value );
	}

	public function fetch() {

		$value = $this->raw();

		return $value ? apply_filters( "hybrid/media/meta/{$this->name}/sanitize", $value ) : '';
	}
}
