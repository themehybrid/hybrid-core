<?php

namespace Hybrid\MediaMeta\Meta;

class FileType extends Meta {

	public function raw() {

		$file_type = '';

		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->post_id ), $matches ) ) {

			$file_type = $matches[1];
		}

		return $file_type;
	}

	public function fetch() {

		$type = $this->raw();

		return $type ? esc_html( strtoupper( $type ) ) : '';
	}
}
