<?php

namespace Hybrid\MediaMeta\Meta;

class FileSize extends Meta {

	protected $name = 'filesize';

	public function raw() {

		$filesize = isset( $this->meta['filesize'] ) ? $this->meta['filesize'] : '';

		if ( ! $filesize ) {
			$file = get_attached_file( $this->post_id );

			if ( file_exists( $file ) ) {
				$filesize = filesize( $file );
			}
		}

		return $filesize;
	}

	public function fetch() {

		$filesize = $this->raw();

		if ( $filesize ) {

			$filesize = size_format( strip_tags( $filesize ), 2 );
		}

		return $filesize;
	}
}
