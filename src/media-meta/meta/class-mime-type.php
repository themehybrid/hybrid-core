<?php

namespace Hybrid\MediaMeta\Meta;

class MimeType extends Meta {

	protected $name = 'mime_type';

	public function raw() {

		$mime_type = get_post_mime_type( $this->post_id );

		if ( empty( $mime_type ) && ! empty( $this->meta['mime_type'] ) ) {

			$mime_type = $this->meta['mime_type'];
		}

		return $mime_type;
	}

	public function fetch() {

		$mime_type = $this->raw();

		return $mime_type ? esc_html( $mime_type ) : '';
	}
}
